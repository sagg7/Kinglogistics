<?php

use App\Http\Controllers\Carriers\RoadLoadController;
use Illuminate\Support\Facades\Route;

Route::prefix('load')->group(function () {
    Route::prefix('road')->group(function () {
        Route::get('index', [RoadLoadController::class, 'index'])
            ->name('load.road.index');
        Route::get('search', [RoadLoadController::class, 'search'])
            ->name('load.road.search');
    });
});
