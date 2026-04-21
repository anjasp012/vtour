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
     * List assets for a given infospot (JSON for AJAX).
     */
    public function index(Infospot $infospot)
    {
        $assets = $infospot->assets()->get()->map(fn ($a) => [
            'id'        => $a->id,
            'file_type' => $a->file_type,
            'file_path' => $a->file_path,
            'filename'  => basename($a->file_path),
            'label'     => $a->label,
            'url'       => asset('storage/' . $a->file_path),
            'sort_order'=> $a->sort_order,
        ]);

        return response()->json(['assets' => $assets]);
    }

    /**
     * Upload one or more assets linked to an infospot.
     * Accepts JSON or multipart/form-data (AJAX-friendly).
     */
    public function store(Request $request, Infospot $infospot)
    {
        $request->validate([
            'assets'              => 'required|array|min:1',
            'assets.*.file'       => 'required|file|max:102400', // 100MB max
            'assets.*.file_type'  => 'required|in:3d,2d',
            'assets.*.label'      => 'nullable|string|max:255',
        ]);

        $lastOrder = $infospot->assets()->max('sort_order') ?? -1;

        foreach ($request->input('assets', []) as $index => $meta) {
            $fileKey = "assets.{$index}.file";
            if (!$request->hasFile($fileKey)) {
                continue;
            }

            $file   = $request->file($fileKey);
            $type   = $meta['file_type'];
            $label  = $meta['label'] ?? null;

            $folder = $type === '3d' ? 'infospots/models' : 'infospots/images';
            $path   = $file->store($folder, 'public');

            $infospot->assets()->create([
                'file_type'  => $type,
                'file_path'  => $path,
                'label'      => $label,
                'sort_order' => ++$lastOrder,
            ]);
        }

        // Support both AJAX JSON and normal form redirect
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Uploaded successfully.']);
        }

        return redirect()
            ->route('admin.infospots.edit', $infospot)
            ->with('success', 'Asset(s) uploaded successfully.');
    }

    /**
     * Delete a single asset.
     */
    public function destroy(Request $request, InfospotAsset $asset)
    {
        $infospotId = $asset->infospot_id;

        if (Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.infospots.edit', $infospotId)
            ->with('success', 'Asset deleted.');
    }
}
