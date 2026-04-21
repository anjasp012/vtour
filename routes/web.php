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
    Route::post('scenes/{scene}/add-view', [AdminSceneController::class, 'addView'])->name('scenes.add-view');
    // Nested resources for infospots within a scene view
    Route::put('views/{sceneView}', [AdminSceneController::class, 'updateView'])->name('views.update');
    Route::resource('views.infospots', AdminInfospotController::class)->shallow();
});
