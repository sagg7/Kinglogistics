<?php

use App\Http\Controllers\PaperworkController;
use Illuminate\Support\Facades\Route;

Route::prefix('paperwork')->group(function () {
    Route::get('index', [PaperworkController::class, 'index'])
        ->name('paperwork.index');
    Route::get('create', [PaperworkController::class, 'create'])
        ->name('paperwork.create');
    Route::post('store', [PaperworkController::class, 'store'])
        ->name('paperwork.store');
    Route::get('search/{type}', [PaperworkController::class, 'search'])
        ->name('paperwork.search');
    Route::get('edit/{id}', [PaperworkController::class, 'edit'])
        ->name('paperwork.edit');
    Route::post('update/{id}', [PaperworkController::class, 'update'])
        ->name('paperwork.update');
    Route::post('delete/{id}', [PaperworkController::class, 'destroy'])
        ->name('paperwork.delete');


    Route::post('storeFiles', [PaperworkController::class, 'storeFiles'])
        ->name('paperwork.storeFiles');
});
