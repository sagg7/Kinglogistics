<?php

use App\Http\Controllers\DestinationController;
use App\Http\Controllers\OriginController;
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

    // Origins
    Route::prefix('origin')->group(function () {
        Route::group(['middleware' => ['permission:read-job']], function () {
            Route::get('index', [OriginController::class, 'index'])
                ->name('origin.index');
            Route::get('search', [OriginController::class, 'search'])
                ->name('origin.search');
        });
        Route::group(['middleware' => ['permission:create-job']], function () {
            Route::get('create', [OriginController::class, 'create'])
                ->name('origin.create');
            Route::post('store', [OriginController::class, 'store'])
                ->name('origin.store');
        });
        Route::group(['middleware' => ['permission:update-job']], function () {
            Route::get('edit/{id}', [OriginController::class, 'edit'])
                ->name('origin.edit');
            Route::post('update/{id}', [OriginController::class, 'update'])
                ->name('origin.update');
        });
        Route::group(['middleware' => ['permission:delete-job']], function () {
            Route::post('delete/{id}', [OriginController::class, 'destroy'])
                ->name('origin.delete');
        });
        Route::get('selection', [OriginController::class, 'selection'])
            ->name('origin.selection');
    });

    // Destinations
    Route::prefix('destination')->group(function () {
        Route::group(['middleware' => ['permission:read-job']], function () {
            Route::get('index', [DestinationController::class, 'index'])
                ->name('destination.index');
            Route::get('search', [DestinationController::class, 'search'])
                ->name('destination.search');
        });
        Route::group(['middleware' => ['permission:create-job']], function () {
            Route::get('create', [DestinationController::class, 'create'])
                ->name('destination.create');
            Route::post('store', [DestinationController::class, 'store'])
                ->name('destination.store');
        });
        Route::group(['middleware' => ['permission:update-job']], function () {
            Route::get('edit/{id}', [DestinationController::class, 'edit'])
                ->name('destination.edit');
            Route::post('update/{id}', [DestinationController::class, 'update'])
                ->name('destination.update');
        });
        Route::group(['middleware' => ['permission:delete-job']], function () {
            Route::post('delete/{id}', [DestinationController::class, 'destroy'])
                ->name('destination.delete');
        });
        Route::get('selection', [DestinationController::class, 'selection'])
            ->name('destination.selection');
    });
});
