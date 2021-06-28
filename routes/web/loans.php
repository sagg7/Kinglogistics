<?php

use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('loan')->group(function () {
    Route::get('index', [LoanController::class, 'index'])
        ->name('loan.index');
    Route::get('create', [LoanController::class, 'create'])
        ->name('loan.create');
    Route::post('store', [LoanController::class, 'store'])
        ->name('loan.store');
    Route::get('search', [LoanController::class, 'search'])
        ->name('loan.search');
    Route::get('edit/{id}', [LoanController::class, 'edit'])
        ->name('loan.edit');
    Route::post('update/{id}', [LoanController::class, 'update'])
        ->name('loan.update');
    Route::post('delete/{id}', [LoanController::class, 'destroy'])
        ->name('loan.delete');
});
