<?php

use App\Http\Controllers\ShipperController;
use App\Http\Controllers\ShipperInvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('shipper')->group(function () {
    Route::group(['middleware' => ['permission:read-customer']], function () {
        Route::get('index', [ShipperController::class, 'index'])
            ->name('shipper.index');
        Route::get('search', [ShipperController::class, 'search'])
            ->name('shipper.search');
    });
    Route::group(['middleware' => ['permission:create-customer']], function () {
        Route::get('create', [ShipperController::class, 'create'])
            ->name('shipper.create');
        Route::post('store', [ShipperController::class, 'store'])
            ->name('shipper.store');
    });
    Route::group(['middleware' => ['permission:update-customer']], function () {
        Route::get('edit/{id}', [ShipperController::class, 'edit'])
            ->name('shipper.edit');
        Route::post('update/{id}', [ShipperController::class, 'update'])
            ->name('shipper.update');
    });
    Route::group(['middleware' => ['permission:delete-customer']], function () {
        Route::post('delete/{id}', [ShipperController::class, 'destroy'])
            ->name('shipper.delete');
    });
    Route::get('selection', [ShipperController::class, 'selection'])
        ->name('shipper.selection');

    Route::group(['middleware' => ['permission:read-customer']], function () {
        Route::get('status/{customerId?}', [ShipperController::class, 'shipperStatus'])
            ->name('shipper.status');

    });


    Route::prefix('invoice')->group(function () {
        Route::group(['middleware' => ['permission:read-invoice']], function () {
            Route::get('/', [ShipperInvoiceController::class, 'index'])
                ->name('invoice.payments');
            Route::get('search/{type}', [ShipperInvoiceController::class, 'search'])
                ->name('invoice.paymentsSearch');
            Route::get('downloadPDF/{id}', [ShipperInvoiceController::class, 'downloadPDF'])
                ->name('invoice.downloadPaymentPDF');
            Route::get('downloadXLSX/{id}', [ShipperInvoiceController::class, 'downloadXLSX'])
                ->name('invoice.downloadPaymentXLSX');
            Route::get('downloadPhotos/{id}', [ShipperInvoiceController::class, 'downloadPhotos'])
                ->name('invoice.downloadPhotos');
        });
        Route::group(['middleware' => ['permission:create-invoice']], function () {
            Route::post('runInvoices', [ShipperInvoiceController::class, 'runInvoices'])
                ->name('invoice.runInvoices');
        });
        Route::group(['middleware' => ['permission:update-invoice']], function () {
            Route::post('complete/{id}', [ShipperInvoiceController::class, 'complete'])
                ->name('invoice.complete');
            Route::post('completeAll', [ShipperInvoiceController::class, 'completeAll'])
                ->name('invoice.completeAll');
            Route::post('pay/{id}', [ShipperInvoiceController::class, 'pay'])
                ->name('invoice.pay');
            Route::post('payAll', [ShipperInvoiceController::class, 'payAll'])
                ->name('invoice.payAll');
            Route::post('pending/{id}', [ShipperInvoiceController::class, 'pending'])
                ->name('invoice.pending');
        });
        Route::group(['middleware' => ['permission:delete-invoice']], function () {
        });
    });
});
