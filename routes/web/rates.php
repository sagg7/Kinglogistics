<?php

use App\Http\Controllers\RateController;
use Illuminate\Support\Facades\Route;

Route::prefix('rate')->group(function () {
    Route::get('index', [RateController::class, 'index'])
        ->name('rate.index');
    Route::get('create', [RateController::class, 'create'])
        ->name('rate.create');
    Route::post('store', [RateController::class, 'store'])
        ->name('rate.store');
    Route::get('search', [RateController::class, 'search'])
        ->name('rate.search');
    Route::get('selection', [RateController::class, 'selection'])
        ->name('rate.selection');
    Route::get('edit/{id}', [RateController::class, 'edit'])
        ->name('rate.edit');
    Route::post('update/{id}', [RateController::class, 'update'])
        ->name('rate.update');
    Route::post('delete/{id}', [RateController::class, 'destroy'])
        ->name('rate.delete');
});
