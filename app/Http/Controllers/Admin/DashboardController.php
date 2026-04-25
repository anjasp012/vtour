<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scene;
use App\Models\Infospot;

class DashboardController extends Controller
{
    public function index()
    {
        $totalScenes = Scene::count();
        $totalHotspots = Infospot::count();
        $recentScenes = Scene::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalScenes', 'totalHotspots', 'recentScenes'));
    }
}
