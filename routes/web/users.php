<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('index', [UserController::class, 'index'])
        ->name('user.index');
    Route::get('create', [UserController::class, 'create'])
        ->name('user.create');
    Route::post('store', [UserController::class, 'store'])
        ->name('user.store');
    Route::get('search', [UserController::class, 'search'])
        ->name('user.search');
    Route::get('selection', [UserController::class, 'selection'])
        ->name('user.selection');
    Route::get('edit/{id}', [UserController::class, 'edit'])
        ->name('user.edit');
    Route::post('update/{id}', [UserController::class, 'update'])
        ->name('user.update');
    Route::post('delete/{id}', [UserController::class, 'destroy'])
        ->name('user.delete');
});
