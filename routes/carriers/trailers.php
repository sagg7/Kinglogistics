<?php

use App\Http\Controllers\Carriers\TrailerController;
use Illuminate\Support\Facades\Route;

Route::prefix('trailer')->group(function () {
    Route::get('index', [TrailerController::class, 'index'])
        ->name('trailer.index');
    Route::get('create', [TrailerController::class, 'create'])
        ->name('trailer.create');
    Route::post('store', [TrailerController::class, 'store'])
        ->name('trailer.store');
    Route::get('search', [TrailerController::class, 'search'])
        ->name('trailer.search');
    Route::get('selection', [TrailerController::class, 'selection'])
        ->name('trailer.selection');
    Route::get('edit/{id}', [TrailerController::class, 'edit'])
        ->name('trailer.edit');
    Route::post('update/{id}', [TrailerController::class, 'update'])
        ->name('trailer.update');
    Route::post('delete/{id}', [TrailerController::class, 'destroy'])
        ->name('trailer.delete');
});
