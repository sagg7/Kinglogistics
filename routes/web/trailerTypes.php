<?php

use App\Http\Controllers\TrailerTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('trailerType')->group(function () {
    Route::group(['middleware' => ['permission:read-trailer']], function () {
        Route::get('index', [TrailerTypeController::class, 'index'])
            ->name('trailerType.index');
        Route::get('search', [TrailerTypeController::class, 'search'])
            ->name('trailerType.search');
        Route::get('selection', [TrailerTypeController::class, 'selection'])
            ->name('trailerType.selection');
    });
    Route::group(['middleware' => ['permission:create-trailer']], function () {
        Route::get('create', [TrailerTypeController::class, 'create'])
            ->name('trailerType.create');
        Route::post('store', [TrailerTypeController::class, 'store'])
            ->name('trailerType.store');
    });
    Route::group(['middleware' => ['permission:update-trailer']], function () {
        Route::get('edit/{id}', [TrailerTypeController::class, 'edit'])
            ->name('trailerType.edit');
        Route::post('update/{id}', [TrailerTypeController::class, 'update'])
            ->name('trailerType.update');
    });
    Route::group(['middleware' => ['permission:delete-trailer']], function () {
        Route::post('delete/{id?}', [TrailerTypeController::class, 'destroy'])
            ->name('trailerType.delete');
    });
});
