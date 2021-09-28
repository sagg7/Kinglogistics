<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin|operations|dispatch']], function () {
    Route::prefix('tracking')->group(function () {
        Route::get('/', [TrackingController::class, 'index'])
            ->name('tracking.index');
        Route::get('history', [TrackingController::class, 'history'])
            ->name('tracking.history');
        Route::get('historyData', [TrackingController::class, 'historyData'])
            ->name('tracking.historyData');
        Route::get('getPinLoadData', [TrackingController::class, 'getPinLoadData'])
            ->name('tracking.getPinLoadData');
    });
});
