<?php

use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::prefix('truck')->group(function () {
    Route::get('selection', [TruckController::class, 'selection'])
        ->name('truck.selection');
});
