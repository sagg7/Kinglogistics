<?php

use App\Http\Controllers\BrokerController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin']], function () {
    Route::prefix('company')->group(function () {
        Route::get('profile', [BrokerController::class, 'profile'])
            ->name('company.profile');
        Route::post('update', [BrokerController::class, 'update'])
            ->name('company.update');
        Route::post('equipment', [BrokerController::class, 'equipment'])
            ->name('company.equipment');
        Route::post('service', [BrokerController::class, 'service'])
            ->name('company.service');
    });
});
