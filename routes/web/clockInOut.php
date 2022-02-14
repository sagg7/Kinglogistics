<?php

use App\Http\Controllers\ClockInOutController;
use Illuminate\Support\Facades\Route;

Route::prefix('rate')->group(function () {
    Route::group(['middleware' => ['permission:read-rate']], function () {
        Route::get('index', [ClockInOutController::class, 'index'])
            ->name('rate.index');
        Route::get('search', [ClockInOutController::class, 'search'])
            ->name('rate.search');
    });
    Route::group(['middleware' => ['permission:create-rate']], function () {
        Route::get('create', [ClockInOutController::class, 'create'])
            ->name('rate.create');
        Route::post('store', [ClockInOutController::class, 'store'])
            ->name('rate.store');
    });
    // Route::group(['middleware' => ['permission:update-rate']], function () {
    //     Route::get('edit/{id}', [ClockInOutController::class, 'edit'])
    //         ->name('rate.edit');
    //     Route::post('update/{id}', [ClockInOutController::class, 'update'])
    //         ->name('rate.update');
    // });
    Route::group(['middleware' => ['permission:delete-rate']], function () {
        Route::post('delete/{id}', [ClockInOutController::class, 'destroy'])
            ->name('rate.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [ClockInOutController::class, 'selection'])
        ->name('rate.selection');
});
