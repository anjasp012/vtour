<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SceneController as AdminSceneController;
use App\Http\Controllers\Admin\SitePlanController as AdminSitePlanController;
use App\Http\Controllers\Admin\InfospotController as AdminInfospotController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductAssetController as AdminProductAssetController;

// Frontend route for Virtual Tour
Route::get('/', [TourController::class, 'index'])->name('tour.show');

// Admin CMS group
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Site Plans
    Route::resource('site-plans', AdminSitePlanController::class);
    Route::post('site-plans/{site_plan}/hotspots', [AdminSitePlanController::class, 'saveHotspots'])
        ->name('site-plans.hotspots.save');

    // Admin Scene CRUD
    Route::post('scenes/reorder', [AdminSceneController::class, 'reorder'])->name('scenes.reorder');
    Route::resource('scenes', AdminSceneController::class);
    Route::post('scenes/{scene}/lock-view', [AdminSceneController::class, 'lockView'])
        ->name('scenes.lockView');
    
    // Nested resources for infospots within a scene
    Route::resource('scenes.infospots', AdminInfospotController::class)->shallow();

    // Products (belonging to infospots)
    Route::get('infospots/{infospot}/products', [AdminProductController::class, 'index'])
        ->name('infospots.products.index');
    Route::post('infospots/{infospot}/products', [AdminProductController::class, 'store'])
        ->name('infospots.products.store');
    Route::patch('products/{product}', [AdminProductController::class, 'update'])
        ->name('products.update');
    Route::delete('products/{product}', [AdminProductController::class, 'destroy'])
        ->name('products.destroy');

    // Product Assets (belonging to products)
    Route::get('products/{product}/assets', [AdminProductAssetController::class, 'index'])
        ->name('products.assets.index');
    Route::post('products/{product}/assets', [AdminProductAssetController::class, 'store'])
        ->name('products.assets.store');
    Route::delete('product-assets/{asset}', [AdminProductAssetController::class, 'destroy'])
        ->name('product-assets.destroy');
    Route::post('product-assets/reorder', [AdminProductAssetController::class, 'reorder'])
        ->name('product-assets.reorder');
});
