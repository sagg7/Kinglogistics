<?php

use App\Http\Controllers\Carriers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('report')->group(function () {
    Route::get('historical', [ReportController::class, 'historical'])
        ->name('rental.historical');
    Route::get('historicalData', [ReportController::class, 'historicalData'])
        ->name('rental.historicalData');
});
