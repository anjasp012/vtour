<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scene;
use App\Models\Infospot;
use Illuminate\Support\Facades\Storage;

class InfospotController extends Controller
{
    public function create(Scene $scene)
    {
        $hasTargetScenes = $scene->tour->scenes()->where('id', '!=', $scene->id)->get();
        return view('admin.infospots.create', compact('scene', 'hasTargetScenes'));
    }

    public function store(Request $request, Scene $scene)
    {
        $validated = $request->validate([
            'type' => 'required|in:info,nav',
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
            'position_z' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'content_id' => 'nullable|string',
            'content_en' => 'nullable|string',
            'target_scene_id' => 'nullable|exists:scenes,id',
            'is_perspective' => 'boolean',
            'rotation_x' => 'nullable|numeric',
            'rotation_y' => 'nullable|numeric',
            'rotation_z' => 'nullable|numeric',
            'scale_x' => 'nullable|numeric',
            'scale_y' => 'nullable|numeric',
            'model_file' => 'nullable|file' // 20MB limit
        ]);

        if ($request->hasFile('model_file')) {
            $validated['model_path'] = $request->file('model_file')->store('infospots/models', 'public');
        }

        // Remove file object from array before database insertion
        unset($validated['model_file']);

        $scene->infospots()->create($validated);

        return redirect()->route('admin.scenes.show', $scene)->with('success', 'Infospot added successfully.');
    }

    public function edit(Infospot $infospot)
    {
        $infospot->load('assets');
        $scene = $infospot->scene;
        $hasTargetScenes = $scene->tour->scenes()->where('id', '!=', $scene->id)->get();
        return view('admin.infospots.edit', compact('infospot', 'scene', 'hasTargetScenes'));
    }

    public function update(Request $request, Infospot $infospot)
    {
        $validated = $request->validate([
            'type' => 'required|in:info,nav',
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
            'position_z' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'content_id' => 'nullable|string',
            'content_en' => 'nullable|string',
            'target_scene_id' => 'nullable|exists:scenes,id',
            'is_perspective' => 'boolean',
            'rotation_x' => 'nullable|numeric',
            'rotation_y' => 'nullable|numeric',
            'rotation_z' => 'nullable|numeric',
            'scale_x' => 'nullable|numeric',
            'scale_y' => 'nullable|numeric',
            'model_file' => 'nullable|file|mimes:glb|max:20480'
        ]);

        if ($request->hasFile('model_file')) {
            // Delete old model if exists
            if ($infospot->model_path && Storage::disk('public')->exists($infospot->model_path)) {
                Storage::disk('public')->delete($infospot->model_path);
            }
            $validated['model_path'] = $request->file('model_file')->store('infospots/models', 'public');
        }

        // Remove file object from array before database update
        unset($validated['model_file']);

        $infospot->update($validated);

        return redirect()->route('admin.scenes.show', $infospot->scene_id)->with('success', 'Infospot updated successfully.');
    }

    public function destroy(Infospot $infospot)
    {
        $sceneId = $infospot->scene_id;
        $infospot->delete();

        return redirect()->route('admin.scenes.show', $sceneId)->with('success', 'Infospot deleted successfully.');
    }
}
