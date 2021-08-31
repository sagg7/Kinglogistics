<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin|operations|dispatch']], function () {
    Route::prefix('tracking')->group(function () {
        Route::get('/', [TrackingController::class, 'index'])
            ->name('tracking.index');
    });
});
