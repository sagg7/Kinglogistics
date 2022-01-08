<?php

use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::prefix('truck')->group(function () {
    Route::get('index', function () {
        return view('subdomains.carriers.trucks.index');
    })
        ->name('truck.index');
    Route::get('create', [TruckController::class, 'create'])
        ->name('truck.create');
    Route::post('store', [TruckController::class, 'store'])
        ->name('truck.store');
    Route::get('search', [TruckController::class, 'search'])
        ->name('truck.search');
    Route::get('selection', [TruckController::class, 'selection'])
        ->name('truck.selection');
    Route::get('edit/{id}', [TruckController::class, 'edit'])
        ->name('truck.edit');
    Route::post('update/{id}', [TruckController::class, 'update'])
        ->name('truck.update');
});
