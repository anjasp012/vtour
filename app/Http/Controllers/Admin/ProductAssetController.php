<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductAsset;
use Illuminate\Support\Facades\Storage;

class ProductAssetController extends Controller
{
    /**
     * List assets for a given product (JSON for AJAX).
     */
    public function index(Request $request, Product $product)
    {
        $assets = $product->assets()->get()->map(fn ($a) => [
            'id'        => $a->id,
            'file_type' => $a->file_type,
            'file_path' => $a->file_path,
            'filename'  => basename($a->file_path),
            'label'          => $a->label,
            'url'            => asset('storage/' . $a->file_path),
            'sort_order'     => $a->sort_order,
        ]);

        return response()->json(['assets' => $assets]);
    }

    /**
     * Upload one or more assets linked to a product.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'assets'              => 'required|array|min:1',
            'assets.*.file'       => 'required|file', 
            'assets.*.file_type'  => 'required|in:3d,2d,video',
            'assets.*.label'      => 'nullable|string|max:255',
        ]);

        $lastOrder = $product->assets()->max('sort_order') ?? -1;

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

            $product->assets()->create([
                'file_type'  => $type,
                'file_path'  => $path,
                'label'      => $label,
                'sort_order' => ++$lastOrder,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Uploaded successfully.']);
    }

    /**
     * Delete a single asset.
     */
    public function destroy(Request $request, ProductAsset $asset)
    {
        if (Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reorder assets (update sort_order in bulk).
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order'    => 'required|array',
            'order.*.id' => 'required|integer|exists:product_assets,id',
        ]);

        foreach ($request->input('order') as $position => $item) {
            ProductAsset::where('id', $item['id'])->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
