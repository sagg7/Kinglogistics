<?php

use App\Http\Controllers\Shippers\BrokerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->group(function () {
    Route::get('/', [BrokerController::class, 'index'])
        ->name('company.index');
    Route::get('equipment', [BrokerController::class, 'equipment'])
        ->name('company.equipment');
    Route::get('equipment', [BrokerController::class, 'equipment'])
        ->name('company.equipment');
    Route::get('services', [BrokerController::class, 'services'])
        ->name('equipment.services');
    Route::get('searchStaff', [UserController::class, 'staffOnTurn'])
        ->name('user.searchStaff');
});
