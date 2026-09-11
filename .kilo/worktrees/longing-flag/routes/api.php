<?php

use App\Http\Controllers\Api\RegionController;

Route::prefix('regions')->group(function () {
    Route::get('/provinces', [RegionController::class, 'provinces'])->name('api.regions.provinces');
    Route::get('/cities', [RegionController::class, 'cities'])->name('api.regions.cities');
    Route::get('/districts', [RegionController::class, 'districts'])->name('api.regions.districts');
    Route::get('/subdistricts', [RegionController::class, 'subdistricts'])->name('api.regions.subdistricts');
    Route::get('/search', [RegionController::class, 'search'])->name('api.regions.search');
});