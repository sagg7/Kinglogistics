<?php

use App\Http\Controllers\Shippers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('equipment')->group(function () {
    Route::get('/', [ProfileController::class, 'equipment'])
        ->name('company.equipment');
});
Route::prefix('services')->group(function () {
    Route::get('/', [ProfileController::class, 'services'])
        ->name('equipment.services');
});
