<?php

use App\Http\Controllers\ShipperController;
use App\Http\Controllers\ShipperInvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('shipper')->group(function () {
    Route::get('index', [ShipperController::class, 'index'])
        ->name('shipper.index');
    Route::get('create', [ShipperController::class, 'create'])
        ->name('shipper.create');
    Route::post('store', [ShipperController::class, 'store'])
        ->name('shipper.store');
    Route::get('search', [ShipperController::class, 'search'])
        ->name('shipper.search');
    Route::get('selection', [ShipperController::class, 'selection'])
        ->name('shipper.selection');
    Route::get('selection', [ShipperController::class, 'selection'])
        ->name('shipper.selection');
    Route::get('edit/{id}', [ShipperController::class, 'edit'])
        ->name('shipper.edit');
    Route::post('update/{id}', [ShipperController::class, 'update'])
        ->name('shipper.update');
    Route::post('delete/{id}', [ShipperController::class, 'destroy'])
        ->name('shipper.delete');

    Route::prefix('invoice')->group(function () {
        Route::get('/', [ShipperInvoiceController::class, 'index'])
            ->name('invoice.payments');
        Route::get('search/{type}', [ShipperInvoiceController::class, 'search'])
            ->name('invoice.paymentsSearch');
        Route::get('downloadPDF/{id}', [ShipperInvoiceController::class, 'downloadPDF'])
            ->name('invoice.downloadPaymentPDF');

        Route::post('complete/{id}', [ShipperInvoiceController::class, 'complete'])
            ->name('invoice.completePayment');
    });
});
