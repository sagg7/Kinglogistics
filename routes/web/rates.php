<?php

use App\Http\Controllers\RateController;
use Illuminate\Support\Facades\Route;

Route::prefix('rate')->group(function () {
    Route::group(['middleware' => ['permission:read-rate']], function () {
        Route::get('index', [RateController::class, 'index'])
            ->name('rate.index');
        Route::get('search', [RateController::class, 'search'])
            ->name('rate.search');
    });
    Route::group(['middleware' => ['permission:create-rate']], function () {
        Route::get('create', [RateController::class, 'create'])
            ->name('rate.create');
        Route::post('store', [RateController::class, 'store'])
            ->name('rate.store');
    });
    Route::group(['middleware' => ['permission:update-rate']], function () {
        Route::get('edit/{id}', [RateController::class, 'edit'])
            ->name('rate.edit');
        Route::post('update/{id}', [RateController::class, 'update'])
            ->name('rate.update');
    });
    Route::group(['middleware' => ['permission:delete-rate']], function () {
        Route::post('delete/{id}', [RateController::class, 'destroy'])
            ->name('rate.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [RateController::class, 'selection'])
        ->name('rate.selection');
});
