<?php

use App\Http\Controllers\Shippers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('report')->group(function () {
    Route::get('trailers', [ReportController::class, 'trailers'])
        ->name('report.trailers');
    Route::get('trailersData', [ReportController::class, 'trailersData'])
        ->name('report.trailersData');
});
