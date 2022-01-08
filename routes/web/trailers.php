<?php

use App\Http\Controllers\TrailerController;
use Illuminate\Support\Facades\Route;

Route::prefix('trailer')->group(function () {
    Route::group(['middleware' => ['permission:read-trailer']], function () {
        Route::get('index', [TrailerController::class, 'index'])
            ->name('trailer.index');
        Route::get('search/{type?}', [TrailerController::class, 'search'])
            ->name('trailer.search');
        Route::get('downloadXLS/{type}', [TrailerController::class, 'downloadXLS'])
            ->name('trailer.downloadXLS');
    });
    Route::group(['middleware' => ['permission:create-trailer']], function () {
        Route::get('create', [TrailerController::class, 'create'])
            ->name('trailer.create');
        Route::post('store', [TrailerController::class, 'store'])
            ->name('trailer.store');
    });
    Route::group(['middleware' => ['permission:update-trailer']], function () {
        Route::get('edit/{id}', [TrailerController::class, 'edit'])
            ->name('trailer.edit');
        Route::post('update/{id}', [TrailerController::class, 'update'])
            ->name('trailer.update');
    });
    Route::group(['middleware' => ['permission:delete-trailer']], function () {
        Route::post('delete/{id}', [TrailerController::class, 'destroy'])
            ->name('trailer.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [TrailerController::class, 'selection'])
        ->name('trailer.selection');
});
