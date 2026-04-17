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
        $tour->load('scenes');
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
            'image' => 'required|image|mimes:jpeg,png,jpg', // 10MB max
            'is_start_scene' => 'boolean',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $tour = $this->getTour();
        $imagePath = $request->file('image')->store('scenes/images', 'public');

        if ($request->has('is_start_scene') && $request->is_start_scene) {
            $tour->scenes()->update(['is_start_scene' => false]);
        }

        $newScene = $tour->scenes()->create([
            'name' => $validated['name'],
            'image_path' => $imagePath,
            'is_start_scene' => $request->has('is_start_scene') ? true : false,
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Scene created successfully.',
                'scene' => $newScene
            ]);
        }

        return redirect()->route('admin.scenes.index')->with('success', 'Scene added successfully.');
    }

    public function show(Scene $scene)
    {
        $scene->load('infospots.targetScene');
        $hasTargetScenes = Scene::where('id', '!=', $scene->id)->get();
        return view('admin.scenes.show', compact('scene', 'hasTargetScenes'));
    }

    public function edit(Scene $scene)
    {
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

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($scene->image_path)) {
                Storage::disk('public')->delete($scene->image_path);
            }
            $data['image_path'] = $request->file('image')->store('scenes/images', 'public');
        }

        $scene->update($data);

        return redirect()->route('admin.scenes.index')->with('success', 'Scene updated successfully.');
    }

    public function destroy(Scene $scene)
    {
        if (Storage::disk('public')->exists($scene->image_path)) {
            Storage::disk('public')->delete($scene->image_path);
        }
        
        $scene->delete();

        return redirect()->route('admin.scenes.index')->with('success', 'Scene deleted successfully.');
    }
}
