<?php

use App\Http\Controllers\RoadLoadController;
use Illuminate\Support\Facades\Route;

Route::prefix('load')->group(function () {
    Route::prefix('road')->group(function () {
        Route::get('index', [RoadLoadController::class, 'index'])
            ->name('load.road.index');
        Route::get('search', [RoadLoadController::class, 'search'])
            ->name('load.road.search');
        Route::post('request', [RoadLoadController::class, 'request'])
            ->name('load.road.request');
        Route::get('getRequests', [RoadLoadController::class, 'getRequests'])
            ->name('load.road.getRequests');
        Route::post('acceptRequest', [RoadLoadController::class, 'acceptRequest'])
            ->name('load.road.acceptRequest');

        // Store modal routes
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

        // Dispatch routes
        Route::prefix('dispatch')->group(function () {
            Route::get('index', [RoadLoadController::class, 'indexDispatch'])
                ->name('load.road.indexDispatch');
        });
    });
});
