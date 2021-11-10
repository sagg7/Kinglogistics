<?php

use App\Http\Controllers\Carriers\DriverController as CarrierDriverController;
use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::get('index', [CarrierDriverController::class, 'index'])
        ->name('driver.index');
    Route::get('search', [CarrierDriverController::class, 'search'])
        ->name('driver.search');
    Route::get('selection', [CarrierDriverController::class, 'selection'])
        ->name('driver.selection');
    Route::post('delete/{id}', [CarrierDriverController::class, 'destroy'])
        ->name('driver.delete');

    // Points to main driver controller
    Route::get('create', [DriverController::class, 'create'])
        ->name('driver.create');
    Route::post('store', [DriverController::class, 'store'])
        ->name('driver.store');
    Route::get('edit/{id}', [DriverController::class, 'edit'])
        ->name('driver.edit');
    Route::post('update/{id}', [DriverController::class, 'update'])
        ->name('driver.update');
});
