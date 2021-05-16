<?php

use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::prefix('incident')->group(function () {
    Route::get('index', [IncidentController::class, 'index'])
        ->name('incident.index');
    Route::get('create', [IncidentController::class, 'create'])
        ->name('incident.create');
    Route::post('store', [IncidentController::class, 'store'])
        ->name('incident.store');
    Route::get('search', [IncidentController::class, 'search'])
        ->name('incident.search');
    Route::get('selection', [IncidentController::class, 'selection'])
        ->name('incident.selection');
    Route::get('edit/{id}', [IncidentController::class, 'edit'])
        ->name('incident.edit');
    Route::post('update/{id}', [IncidentController::class, 'update'])
        ->name('incident.update');
    Route::post('delete/{id}', [IncidentController::class, 'destroy'])
        ->name('incident.delete');
});
