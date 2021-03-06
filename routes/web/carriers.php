<?php

use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CarrierPaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('carrier')->group(function () {
    Route::group(['middleware' => ['permission:read-carrier']], function () {
        Route::get('index', [CarrierController::class, 'index'])
            ->name('carrier.index');
        Route::get('show/{id}', [CarrierController::class, 'show'])
            ->name('carrier.show');
        Route::get('summaryData/{id?}', [CarrierController::class, 'summaryData'])
            ->name('carrier.summaryData');
        Route::get('search/{type?}', [CarrierController::class, 'search'])
            ->name('carrier.search');
        Route::get('getCarrierData/{id}', [CarrierController::class, 'getCarrierData'])
            ->name('carrier.getCarrierData');
        Route::get('getLink/{id}', [CarrierController::class, 'getLink'])
            ->name('carrier.getLink');
    });
    Route::group(['middleware' => ['permission:create-carrier']], function () {
        Route::get('create', [CarrierController::class, 'create'])
            ->name('carrier.create');
        Route::post('store', [CarrierController::class, 'store'])
            ->name('carrier.store');
        Route::post('sendMail/{id?}', [CarrierController::class, 'sendMail'])
            ->name('carrier.sendMail');
    });
    Route::group(['middleware' => ['permission:update-carrier']], function () {
        Route::get('edit/{id}', [CarrierController::class, 'edit'])
            ->name('carrier.edit');
        Route::post('update/{id}', [CarrierController::class, 'update'])
            ->name('carrier.update');
        Route::post('setStatus/{id}', [CarrierController::class, 'setStatus'])
            ->name('carrier.setStatus');
    });
    Route::group(['middleware' => ['permission:delete-carrier']], function () {
        Route::post('delete/{id}', [CarrierController::class, 'destroy'])
            ->name('carrier.delete');
        Route::post('restore/{id}', [CarrierController::class, 'restore'])
            ->name('carrier.restore');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [CarrierController::class, 'selection'])
        ->name('carrier.selection');

    Route::prefix('payment')->group(function () {
        Route::group(['middleware' => ['permission:read-carrier']], function () {
            Route::get('/', [CarrierPaymentController::class, 'index'])
                ->name('carrierPayment.index');
            Route::get('search/{type}', [CarrierPaymentController::class, 'search'])
                ->name('carrierPayment.search');
            Route::get('downloadXLSX/{type}', [CarrierPaymentController::class, 'downloadXLSX'])
                ->name('carrierPayment.downloadXLSX');
            Route::get('downloadPDF/{id}', [CarrierPaymentController::class, 'downloadPDF'])
                ->name('carrierPayment.downloadPaymentPDF');
            Route::post('pending/{id}', [CarrierPaymentController::class, 'pending'])
                ->name('carrierPayment.pending');
        });
        Route::group(['middleware' => ['permission:update-carrier']], function () {
            Route::get('edit/{id}', [CarrierPaymentController::class, 'edit'])
                ->name('carrierPayment.edit');
            Route::post('update/{id}', [CarrierPaymentController::class, 'update'])
                ->name('carrierPayment.update');
            Route::post('complete/{id}', [CarrierPaymentController::class, 'complete'])
                ->name('carrierPayment.completePayment');
            Route::post('approve/{id}', [CarrierPaymentController::class, 'approve'])
                ->name('carrierPayment.approvePayment');
            Route::post('payCharges/{id}', [CarrierPaymentController::class, 'payCharges'])
                ->name('carrierPayment.payCharges');
        });
    });

    Route::prefix('equipment')->group(function () {
        Route::group(['middleware' => ['permission:read-carrier']], function () {
            Route::get('search', [CarrierController::class, 'searchEquipment'])
                ->name('equipment.search');
        });
    });
});
