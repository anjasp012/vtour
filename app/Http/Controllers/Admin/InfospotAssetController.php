<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Infospot;
use App\Models\InfospotAsset;
use Illuminate\Support\Facades\Storage;

class InfospotAssetController extends Controller
{
    /**
     * Upload one or more assets linked to an infospot.
     */
    public function store(Request $request, Infospot $infospot)
    {
        $request->validate([
            'assets'            => 'required|array|min:1',
            'assets.*.file'     => 'required|file|max:51200', // 50MB max
            'assets.*.file_type'=> 'required|in:3d,2d',
            'assets.*.label'    => 'nullable|string|max:255',
        ]);

        $lastOrder = $infospot->assets()->max('sort_order') ?? -1;

        foreach ($request->input('assets', []) as $index => $meta) {
            $fileKey = "assets.{$index}.file";
            if (!$request->hasFile($fileKey)) {
                continue;
            }

            $file     = $request->file($fileKey);
            $type     = $meta['file_type'];
            $label    = $meta['label'] ?? null;

            // Store in appropriate subfolder
            $folder = $type === '3d' ? 'infospots/models' : 'infospots/images';
            $path   = $file->store($folder, 'public');

            $infospot->assets()->create([
                'file_type'  => $type,
                'file_path'  => $path,
                'label'      => $label,
                'sort_order' => ++$lastOrder,
            ]);
        }

        return redirect()
            ->route('admin.infospots.edit', $infospot)
            ->with('success', 'Asset(s) uploaded successfully.');
    }

    /**
     * Delete a single asset.
     */
    public function destroy(InfospotAsset $asset)
    {
        $infospotId = $asset->infospot_id;

        // Remove from storage
        if (Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return redirect()
            ->route('admin.infospots.edit', $infospotId)
            ->with('success', 'Asset deleted.');
    }
}
