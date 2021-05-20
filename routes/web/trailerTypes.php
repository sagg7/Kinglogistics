<?php

use App\Http\Controllers\TrailerTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('trailerType')->group(function () {
    Route::get('index', [TrailerTypeController::class, 'index'])
        ->name('trailerType.index');
    Route::get('create', [TrailerTypeController::class, 'create'])
        ->name('trailerType.create');
    Route::post('store', [TrailerTypeController::class, 'store'])
        ->name('trailerType.store');
    Route::get('search', [TrailerTypeController::class, 'search'])
        ->name('trailerType.search');
    Route::get('selection', [TrailerTypeController::class, 'selection'])
        ->name('trailerType.selection');
    Route::get('edit/{id}', [TrailerTypeController::class, 'edit'])
        ->name('trailerType.edit');
    Route::post('update/{id}', [TrailerTypeController::class, 'update'])
        ->name('trailerType.update');
    Route::post('delete/{id?}', [TrailerTypeController::class, 'destroy'])
        ->name('trailerType.delete');
});
