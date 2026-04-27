<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scene;
use App\Models\SitePlan;

class TourController extends Controller
{
    public function index(Request $request)
    {
        // Coming Soon Logic
        $isComingSoon = \App\Models\Setting::where('key', 'coming_soon')->value('value') === 'true';

        // One-time bypass check
        $hasBypass = $request->session()->pull('preview_bypass');

        // Bypass if authenticated or has one-time bypass token
        if ($isComingSoon && !auth()->check() && !$hasBypass) {
            return view('coming_soon');
        }

        $scenes = Scene::with([
            'infospots.targetScene',
            'infospots.products.assets'
        ])->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        $sitePlans = SitePlan::with('hotspots.scene')->get();

        if ($scenes->isEmpty()) {
            return redirect()->route('login');
        }

        return view('tour.index', [
            'scenes' => $scenes,
            'sitePlans' => $sitePlans,
            'tourName' => 'Rumah Inovasi Indonesia'
        ]);
    }
}
