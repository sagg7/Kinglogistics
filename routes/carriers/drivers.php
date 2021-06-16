<?php

use App\Http\Controllers\Carriers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::get('index', [DriverController::class, 'index'])
        ->name('driver.index');
    Route::get('create', [DriverController::class, 'create'])
        ->name('driver.create');
    Route::post('store', [DriverController::class, 'store'])
        ->name('driver.store');
    Route::get('search', [DriverController::class, 'search'])
        ->name('driver.search');
    Route::get('selection', [DriverController::class, 'selection'])
        ->name('driver.selection');
    Route::get('edit/{id}', [DriverController::class, 'edit'])
        ->name('driver.edit');
    Route::post('update/{id}', [DriverController::class, 'update'])
        ->name('driver.update')
        ->where('profile', '[0-1]');
    Route::post('delete/{id}', [DriverController::class, 'destroy'])
        ->name('driver.delete');
});
