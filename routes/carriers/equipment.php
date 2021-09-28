<?php

use App\Http\Controllers\Carriers\CarrierEquipmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('equipment')->group(function () {
    Route::get('create', [CarrierEquipmentController::class, 'create'])
        ->name('equipment.create');
    Route::post('store', [CarrierEquipmentController::class, 'store'])
        ->name('equipment.store');
    Route::get('edit/{id}', [CarrierEquipmentController::class, 'edit'])
        ->name('equipment.edit');
    Route::post('update/{id}', [CarrierEquipmentController::class, 'update'])
        ->name('equipment.update');
    Route::post('delete/{id}', [CarrierEquipmentController::class, 'destroy'])
        ->name('equipment.delete');
    Route::get('search', [CarrierEquipmentController::class, 'search'])
        ->name('equipment.search');
});
