<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\BonusTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('bonus')->group(function () {
    Route::get('index', [BonusController::class, 'index'])
        ->name('bonus.index');
    Route::get('create', [BonusController::class, 'create'])
        ->name('bonus.create');
    Route::get('diesel', [BonusController::class, 'diesel'])
        ->name('bonus.diesel');
    Route::post('store', [BonusController::class, 'store'])
        ->name('bonus.store');
    Route::post('storeDiesel', [BonusController::class, 'storeDiesel'])
        ->name('bonus.storeDiesel');
    Route::get('search', [BonusController::class, 'search'])
        ->name('bonus.search');
    Route::get('edit/{id}', [BonusController::class, 'edit'])
        ->name('bonus.edit');
    Route::post('update/{id}', [BonusController::class, 'update'])
        ->name('bonus.update');
    Route::post('delete/{id}', [BonusController::class, 'destroy'])
        ->name('bonus.delete');

    Route::prefix('type')->group(function () {
        Route::post('store', [BonusTypeController::class, 'store'])
            ->name('bonusType.store');
        Route::get('selection', [BonusTypeController::class, 'selection'])
            ->name('bonusType.selection');
        Route::post('delete/{id?}', [BonusTypeController::class, 'destroy'])
            ->name('bonusType.delete');
    });
});
