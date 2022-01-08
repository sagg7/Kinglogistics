<?php

use App\Http\Controllers\LoadController;
use Illuminate\Support\Facades\Route;

Route::prefix('load')->group(function () {
    Route::group(['middleware' => ['permission:read-load']], function () {
        Route::get('index', [LoadController::class, 'index'])
            ->name('load.index');
        Route::get('show/{id}', [LoadController::class, 'show'])
            ->name('load.show');
        Route::get('search', [LoadController::class, 'search'])
            ->name('load.search');
    });
    Route::group(['middleware' => ['permission:create-load']], function () {
        Route::get('create', [LoadController::class, 'create'])
            ->name('load.create');
        Route::post('store', [LoadController::class, 'store'])
            ->name('load.store');
    });
    Route::group(['middleware' => ['permission:update-load']], function () {
        Route::get('edit/{id}', [LoadController::class, 'edit'])
            ->name('load.edit');
        Route::post('update/{id}', [LoadController::class, 'update'])
            ->name('load.update');
    });
    Route::group(['middleware' => ['permission:delete-load']], function () {
        Route::post('delete/{id}', [LoadController::class, 'destroy'])
            ->name('load.delete');
    });
    Route::get('selection', [LoadController::class, 'selection'])
        ->name('load.selection');

    // Loads Dispatch Routes
    Route::group(['middleware' => ['permission:read-load-dispatch']], function () {
        Route::get('indexDispatch', [LoadController::class, 'indexDispatch'])
            ->name('load.indexDispatch');
        Route::get('pictureReport', [LoadController::class, 'pictureReport'])
            ->name('load.pictureReport');
        Route::get('DownloadExcelReport', [LoadController::class, 'DownloadExcelReport'])
            ->name('load.DownloadExcelReport');
    });
    Route::group(['middleware' => ['permission:create-load-dispatch']], function () {
    });
    Route::group(['middleware' => ['permission:update-load-dispatch']], function () {
        Route::post('partialUpdate/{id}', [LoadController::class, 'partialUpdate'])
            ->name('load.partialUpdate');
        Route::post('markAsInspected/{id}', [LoadController::class, 'markAsInspected'])
            ->name('load.markAsInspected');
        Route::post('unmarkAsInspected/{id}', [LoadController::class, 'unmarkAsInspected'])
            ->name('load.unmarkAsInspected');
        Route::post('replacePhoto/{id}/{type}', [LoadController::class, 'replacePhoto'])
            ->name('load.replacePhoto');
        Route::post('loadPhoto/{id}/{type}', [LoadController::class, 'loadPhoto'])
            ->name('load.loadPhoto');
    });
});
