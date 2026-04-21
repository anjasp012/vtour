<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;

class TourController extends Controller
{
    public function index()
    {
        $tour = Tour::with(['scenes.infospots.targetScene'])->first();
        
        if (!$tour) {
            return view('welcome');
        }

        return view('tour.index', [
            'tour' => $tour
        ]);
    }
}
