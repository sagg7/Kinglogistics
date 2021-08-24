<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::prefix('trip')->group(function () {
    Route::get('index', [TripController::class, 'index'])
        ->name('trip.index');
    Route::get('create', [TripController::class, 'create'])
        ->name('trip.create');
    Route::post('store', [TripController::class, 'store'])
        ->name('trip.store');
    Route::get('search/{type?}', [TripController::class, 'search'])
        ->name('trip.search');
    Route::get('selection', [TripController::class, 'selection'])
        ->name('trip.selection');
    Route::get('getTrip', [TripController::class, 'getTrip'])
        ->name('trip.getTrip');
    Route::get('edit/{id}', [TripController::class, 'edit'])
        ->name('trip.edit');
    Route::post('update/{id}', [TripController::class, 'update'])
        ->name('trip.update');
    Route::post('delete/{id}', [TripController::class, 'destroy'])
        ->name('trip.delete');
});
