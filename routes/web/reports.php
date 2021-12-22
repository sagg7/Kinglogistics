<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('report')->group(function () {
    Route::get('dailyLoads', [ReportController::class, 'dailyLoads'])
        ->name('report.dailyLoads');
    Route::get('dailyLoadsData', [ReportController::class, 'dailyLoadsData'])
        ->name('report.dailyLoadsData');
});
