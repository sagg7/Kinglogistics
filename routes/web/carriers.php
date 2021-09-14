<?php

use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CarrierPaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('carrier')->group(function () {
    Route::get('index', [CarrierController::class, 'index'])
        ->name('carrier.index');
    Route::get('create', [CarrierController::class, 'create'])
        ->name('carrier.create');
    Route::post('store', [CarrierController::class, 'store'])
        ->name('carrier.store');
    Route::get('search', [CarrierController::class, 'search'])
        ->name('carrier.search');
    Route::get('selection', [CarrierController::class, 'selection'])
        ->name('carrier.selection');
    Route::get('edit/{id}', [CarrierController::class, 'edit'])
        ->name('carrier.edit');
    Route::post('update/{id}', [CarrierController::class, 'update'])
        ->name('carrier.update')
        ->where('profile', '[0-1]');
    Route::post('delete/{id}', [CarrierController::class, 'destroy'])
        ->name('carrier.delete');

    Route::prefix('payment')->group(function () {
        Route::get('/', [CarrierPaymentController::class, 'index'])
            ->name('carrier.payments');
        Route::get('search/{type}', [CarrierPaymentController::class, 'search'])
            ->name('carrier.paymentsSearch');
        Route::get('downloadPDF/{id}', [CarrierPaymentController::class, 'downloadPDF'])
            ->name('carrier.downloadPaymentPDF');

        Route::post('complete/{id}', [CarrierPaymentController::class, 'complete'])
            ->name('carrier.completePayment');
        Route::post('approve/{id}', [CarrierPaymentController::class, 'approve'])
            ->name('carrier.approvePayment');
        Route::post('payCharges/{id}', [CarrierPaymentController::class, 'payCharges'])
            ->name('carrier.payCharges');
    });
});
