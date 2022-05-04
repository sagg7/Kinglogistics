<?php

use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

Route::prefix('city')->group(function () {
    Route::get('selection', [CityController::class, 'selection'])
        ->name('city.selection');
});
