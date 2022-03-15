<?php

use App\Http\Controllers\IncomeAccountController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('income')->group(function () {
    Route::group(['middleware' => ['permission:read-income']], function () {
        Route::get('index', [IncomeController::class, 'index'])
            ->name('income.index');
        Route::get('search', [IncomeController::class, 'search'])
            ->name('income.search');
        Route::get('downloadXLS', [IncomeController::class, 'downloadXLS'])
            ->name('income.downloadXLS');
    });
    Route::group(['middleware' => ['permission:create-income']], function () {
        Route::get('create', [IncomeController::class, 'create'])
            ->name('income.create');
        Route::post('store', [IncomeController::class, 'store'])
            ->name('income.store');
        Route::post('uploadIncomeExcel', [IncomeController::class, 'uploadIncomeExcel'])
            ->name('income.uploadIncomeExcel');
    });
    Route::group(['middleware' => ['permission:update-income']], function () {
        Route::get('edit/{id}', [IncomeController::class, 'edit'])
            ->name('income.edit');
        Route::post('update/{id}', [IncomeController::class, 'update'])
            ->name('income.update');
    });
    Route::group(['middleware' => ['permission:delete-income']], function () {
        Route::post('delete/{id}', [IncomeController::class, 'destroy'])
            ->name('income.delete');
    });

    Route::prefix('type')->group(function () {
        Route::group(['middleware' => ['permission:read-income']], function () {
            Route::get('selection', [IncomeTypeController::class, 'selection'])
                ->name('incomeType.selection');
        });
        Route::group(['middleware' => ['permission:create-income']], function () {
            Route::post('store', [IncomeTypeController::class, 'store'])
                ->name('incomeType.store');
        });
        Route::group(['middleware' => ['permission:delete-income']], function () {
            Route::post('delete/{id?}', [IncomeTypeController::class, 'destroy'])
                ->name('incomeType.delete');
        });
    });

    Route::prefix('account')->group(function () {
        Route::group(['middleware' => ['permission:read-income']], function () {
            Route::get('selection', [IncomeAccountController::class, 'selection'])
                ->name('incomeAccount.selection');
        });
        Route::group(['middleware' => ['permission:create-income']], function () {
            Route::post('store', [IncomeAccountController::class, 'store'])
                ->name('incomeAccount.store');
        });
        Route::group(['middleware' => ['permission:delete-income']], function () {
            Route::post('delete/{id?}', [IncomeAccountController::class, 'destroy'])
                ->name('incomeAccount.delete');
        });
    });
});
