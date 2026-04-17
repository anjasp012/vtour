<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SceneController as AdminSceneController;
use App\Http\Controllers\Admin\InfospotController as AdminInfospotController;

// Frontend route for Virtual Tour
Route::get('/', [TourController::class, 'index'])->name('tour.show');

// Admin CMS group
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Scene CRUD
    Route::resource('scenes', AdminSceneController::class);
    // Nested resources for infospots within a scene
    Route::resource('scenes.infospots', AdminInfospotController::class)->shallow();
});
