<?php

use App\Http\Controllers\JobOpportunityController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobOpportunity')->group(function () {
    Route::get('index', [JobOpportunityController::class, 'index'])
        ->name('jobOpportunity.index');
    Route::get('create', [JobOpportunityController::class, 'create'])
        ->name('jobOpportunity.create');
    Route::post('store', [JobOpportunityController::class, 'store'])
        ->name('jobOpportunity.store');
    Route::get('store', [JobOpportunityController::class, 'store'])
        ->name('jobOpportunity.store');
    Route::get('search', [JobOpportunityController::class, 'search'])
        ->name('jobOpportunity.search');
    Route::get('edit/{id}', [JobOpportunityController::class, 'edit'])
        ->name('jobOpportunity.edit');
    Route::post('update/{id}', [JobOpportunityController::class, 'update'])
        ->name('jobOpportunity.update');
    Route::post('delete/{id}', [JobOpportunityController::class, 'destroy'])
        ->name('jobOpportunity.delete');
});
