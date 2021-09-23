<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::prefix('trip')->group(function () {
    Route::get('selection', [TripController::class, 'selection'])
        ->name('trip.selection');
});
