<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Infospot;
use App\Models\InfospotProduct;

class InfospotProductController extends Controller
{
    public function index(Infospot $infospot)
    {
        $products = $infospot->products()->with('assets')->get();
        return response()->json(['products' => $products]);
    }

    public function store(Request $request, Infospot $infospot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'researcher' => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        $lastOrder = $infospot->products()->max('sort_order') ?? -1;
        $validated['sort_order'] = ++$lastOrder;

        $product = $infospot->products()->create($validated);

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function update(Request $request, InfospotProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'researcher' => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function destroy(InfospotProduct $product)
    {
        $product->delete();
        return response()->json(['success' => true]);
    }
}
