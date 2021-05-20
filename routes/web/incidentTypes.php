<?php

use App\Http\Controllers\IncidentTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('incidentType')->group(function () {
    Route::get('index', [IncidentTypeController::class, 'index'])
        ->name('incidentType.index');
    Route::get('create', [IncidentTypeController::class, 'create'])
        ->name('incidentType.create');
    Route::post('store', [IncidentTypeController::class, 'store'])
        ->name('incidentType.store');
    Route::get('search', [IncidentTypeController::class, 'search'])
        ->name('incidentType.search');
    Route::get('selection', [IncidentTypeController::class, 'selection'])
        ->name('incidentType.selection');
    Route::get('edit/{id}', [IncidentTypeController::class, 'edit'])
        ->name('incidentType.edit');
    Route::post('update/{id}', [IncidentTypeController::class, 'update'])
        ->name('incidentType.update');
    Route::post('delete/{id?}', [IncidentTypeController::class, 'destroy'])
        ->name('incidentType.delete');
});
