<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scene;
use App\Models\SitePlan;

class TourController extends Controller
{
    public function index()
    {
        $scenes = Scene::with([
            'infospots.targetScene',
            'infospots.products.assets'
        ])->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        $sitePlans = SitePlan::with('hotspots.scene')->get();
        
        if ($scenes->isEmpty()) {
            return view('welcome');
        }

        return view('tour.index', [
            'scenes' => $scenes,
            'sitePlans' => $sitePlans,
            'tourName' => 'Rumah Inovasi Indonesia'
        ]);
    }
}
