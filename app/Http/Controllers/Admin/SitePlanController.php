<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\SitePlan;
use App\Models\SitePlanHotspot;
use App\Models\Scene;
use Illuminate\Support\Facades\Storage;

class SitePlanController extends Controller
{
    public function index(Tour $tour)
    {
        $sitePlans = $tour->sitePlans;
        return view('admin.site_plans.index', compact('tour', 'sitePlans'));
    }

    public function create(Tour $tour)
    {
        return view('admin.site_plans.create', compact('tour'));
    }

    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        $imagePath = $request->file('image')->store('site_plans', 'public');

        $tour->sitePlans()->create([
            'name' => $validated['name'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.tours.site-plans.index', $tour)->with('success', 'Site plan created successfully.');
    }

    public function show(SitePlan $sitePlan)
    {
        $sitePlan->load('hotspots.scene');
        $scenes = $sitePlan->tour->scenes;
        return view('admin.site_plans.show', compact('sitePlan', 'scenes'));
    }

    public function edit(SitePlan $sitePlan)
    {
        return view('admin.site_plans.edit', compact('sitePlan'));
    }

    public function update(Request $request, SitePlan $sitePlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $data = ['name' => $validated['name']];

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($sitePlan->image_path)) {
                Storage::disk('public')->delete($sitePlan->image_path);
            }
            $data['image_path'] = $request->file('image')->store('site_plans', 'public');
        }

        $sitePlan->update($data);

        return redirect()->route('admin.tours.site-plans.index', $sitePlan->tour)->with('success', 'Site plan updated successfully.');
    }

    public function destroy(SitePlan $sitePlan)
    {
        if (Storage::disk('public')->exists($sitePlan->image_path)) {
            Storage::disk('public')->delete($sitePlan->image_path);
        }
        $tour = $sitePlan->tour;
        $sitePlan->delete();

        return redirect()->route('admin.tours.site-plans.index', $tour)->with('success', 'Site plan deleted successfully.');
    }

    public function saveHotspots(Request $request, SitePlan $sitePlan)
    {
        $request->validate([
            'hotspots' => 'required|array',
            'hotspots.*.scene_id' => 'required|exists:scenes,id',
            'hotspots.*.x' => 'required|numeric',
            'hotspots.*.y' => 'required|numeric',
        ]);

        $sitePlan->hotspots()->delete();

        foreach ($request->hotspots as $hotspot) {
            $sitePlan->hotspots()->create([
                'scene_id' => $hotspot['scene_id'],
                'x' => $hotspot['x'],
                'y' => $hotspot['y'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Hotspots saved successfully.']);
    }
}
