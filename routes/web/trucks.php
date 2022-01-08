<?php

use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::prefix('truck')->group(function () {
    Route::group(['middleware' => ['permission:read-truck']], function () {
        Route::get('index', [TruckController::class, 'index'])
            ->name('truck.index');
        Route::get('search', [TruckController::class, 'search'])
            ->name('truck.search');
    });
    Route::group(['middleware' => ['permission:create-truck']], function () {
        Route::get('create', [TruckController::class, 'create'])
            ->name('truck.create');
        Route::post('store', [TruckController::class, 'store'])
            ->name('truck.store');
    });
    Route::group(['middleware' => ['permission:update-truck']], function () {
        Route::get('edit/{id}', [TruckController::class, 'edit'])
            ->name('truck.edit');
        Route::post('update/{id}', [TruckController::class, 'update'])
            ->name('truck.update');
    });
    Route::group(['middleware' => ['permission:delete-truck']], function () {
        Route::post('delete/{id}', [TruckController::class, 'destroy'])
            ->name('truck.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [TruckController::class, 'selection'])
        ->name('truck.selection');
});
