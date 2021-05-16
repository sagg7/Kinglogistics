<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::get('selection', [DriverController::class, 'selection'])
        ->name('driver.selection');
});
