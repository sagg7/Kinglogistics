<?php

use App\Http\Controllers\LoadController;
use Illuminate\Support\Facades\Route;

Route::prefix('load')->group(function () {
    Route::get('index', [LoadController::class, 'index'])
        ->name('load.index');
    Route::get('show/{id}', [LoadController::class, 'show'])
        ->name('load.show');
    Route::get('location/{id}', [LoadController::class, 'location'])
        ->name('load.location');
    Route::get('create', [LoadController::class, 'create'])
        ->name('load.create');
    Route::post('store', [LoadController::class, 'store'])
        ->name('load.store');
    Route::get('search', [LoadController::class, 'search'])
        ->name('load.search');
    Route::get('selection', [LoadController::class, 'selection'])
        ->name('load.selection');
    Route::get('edit/{id}', [LoadController::class, 'edit'])
        ->name('load.edit');
    Route::post('update/{id}', [LoadController::class, 'update'])
        ->name('load.update');
    Route::post('partialUpdate/{id}', [LoadController::class, 'partialUpdate'])
        ->name('load.partialUpdate');
    Route::post('replacePhoto/{id}/{type}', [LoadController::class, 'replacePhoto'])
        ->name('load.replacePhoto');
    Route::post('loadPhoto/{id}/{type}', [LoadController::class, 'loadPhoto'])
        ->name('load.loadPhoto');
    Route::get('DownloadExcelReport', [LoadController::class, 'DownloadExcelReport'])
        ->name('load.DownloadExcelReport');
    Route::post('delete/{id}', [LoadController::class, 'destroy'])
        ->name('load.delete');
});
