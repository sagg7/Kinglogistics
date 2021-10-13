<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::get('index', [DriverController::class, 'index'])
        ->name('driver.index');
    Route::get('search/{type?}', [DriverController::class, 'search'])
        ->name('driver.search');
    Route::middleware('auth:web')->group(function () {
        Route::get('selection', [DriverController::class, 'selection'])
            ->name('driver.selection');
    });
});
