<?php

use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::prefix('incident')->group(function () {
    Route::get('index', [IncidentController::class, 'index'])
        ->name('incident.index');
    Route::get('search', [IncidentController::class, 'search'])
        ->name('incident.search');
    Route::get('downloadPDF/{id}', [IncidentController::class, 'downloadPDF'])
        ->name('incident.downloadPDF');
});
