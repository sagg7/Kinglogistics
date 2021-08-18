<?php

use App\Http\Controllers\Carriers\ExpenseController;
use App\Http\Controllers\Carriers\ExpenseTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('expense')->group(function () {
    Route::get('index', [ExpenseController::class, 'index'])
        ->name('carrierExpense.index');
    Route::get('create', [ExpenseController::class, 'create'])
        ->name('carrierExpense.create');
    Route::post('store', [ExpenseController::class, 'store'])
        ->name('carrierExpense.store');
    Route::get('search', [ExpenseController::class, 'search'])
        ->name('carrierExpense.search');
    Route::get('edit/{id}', [ExpenseController::class, 'edit'])
        ->name('carrierExpense.edit');
    Route::post('update/{id}', [ExpenseController::class, 'update'])
        ->name('carrierExpense.update');
    Route::post('delete/{id}', [ExpenseController::class, 'destroy'])
        ->name('carrierExpense.delete');

    Route::prefix('type')->group(function () {
        Route::post('store', [ExpenseTypeController::class, 'store'])
            ->name('carrierExpenseType.store');
        Route::get('selection', [ExpenseTypeController::class, 'selection'])
            ->name('carrierExpenseType.selection');
        Route::post('delete/{id?}', [ExpenseTypeController::class, 'destroy'])
            ->name('carrierExpenseType.delete');
    });
});
