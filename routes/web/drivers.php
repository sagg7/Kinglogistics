<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->group(function () {
    Route::group(['middleware' => ['permission:read-driver']], function () {
        Route::get('index', [DriverController::class, 'index'])
            ->name('driver.index');
        Route::get('search/{type?}', [DriverController::class, 'search'])
            ->name('driver.search');
        Route::get('downloadExcel', [DriverController::class, 'downloadExcel'])
            ->name('driver.downloadExcel');

    });
    Route::group(['middleware' => ['permission:create-driver']], function () {
        Route::get('create', [DriverController::class, 'create'])
            ->name('driver.create');
        Route::post('store', [DriverController::class, 'store'])
            ->name('driver.store');
    });
    Route::group(['middleware' => ['permission:update-driver']], function () {
        Route::get('edit/{id}', [DriverController::class, 'edit'])
            ->name('driver.edit');
        Route::post('update/{id}', [DriverController::class, 'update'])
            ->name('driver.update');
        Route::post('endShift/{id}', [DriverController::class, 'endShift'])
            ->name('driver.endShift');
        Route::post('setActive/{id}', [DriverController::class, 'setActive'])
            ->name('driver.setActive');
    });
    Route::group(['middleware' => ['permission:delete-driver']], function () {
        Route::post('delete/{id}', [DriverController::class, 'destroy'])
            ->name('driver.delete');
        Route::post('restore/{id}', [DriverController::class, 'restore'])
            ->name('driver.restore');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [DriverController::class, 'selection'])
        ->name('driver.selection');
});
