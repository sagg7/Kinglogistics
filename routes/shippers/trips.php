<?php

use App\Http\Controllers\DestinationController;
use App\Http\Controllers\OriginController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::prefix('trip')->group(function () {
    Route::get('create', [TripController::class, 'create'])
        ->name('trip.create');
    Route::post('store', [TripController::class, 'store'])
        ->name('trip.store');
    Route::get('selection', [TripController::class, 'selection'])
        ->name('trip.selection');
    Route::get('getTrip', [TripController::class, 'getTrip'])
        ->name('trip.getTrip');
    /*
    Route::get('index', [TripController::class, 'index'])
        ->name('trip.index');
    Route::get('search/{type?}', [TripController::class, 'search'])
        ->name('trip.search');
    Route::get('dashboardData', [TripController::class, 'dashboardData'])
        ->name('trip.dashboardData');
    Route::get('getTrip', [TripController::class, 'getTrip'])
        ->name('trip.getTrip');
    Route::get('edit/{id}', [TripController::class, 'edit'])
        ->name('trip.edit');
    Route::post('update/{id}', [TripController::class, 'update'])
        ->name('trip.update');
    Route::post('delete/{id}', [TripController::class, 'destroy'])
        ->name('trip.delete');*/

    // Origins
    Route::prefix('origin')->group(function () {
        Route::get('selection', [OriginController::class, 'selection'])
            ->name('origin.selection');
    });

    // Destinations
    Route::prefix('destination')->group(function () {
        Route::get('selection', [DestinationController::class, 'selection'])
            ->name('destination.selection');
    });
});
