<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::group(['middleware' => ['permission:read-load-dispatch|read-load']], function () {
        Route::get('getData', [DashboardController::class, 'getData'])
            ->name('dashboard.getData');
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('testKernel', [DashboardController::class, 'testKernel'])
            ->name('testKernel');
    });
});
