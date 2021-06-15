<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('zone')->group(function () {
    Route::get('index', [ZoneController::class, 'index'])
        ->name('zone.index');
    Route::get('create', [ZoneController::class, 'create'])
        ->name('zone.create');
    Route::post('store', [ZoneController::class, 'store'])
        ->name('zone.store');
    Route::get('search', [ZoneController::class, 'search'])
        ->name('zone.search');
    Route::get('selection', [ZoneController::class, 'selection'])
        ->name('zone.selection');
    Route::get('edit/{id}', [ZoneController::class, 'edit'])
        ->name('zone.edit');
    Route::post('update/{id}', [ZoneController::class, 'update'])
        ->name('zone.update');
    Route::post('delete/{id}', [ZoneController::class, 'destroy'])
        ->name('zone.delete');
});
