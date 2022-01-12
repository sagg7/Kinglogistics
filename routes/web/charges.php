<?php

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ChargeTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('charge')->group(function () {
    Route::group(['middleware' => ['permission:read-statement']], function () {
        Route::get('index', [ChargeController::class, 'index'])
            ->name('charge.index');
        Route::get('diesel', [ChargeController::class, 'diesel'])
            ->name('charge.diesel');
        Route::get('search', [ChargeController::class, 'search'])
            ->name('charge.search');
    });
    Route::group(['middleware' => ['permission:create-statement']], function () {
        Route::get('create', [ChargeController::class, 'create'])
            ->name('charge.create');
        Route::post('store', [ChargeController::class, 'store'])
            ->name('charge.store');
        Route::post('storeDiesel', [ChargeController::class, 'storeDiesel'])
            ->name('charge.storeDiesel');
        Route::post('uploadDieselExcel', [ChargeController::class, 'uploadDieselExcel'])
            ->name('charge.uploadDieselExcel');
    });
    Route::group(['middleware' => ['permission:update-statement']], function () {
        Route::get('edit/{id}', [ChargeController::class, 'edit'])
            ->name('charge.edit');
        Route::post('update/{id}', [ChargeController::class, 'update'])
            ->name('charge.update');
    });
    Route::group(['middleware' => ['permission:delete-statement']], function () {
        Route::post('delete/{id}', [ChargeController::class, 'destroy'])
            ->name('charge.delete');
    });

    Route::prefix('type')->group(function () {
        Route::group(['middleware' => ['permission:read-statement']], function () {
            Route::get('selection', [ChargeTypeController::class, 'selection'])
                ->name('chargeType.selection');
        });
        Route::group(['middleware' => ['permission:create-statement']], function () {
            Route::post('store', [ChargeTypeController::class, 'store'])
                ->name('chargeType.store');
        });
        Route::group(['middleware' => ['permission:delete-statement']], function () {
            Route::post('delete/{id?}', [ChargeTypeController::class, 'destroy'])
                ->name('chargeType.delete');
        });
    });
});
