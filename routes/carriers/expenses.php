<?php

use App\Http\Controllers\Carriers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::prefix('expense')->group(function () {
    Route::get('index', [ExpenseController::class, 'index'])
        ->name('expense.index');
    Route::get('create', [ExpenseController::class, 'create'])
        ->name('expense.create');
    Route::post('store', [ExpenseController::class, 'store'])
        ->name('expense.store');
    Route::get('search', [ExpenseController::class, 'search'])
        ->name('expense.search');
    Route::get('edit/{id}', [ExpenseController::class, 'edit'])
        ->name('expense.edit');
    Route::post('update/{id}', [ExpenseController::class, 'update'])
        ->name('expense.update');
    Route::post('delete/{id}', [ExpenseController::class, 'destroy'])
        ->name('expense.delete');
});
