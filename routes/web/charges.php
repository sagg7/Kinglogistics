<?php

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ChargeTypeController;
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
    Route::post('uploadDieselExcel', [ChargeController::class, 'uploadDieselExcel'])
        ->name('charge.uploadDieselExcel');

    Route::prefix('type')->group(function () {
        Route::post('store', [ChargeTypeController::class, 'store'])
            ->name('chargeType.store');
        Route::get('selection', [ChargeTypeController::class, 'selection'])
            ->name('chargeType.selection');
        Route::post('delete/{id?}', [ChargeTypeController::class, 'destroy'])
            ->name('chargeType.delete');
    });
});
