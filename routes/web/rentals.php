<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

Route::prefix('rental')->group(function () {
    Route::get('index', [RentalController::class, 'index'])
        ->name('rental.index');
    Route::get('create', [RentalController::class, 'create'])
        ->name('rental.create');
    Route::post('store', [RentalController::class, 'store'])
        ->name('rental.store');
    Route::get('search', [RentalController::class, 'search'])
        ->name('rental.search');
    Route::get('edit/{id}', [RentalController::class, 'edit'])
        ->name('rental.edit');
    Route::post('update/{id}', [RentalController::class, 'update'])
        ->name('rental.update');
    Route::post('delete/{id}', [RentalController::class, 'destroy'])
        ->name('rental.delete');
    Route::post('uploadPhoto', [RentalController::class, 'uploadPhoto'])
        ->name('rental.uploadPhoto');
    Route::post('getRented', [RentalController::class, 'getRented'])
        ->name('getRented');

});

Route::prefix('inspection')->group(function () {

//inspection
    Route::post('store', [RentalController::class, 'storeInspection'])
        ->name('inspection.store');
    Route::get('create/{id}', [RentalController::class, 'createInspection'])
        ->name('inspection.create');
    Route::get('endInspection/{id}', [RentalController::class, 'createEndRental'])
        ->name('createEndRental');
    Route::post('endRental', [RentalController::class, 'storeEndRental'])
        ->name('rental.end');

});
