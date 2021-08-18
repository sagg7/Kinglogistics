<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('getData', [DashboardController::class, 'getData'])
        ->name('dashboard.getData');
    Route::get('loadSummary', [DashboardController::class, 'loadSummary'])
        ->name('dashboard.loadSummary');
    Route::get('testKernel', [DashboardController::class, 'testKernel'])
        ->name('testKernel');
});
