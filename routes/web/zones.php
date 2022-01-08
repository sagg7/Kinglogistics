<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('zone')->group(function () {
    Route::group(['middleware' => ['permission:read-zone']], function () {
        Route::get('index', [ZoneController::class, 'index'])
            ->name('zone.index');
        Route::get('search', [ZoneController::class, 'search'])
            ->name('zone.search');
    });
    Route::group(['middleware' => ['permission:create-zone']], function () {
        Route::get('create', [ZoneController::class, 'create'])
            ->name('zone.create');
        Route::post('store', [ZoneController::class, 'store'])
            ->name('zone.store');
    });
    Route::group(['middleware' => ['permission:update-zone']], function () {
        Route::get('edit/{id}', [ZoneController::class, 'edit'])
            ->name('zone.edit');
        Route::post('update/{id}', [ZoneController::class, 'update'])
            ->name('zone.update');
    });
    Route::group(['middleware' => ['permission:delete-zone']], function () {
        Route::post('delete/{id}', [ZoneController::class, 'destroy'])
            ->name('zone.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [ZoneController::class, 'selection'])
        ->name('zone.selection');
});
