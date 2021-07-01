<?php

use App\Http\Controllers\ChargeController;
use Illuminate\Support\Facades\Route;

Route::prefix('charge')->group(function () {
    Route::get('index', [ChargeController::class, 'index'])
        ->name('charge.index');
    Route::get('create', [ChargeController::class, 'create'])
        ->name('charge.create');
    Route::get('diesel', [ChargeController::class, 'diesel'])
        ->name('charge.diesel');
    Route::post('store', [ChargeController::class, 'store'])
        ->name('charge.store');
    Route::post('storeDiesel', [ChargeController::class, 'storeDiesel'])
        ->name('charge.storeDiesel');
    Route::get('search', [ChargeController::class, 'search'])
        ->name('charge.search');
    Route::get('edit/{id}', [ChargeController::class, 'edit'])
        ->name('charge.edit');
    Route::post('update/{id}', [ChargeController::class, 'update'])
        ->name('charge.update');
    Route::post('delete/{id}', [ChargeController::class, 'destroy'])
        ->name('charge.delete');
});
