<?php

use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::prefix('incident')->group(function () {
    Route::get('index', [IncidentController::class, 'index'])
        ->name('incident.index');
    Route::get('search', [IncidentController::class, 'search'])
        ->name('incident.search');
    Route::get('selection', [IncidentController::class, 'selection'])
        ->name('incident.selection');
    Route::get('downloadPDF/{id}', [IncidentController::class, 'downloadPDF'])
        ->name('incident.downloadPDF');

    Route::middleware(['auth:carrier','documentation'])->group(function () {
        Route::get('create', [IncidentController::class, 'create'])
            ->name('incident.create');
        Route::post('store', [IncidentController::class, 'store'])
            ->name('incident.store');
    });
});
