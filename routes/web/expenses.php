<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
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

    Route::prefix('type')->group(function () {
        Route::post('store', [ExpenseTypeController::class, 'store'])
            ->name('expenseType.store');
        Route::get('selection', [ExpenseTypeController::class, 'selection'])
            ->name('expenseType.selection');
        Route::post('delete/{id?}', [ExpenseTypeController::class, 'destroy'])
            ->name('expenseType.delete');
    });
});
