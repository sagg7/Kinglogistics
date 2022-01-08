<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::prefix('trip')->group(function () {
    Route::group(['middleware' => ['permission:read-job']], function () {
        Route::get('index', [TripController::class, 'index'])
            ->name('trip.index');
        Route::get('search/{type?}', [TripController::class, 'search'])
            ->name('trip.search');
        Route::get('dashboardData', [TripController::class, 'dashboardData'])
            ->name('trip.dashboardData');
        Route::get('getTrip', [TripController::class, 'getTrip'])
            ->name('trip.getTrip');
    });
    Route::group(['middleware' => ['permission:create-job']], function () {
        Route::get('create', [TripController::class, 'create'])
            ->name('trip.create');
        Route::post('store', [TripController::class, 'store'])
            ->name('trip.store');
    });
    Route::group(['middleware' => ['permission:update-job']], function () {
        Route::get('edit/{id}', [TripController::class, 'edit'])
            ->name('trip.edit');
        Route::post('update/{id}', [TripController::class, 'update'])
            ->name('trip.update');
    });
    Route::group(['middleware' => ['permission:delete-job']], function () {
        Route::post('delete/{id}', [TripController::class, 'destroy'])
            ->name('trip.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [TripController::class, 'selection'])
        ->name('trip.selection');
});
