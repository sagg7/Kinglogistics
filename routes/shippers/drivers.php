<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::get('index', function () {
        return view('subdomains.shippers.drivers.index');
    })
        ->name('driver.index');
    Route::get('search/{type?}', [DriverController::class, 'search'])
        ->name('driver.search');
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
});
