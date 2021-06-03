<?php

use App\Http\Controllers\ShipperController;
use Illuminate\Support\Facades\Route;

Route::prefix('shipper')->group(function () {
    Route::get('selection', [ShipperController::class, 'selection'])
        ->name('shipper.selection');
});
