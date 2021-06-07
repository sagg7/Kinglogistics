<?php

use App\Http\Controllers\Drivers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::prefix('incident')->group(function () {
    Route::get('index', [IncidentController::class, 'index'])
        ->name('incident.index');
    Route::get('search', [IncidentController::class, 'search'])
        ->name('incident.search');
    Route::get('edit/{id}', [IncidentController::class, 'edit'])
        ->name('incident.edit');
    Route::post('update/{id}', [IncidentController::class, 'update'])
        ->name('incident.update');
});
