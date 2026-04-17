<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::latest()->get();
        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        return view('admin.tours.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Tour::create($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Tour created successfully.');
    }

    public function show(Tour $tour)
    {
        $tour->load('scenes');
        return view('admin.tours.show', compact('tour'));
    }

    public function edit(Tour $tour)
    {
        return view('admin.tours.edit', compact('tour'));
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $tour->update($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Tour updated successfully.');
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Tour deleted successfully.');
    }
}
