<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Scene;
use App\Models\Infospot;

class DashboardController extends Controller
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
        $totalScenes = $tour->scenes()->count();
        $sceneIds = $tour->scenes()->pluck('id');
        $viewIds = \App\Models\SceneView::whereIn('scene_id', $sceneIds)->pluck('id');
        $totalHotspots = Infospot::whereIn('scene_view_id', $viewIds)->count();
        
        $recentScenes = $tour->scenes()->latest()->take(5)->get();

        return view('admin.dashboard', compact('tour', 'totalScenes', 'totalHotspots', 'recentScenes'));
    }
}
