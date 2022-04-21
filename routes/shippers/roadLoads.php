<?php

use App\Http\Controllers\Shippers\RoadLoadController;
use Illuminate\Support\Facades\Route;

Route::prefix('roadLoads')->group(function () {
    // Route::get('index', [LoadController::class, 'index'])
    //     ->name('load.index');
    // Route::get('show/{id}', [LoadController::class, 'show'])
    //     ->name('load.show');
    // Route::get('create', [LoadController::class, 'create'])
    //     ->name('load.create');
    Route::post('store', [RoadLoadController::class, 'store'])
        ->name('roadLoads.store');
    Route::get('selectionLoadType', [RoadLoadController::class, 'selectionLoadType'])
        ->name('roadLoads.selectionLoadType');
    Route::get('selectionTrailerType', [RoadLoadController::class, 'selectionTrailerType'])
        ->name('roadLoads.selectionTrailerType');
    Route::get('selectionLoadMode', [RoadLoadController::class, 'selectionLoadMode'])
        ->name('roadLoads.selectionLoadMode');
    Route::get('selectionStates', [RoadLoadController::class, 'selectionStates'])
        ->name('roadLoads.selectionStates');
    Route::get('selectionCity', [RoadLoadController::class, 'selectionCity'])
        ->name('roadLoads.selectionCity');
    // Route::get('search', [LoadController::class, 'search'])
    //     ->name('load.search');
});
