<?php

use App\Http\Controllers\RateGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('rateGroup')->group(function () {
    Route::group(['middleware' => ['permission:read-rate']], function () {
        Route::get('search', [RateGroupController::class, 'search'])
            ->name('rateGroup.search');
        Route::get('selection', [RateGroupController::class, 'selection'])
            ->name('rateGroup.selection');
    });
    Route::group(['middleware' => ['permission:create-rate']], function () {
        Route::post('store', [RateGroupController::class, 'store'])
            ->name('rateGroup.store');
    });
    Route::group(['middleware' => ['permission:update-rate']], function () {
    });
    Route::group(['middleware' => ['permission:delete-rate']], function () {
        Route::post('delete/{id?}', [RateGroupController::class, 'destroy'])
            ->name('rateGroup.delete');
    });
});
