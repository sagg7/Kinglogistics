<?php

use App\Http\Controllers\RateGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('rateGroup')->group(function () {
    Route::post('store', [RateGroupController::class, 'store'])
        ->name('rateGroup.store');
    Route::get('search', [RateGroupController::class, 'search'])
        ->name('rateGroup.search');
    Route::get('selection', [RateGroupController::class, 'selection'])
        ->name('rateGroup.selection');
    Route::post('delete/{id?}', [RateGroupController::class, 'destroy'])
        ->name('rateGroup.delete');
});
