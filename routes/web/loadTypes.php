<?php

use App\Http\Controllers\LoadTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('loadType')->group(function () {
    Route::get('index', [LoadTypeController::class, 'index'])
        ->name('loadType.index');
    Route::get('create', [LoadTypeController::class, 'create'])
        ->name('loadType.create');
    Route::post('store', [LoadTypeController::class, 'store'])
        ->name('loadType.store');
    Route::get('search', [LoadTypeController::class, 'search'])
        ->name('loadType.search');
    Route::get('selection', [LoadTypeController::class, 'selection'])
        ->name('loadType.selection');
    Route::get('edit/{id}', [LoadTypeController::class, 'edit'])
        ->name('loadType.edit');
    Route::post('update/{id}', [LoadTypeController::class, 'update'])
        ->name('loadType.update');
    Route::post('delete/{id?}', [LoadTypeController::class, 'destroy'])
        ->name('loadType.delete');
});
