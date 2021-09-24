<?php

use App\Http\Controllers\Carriers\DailyPayController;
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
