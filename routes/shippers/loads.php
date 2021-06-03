<?php

use App\Http\Controllers\LoadController;
use Illuminate\Support\Facades\Route;

Route::prefix('load')->group(function () {
    Route::get('index', [LoadController::class, 'index'])
        ->name('load.index');
    Route::get('show/{id}', [LoadController::class, 'show'])
        ->name('load.show');
    Route::get('create', [LoadController::class, 'create'])
        ->name('load.create');
    Route::post('store', [LoadController::class, 'store'])
        ->name('load.store');
    Route::get('search', [LoadController::class, 'search'])
        ->name('load.search');
});
