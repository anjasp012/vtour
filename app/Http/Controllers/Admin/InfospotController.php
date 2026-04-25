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
            'type' => 'required|in:info,nav,3d,image',
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
            'model_file' => 'nullable|file',
            'marker_image' => 'nullable|image',
            'is_multi' => 'boolean',
            'product_desc_id' => 'nullable|string',
            'product_desc_en' => 'nullable|string',
            'product_researcher' => 'nullable|string',
            'product_contact' => 'nullable|string'
        ]);

        if ($request->hasFile('model_file')) {
            $validated['model_path'] = $request->file('model_file')->store('infospots/models', 'public');
        } elseif ($request->hasFile('marker_image')) {
            $validated['model_path'] = $request->file('marker_image')->store('infospots/markers', 'public');
        }

        // Remove objects/fields not in infospots table before database insertion
        unset($validated['model_file']);
        unset($validated['marker_image']);
        unset($validated['product_desc_id']);
        unset($validated['product_desc_en']);
        unset($validated['product_researcher']);
        unset($validated['product_contact']);

        // Handle checkbox/hidden defaults
        $validated['is_perspective'] = $request->has('is_perspective');
        $isMulti = $request->boolean('is_multi');
        $validated['is_multi'] = $isMulti;

        $infospot = $scene->infospots()->create($validated);

        // If Single Product, create the default product
        if (!$isMulti) {
            $infospot->products()->create([
                'name' => $infospot->title ?? 'Default Product',
                'description_id' => $request->product_desc_id,
                'description_en' => $request->product_desc_en,
                'researcher' => $request->product_researcher,
                'contact_person' => $request->product_contact,
            ]);
        }

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
            'type' => 'required|in:info,nav,3d,image',
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
            'model_file' => 'nullable|file',
            'marker_image' => 'nullable|image',
            'is_multi' => 'boolean',
            'product_desc_id' => 'nullable|string',
            'product_desc_en' => 'nullable|string',
            'product_researcher' => 'nullable|string',
            'product_contact' => 'nullable|string'
        ]);

        if ($request->hasFile('model_file')) {
            // Delete old model if exists
            if ($infospot->model_path && Storage::disk('public')->exists($infospot->model_path)) {
                Storage::disk('public')->delete($infospot->model_path);
            }
            $validated['model_path'] = $request->file('model_file')->store('infospots/models', 'public');
        } elseif ($request->hasFile('marker_image')) {
            // Delete old model/marker if exists
            if ($infospot->model_path && Storage::disk('public')->exists($infospot->model_path)) {
                Storage::disk('public')->delete($infospot->model_path);
            }
            $validated['model_path'] = $request->file('marker_image')->store('infospots/markers', 'public');
        }

        // If type is switched to icon/nav, clear the old model_path
        if (in_array($validated['type'], ['info', 'nav'])) {
            if ($infospot->model_path && Storage::disk('public')->exists($infospot->model_path)) {
                Storage::disk('public')->delete($infospot->model_path);
            }
            $validated['model_path'] = null;
        }

        // Handle checkbox (if unchecked, and not in validated array)
        $validated['is_perspective'] = $request->has('is_perspective');
        $isMulti = $request->boolean('is_multi');
        $validated['is_multi'] = $isMulti;

        // Remove objects/fields not in infospots table before database update
        unset($validated['model_file']);
        unset($validated['marker_image']);
        unset($validated['product_desc_id']);
        unset($validated['product_desc_en']);
        unset($validated['product_researcher']);
        unset($validated['product_contact']);

        $infospot->update($validated);

        // If Single Product, sync the default product
        if (!$isMulti) {
            $product = $infospot->products()->first();
            if ($product) {
                $product->update([
                    'name' => $infospot->title ?? 'Default Product',
                    'description_id' => $request->product_desc_id,
                    'description_en' => $request->product_desc_en,
                    'researcher' => $request->product_researcher,
                    'contact_person' => $request->product_contact,
                ]);
            } else {
                $infospot->products()->create([
                    'name' => $infospot->title ?? 'Default Product',
                    'description_id' => $request->product_desc_id,
                    'description_en' => $request->product_desc_en,
                    'researcher' => $request->product_researcher,
                    'contact_person' => $request->product_contact,
                ]);
            }
        }

        return redirect()->route('admin.scenes.show', $infospot->scene_id)->with('success', 'Infospot updated successfully.');
    }

    public function destroy(Infospot $infospot)
    {
        $sceneId = $infospot->scene_id;
        $infospot->delete();

        return redirect()->route('admin.scenes.show', $sceneId)->with('success', 'Infospot deleted successfully.');
    }
}
