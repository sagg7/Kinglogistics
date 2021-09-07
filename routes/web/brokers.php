<?php

use App\Http\Controllers\BrokerController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->group(function () {
    Route::get('profile', [BrokerController::class, 'profile'])
        ->name('company.profile');
    Route::post('update/{id}', [BrokerController::class, 'update'])
        ->name('company.update');
});
