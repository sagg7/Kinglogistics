<?php

use App\Http\Controllers\ExpenseAccountController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('expense')->group(function () {
    Route::group(['middleware' => ['permission:read-expense']], function () {
        Route::get('index', [ExpenseController::class, 'index'])
            ->name('expense.index');
        Route::get('search', [ExpenseController::class, 'search'])
            ->name('expense.search');
        Route::get('downloadXLS', [ExpenseController::class, 'downloadXLS'])
            ->name('expense.downloadXLS');
    });
    Route::group(['middleware' => ['permission:create-expense']], function () {
        Route::get('create', [ExpenseController::class, 'create'])
            ->name('expense.create');
        Route::post('store', [ExpenseController::class, 'store'])
            ->name('expense.store');
    });
    Route::group(['middleware' => ['permission:update-expense']], function () {
        Route::get('edit/{id}', [ExpenseController::class, 'edit'])
            ->name('expense.edit');
        Route::post('update/{id}', [ExpenseController::class, 'update'])
            ->name('expense.update');
    });
    Route::group(['middleware' => ['permission:delete-expense']], function () {
        Route::post('delete/{id}', [ExpenseController::class, 'destroy'])
            ->name('expense.delete');
    });

    Route::prefix('type')->group(function () {
        Route::group(['middleware' => ['permission:read-expense']], function () {
            Route::get('selection', [ExpenseTypeController::class, 'selection'])
                ->name('expenseType.selection');
        });
        Route::group(['middleware' => ['permission:create-expense']], function () {
            Route::post('store', [ExpenseTypeController::class, 'store'])
                ->name('expenseType.store');
        });
        Route::group(['middleware' => ['permission:delete-expense']], function () {
            Route::post('delete/{id?}', [ExpenseTypeController::class, 'destroy'])
                ->name('expenseType.delete');
        });
    });

    Route::prefix('account')->group(function () {
        Route::group(['middleware' => ['permission:read-expense']], function () {
            Route::get('selection', [ExpenseAccountController::class, 'selection'])
                ->name('expenseAccount.selection');
        });
        Route::group(['middleware' => ['permission:create-expense']], function () {
            Route::post('store', [ExpenseAccountController::class, 'store'])
                ->name('expenseAccount.store');
        });
        Route::group(['middleware' => ['permission:delete-expense']], function () {
            Route::post('delete/{id?}', [ExpenseAccountController::class, 'destroy'])
                ->name('expenseAccount.delete');
        });
    });
});
