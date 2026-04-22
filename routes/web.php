<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SceneController as AdminSceneController;
use App\Http\Controllers\Admin\InfospotController as AdminInfospotController;
use App\Http\Controllers\Admin\InfospotAssetController as AdminInfospotAssetController;

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
    Route::post('scenes/{scene}/lock-view', [AdminSceneController::class, 'lockView'])
        ->name('scenes.lockView');
    // Nested resources for infospots within a scene
    Route::resource('scenes.infospots', AdminInfospotController::class)->shallow();

    // Infospot multi-file assets
    Route::get('infospots/{infospot}/assets', [AdminInfospotAssetController::class, 'index'])
        ->name('infospots.assets.index');
    Route::post('infospots/{infospot}/assets', [AdminInfospotAssetController::class, 'store'])
        ->name('infospots.assets.store');
    Route::delete('infospot-assets/{asset}', [AdminInfospotAssetController::class, 'destroy'])
        ->name('infospot-assets.destroy');
    Route::post('infospot-assets/reorder', [AdminInfospotAssetController::class, 'reorder'])
        ->name('infospot-assets.reorder');
});
