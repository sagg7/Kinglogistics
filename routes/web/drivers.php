<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::get('index', [DriverController::class, 'index'])
        ->name('driver.index');
    Route::get('create', [DriverController::class, 'create'])
        ->name('driver.create');
    Route::post('store', [DriverController::class, 'store'])
        ->name('driver.store');
    Route::get('edit/{id}', [DriverController::class, 'edit'])
        ->name('driver.edit');
    Route::post('update/{id}', [DriverController::class, 'update'])
        ->name('driver.update');
    Route::post('endShift/{id}', [DriverController::class, 'endShift'])
        ->name('driver.endShift');
    Route::post('setActive/{id}', [DriverController::class, 'setActive'])
        ->name('driver.setActive');
    Route::get('search/{type?}', [DriverController::class, 'search'])
        ->name('driver.search');
    Route::middleware('auth:web')->group(function () {
        Route::get('selection', [DriverController::class, 'selection'])
            ->name('driver.selection');
        Route::post('delete/{id}', [DriverController::class, 'destroy'])
            ->name('driver.delete');
        Route::post('restore/{id}', [DriverController::class, 'restore'])
            ->name('driver.restore');
    });
});
