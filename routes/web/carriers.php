<?php

use App\Http\Controllers\CarrierController;
use Illuminate\Support\Facades\Route;

Route::prefix('carrier')->group(function () {
    Route::get('index', [CarrierController::class, 'index'])
        ->name('carrier.index');
    Route::get('create', [CarrierController::class, 'create'])
        ->name('carrier.create');
    Route::post('store', [CarrierController::class, 'store'])
        ->name('carrier.store');
    Route::get('search', [CarrierController::class, 'search'])
        ->name('carrier.search');
    Route::get('selection', [CarrierController::class, 'selection'])
        ->name('carrier.selection');
    Route::get('edit/{id}', [CarrierController::class, 'edit'])
        ->name('carrier.edit');
    Route::post('update/{id}/{profile?}', [CarrierController::class, 'update'])
        ->name('carrier.update')
        ->where('profile', '[0-1]');
    Route::post('delete/{id}', [CarrierController::class, 'destroy'])
        ->name('carrier.delete');
});
