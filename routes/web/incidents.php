<?php

use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::prefix('incident')->group(function () {
    Route::group(['middleware' => ['permission:read-incident']], function () {
        Route::get('index', [IncidentController::class, 'index'])
            ->name('incident.index');
        Route::get('search', [IncidentController::class, 'search'])
            ->name('incident.search');
        Route::get('downloadPDF/{id}', [IncidentController::class, 'downloadPDF'])
            ->name('incident.downloadPDF');
    });
    Route::group(['middleware' => ['permission:create-incident']], function () {
        Route::get('create', [IncidentController::class, 'create'])
            ->name('incident.create');
        Route::post('store', [IncidentController::class, 'store'])
            ->name('incident.store');
    });
    Route::group(['middleware' => ['permission:update-incident']], function () {
        Route::get('edit/{id}', [IncidentController::class, 'edit'])
            ->name('incident.edit');
        Route::post('update/{id}', [IncidentController::class, 'update'])
            ->name('incident.update');
    });
    Route::group(['middleware' => ['permission:delete-incident']], function () {
        Route::post('delete/{id}', [IncidentController::class, 'destroy'])
            ->name('incident.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [IncidentController::class, 'selection'])
        ->name('incident.selection');
});
