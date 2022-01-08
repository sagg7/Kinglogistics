<?php

use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('loan')->group(function () {
    Route::group(['middleware' => ['permission:read-carrier']], function () {
        Route::get('index', [LoanController::class, 'index'])
            ->name('loan.index');
        Route::get('search', [LoanController::class, 'search'])
            ->name('loan.search');
    });
    Route::group(['middleware' => ['permission:create-carrier']], function () {
        Route::get('create', [LoanController::class, 'create'])
            ->name('loan.create');
        Route::post('store', [LoanController::class, 'store'])
            ->name('loan.store');
    });
    Route::group(['middleware' => ['permission:update-carrier']], function () {
        Route::get('edit/{id}', [LoanController::class, 'edit'])
            ->name('loan.edit');
        Route::post('update/{id}', [LoanController::class, 'update'])
            ->name('loan.update');
    });
    Route::group(['middleware' => ['permission:delete-carrier']], function () {
        Route::post('delete/{id}', [LoanController::class, 'destroy'])
            ->name('loan.delete');
    });
});
