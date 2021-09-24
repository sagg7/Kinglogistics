<?php

use App\Http\Controllers\JobOpportunityController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobOpportunity')->group(function () {
    Route::get('index', [JobOpportunityController::class, 'index'])
        ->name('jobOpportunity.index');
    Route::get('show/{id}', [JobOpportunityController::class, 'show'])
        ->name('jobOpportunity.show');
    Route::get('search', [JobOpportunityController::class, 'search'])
        ->name('jobOpportunity.search');
});
