<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('zone')->group(function () {
    Route::get('selection', [ZoneController::class, 'selection'])
        ->name('zone.selection');
});
