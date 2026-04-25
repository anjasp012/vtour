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
    public function index(Request $request, Infospot $infospot)
    {
        $query = $infospot->assets();
        
        if ($request->has('product_id')) {
            $query->where('infospot_product_id', $request->product_id);
        }

        $assets = $query->with('product')->get()->map(fn ($a) => [
            'id'        => $a->id,
            'file_type' => $a->file_type,
            'file_path' => $a->file_path,
            'filename'  => basename($a->file_path),
            'label'          => $a->label,
            'url'            => asset('storage/' . $a->file_path),
            'sort_order'     => $a->sort_order,
            'product'        => $a->product ? ['id' => $a->product->id, 'name' => $a->product->name] : null,
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
            'assets.*.file'       => 'required|file', // 100MB max
            'assets.*.file_type'  => 'required|in:3d,2d,video',
            'assets.*.label'      => 'nullable|string|max:255',
            'assets.*.infospot_product_id' => 'nullable|integer|exists:infospot_products,id',
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

            $folder = $type === '3d' ? 'infospots/models' : ($type === 'video' ? 'infospots/videos' : 'infospots/images');
            $path   = $file->store($folder, 'public');

            $infospot->assets()->create([
                'file_type'      => $type,
                'file_path'      => $path,
                'label'          => $label,
                'infospot_product_id' => $meta['infospot_product_id'] ?? null,
                'sort_order'     => ++$lastOrder,
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

    /**
     * Reorder assets (update sort_order in bulk).
     * Expects JSON body: { "order": [{"id": 1}, {"id": 3}, {"id": 2}] }
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order'    => 'required|array',
            'order.*.id' => 'required|integer|exists:infospot_assets,id',
        ]);

        foreach ($request->input('order') as $position => $item) {
            InfospotAsset::where('id', $item['id'])->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
