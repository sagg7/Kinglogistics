<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('tracking')->group(function () {
    Route::group(['middleware' => ['permission:read-tracking']], function () {
        Route::get('/', [TrackingController::class, 'index'])
            ->name('tracking.index');
    });
    Route::group(['middleware' => ['permission:read-tracking-history']], function () {
        Route::get('history', [TrackingController::class, 'history'])
            ->name('tracking.history');
        Route::get('historyData', [TrackingController::class, 'historyData'])
            ->name('tracking.historyData');
        Route::get('getPinLoadData', [TrackingController::class, 'getPinLoadData'])
            ->name('tracking.getPinLoadData');
    });
});
