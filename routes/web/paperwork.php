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

    Route::get('showTemplate/{id}/{related_id}', [PaperworkController::class, 'showTemplate'])
        ->name('paperwork.showTemplate');
    Route::get('pdf/{id}/{related_id}', [PaperworkController::class, 'pdf'])
        ->name('paperwork.pdf');
    Route::post('storeTemplate/{id}/{related_id}', [PaperworkController::class, 'storeTemplate'])
        ->name('paperwork.storeTemplate');
});
