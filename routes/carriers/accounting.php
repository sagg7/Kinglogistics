<?php

use App\Http\Controllers\Carriers\CarrierPaymentsController;
use App\Http\Controllers\Carriers\DailyPayController;
use App\Http\Controllers\Carriers\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('dailyPay')->group(function () {
    Route::get('index', [DailyPayController::class, 'index'])
        ->name('dailyPay.index');
    Route::get('create', [DailyPayController::class, 'create'])
        ->name('dailyPay.create');
    Route::post('store', [DailyPayController::class, 'store'])
        ->name('dailyPay.store');
    Route::get('search', [DailyPayController::class, 'search'])
        ->name('dailyPay.search');
    Route::get('edit/{id}', [DailyPayController::class, 'edit'])
        ->name('dailyPay.edit');
    Route::post('update/{id}', [DailyPayController::class, 'update'])
        ->name('dailyPay.update');
    Route::post('delete/{id}', [DailyPayController::class, 'destroy'])
        ->name('dailyPay.delete');
});

Route::prefix('loan')->group(function () {
    Route::get('index', [LoanController::class, 'index'])
        ->name('loans.index');
    Route::get('search', [LoanController::class, 'search'])
        ->name('loans.search');
});

Route::prefix('payment')->group(function () {
    Route::get('index', [CarrierPaymentsController::class, 'index'])
        ->name('payment.index');
    Route::get('search', [CarrierPaymentsController::class, 'search'])
        ->name('payment.search');
    Route::get('downloadPDF/{id}', [CarrierPaymentsController::class, 'downloadPDF'])
        ->name('payment.downloadPaymentPDF');
});
