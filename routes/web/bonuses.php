<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\BonusTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('bonus')->group(function () {
    Route::group(['middleware' => ['permission:read-bonus']], function () {
        Route::get('index', [BonusController::class, 'index'])
            ->name('bonus.index');
        Route::get('search', [BonusController::class, 'search'])
            ->name('bonus.search');
    });
    Route::group(['middleware' => ['permission:create-bonus']], function () {
        Route::get('create', [BonusController::class, 'create'])
            ->name('bonus.create');
        Route::post('store', [BonusController::class, 'store'])
            ->name('bonus.store');
    });
    Route::group(['middleware' => ['permission:update-bonus']], function () {
        Route::get('edit/{id}', [BonusController::class, 'edit'])
            ->name('bonus.edit');
        Route::post('update/{id}', [BonusController::class, 'update'])
            ->name('bonus.update');
    });
    Route::group(['middleware' => ['permission:delete-bonus']], function () {
        Route::post('delete/{id}', [BonusController::class, 'destroy'])
            ->name('bonus.delete');
    });

    Route::prefix('type')->group(function () {
        Route::group(['middleware' => ['permission:create-bonus']], function () {
            Route::post('store', [BonusTypeController::class, 'store'])
                ->name('bonusType.store');
        });
        Route::group(['middleware' => ['permission:read-bonus']], function () {
            Route::get('selection', [BonusTypeController::class, 'selection'])
                ->name('bonusType.selection');
        });
        Route::group(['middleware' => ['permission:delete-bonus']], function () {
            Route::post('delete/{id?}', [BonusTypeController::class, 'destroy'])
                ->name('bonusType.delete');
        });
    });
});
