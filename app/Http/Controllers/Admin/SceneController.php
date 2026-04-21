<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Scene;
use Illuminate\Support\Facades\Storage;

class SceneController extends Controller
{
    private function getTour()
    {
        return Tour::firstOrCreate(
            ['id' => 1],
            ['name' => 'Main Virtual Tour', 'description' => 'Automatically generated primary tour.']
        );
    }

    public function index()
    {
        $tour = $this->getTour();
        $tour->load('scenes.primaryView');
        return view('admin.scenes.index', compact('tour'));
    }

    public function create()
    {
        return view('admin.scenes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'images.*' => 'required|image|mimes:jpeg,png,jpg',
            'is_start_scene' => 'boolean',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $tour = $this->getTour();

        if ($request->has('is_start_scene') && $request->is_start_scene) {
            $tour->scenes()->update(['is_start_scene' => false]);
        }

        $newScene = $tour->scenes()->create([
            'name' => $validated['name'],
            'is_start_scene' => $request->has('is_start_scene') ? true : false,
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $imagePath = $file->store('scenes/images', 'public');
                $newScene->views()->create([
                    'name' => $index == 0 ? 'Main View' : 'View ' . ($index + 1),
                    'image_path' => $imagePath,
                    'is_primary' => $index == 0,
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Scene created successfully.',
                'scene' => $newScene->load('primaryView')
            ]);
        }

        return redirect()->route('admin.scenes.index')->with('success', 'Scene added successfully.');
    }

    public function show(Scene $scene)
    {
        $scene->load(['views.infospots.targetScene', 'primaryView']);
        $hasTargetScenes = Scene::where('id', '!=', $scene->id)->get();
        return view('admin.scenes.show', compact('scene', 'hasTargetScenes'));
    }

    public function edit(Scene $scene)
    {
        $scene->load('primaryView');
        return view('admin.scenes.edit', compact('scene'));
    }

    public function update(Request $request, Scene $scene)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'is_start_scene' => 'boolean',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        if ($request->has('is_start_scene') && $request->is_start_scene) {
            $scene->tour->scenes()->update(['is_start_scene' => false]);
        }

        $data = [
            'name' => $validated['name'],
            'is_start_scene' => $request->has('is_start_scene') ? true : false,
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
        ];

        $scene->update($data);

        if ($request->hasFile('image')) {
            $primaryView = $scene->primaryView;
            if ($primaryView) {
                if (Storage::disk('public')->exists($primaryView->image_path)) {
                    Storage::disk('public')->delete($primaryView->image_path);
                }
                $primaryView->update(['image_path' => $request->file('image')->store('scenes/images', 'public')]);
            }
        }

        return redirect()->route('admin.scenes.index')->with('success', 'Scene updated successfully.');
    }

    public function addView(Request $request, Scene $scene)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg'
        ]);

        $imagePath = $request->file('image')->store('scenes/images', 'public');

        $scene->views()->create([
            'name' => $request->name,
            'image_path' => $imagePath,
            'is_primary' => false
        ]);

        return back()->with('success', 'View added successfully.');
    }

    public function destroy(Scene $scene)
    {
        foreach ($scene->views as $view) {
            if (Storage::disk('public')->exists($view->image_path)) {
                Storage::disk('public')->delete($view->image_path);
            }
        }
        
        $scene->delete();

        return redirect()->route('admin.scenes.index')->with('success', 'Scene deleted successfully.');
    }
}
