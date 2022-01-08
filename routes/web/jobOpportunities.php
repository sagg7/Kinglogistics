<?php

use App\Http\Controllers\JobOpportunityController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobOpportunity')->group(function () {
    Route::group(['middleware' => ['permission:read-job-opportunity']], function () {
        Route::get('index', [JobOpportunityController::class, 'index'])
            ->name('jobOpportunity.index');
        Route::get('show/{id}', [JobOpportunityController::class, 'show'])
            ->name('jobOpportunity.show');
        Route::get('search', [JobOpportunityController::class, 'search'])
            ->name('jobOpportunity.search');
    });
    Route::group(['middleware' => ['permission:create-job-opportunity']], function () {
        Route::get('create', [JobOpportunityController::class, 'create'])
            ->name('jobOpportunity.create');
        Route::post('store', [JobOpportunityController::class, 'store'])
            ->name('jobOpportunity.store');
    });
    Route::group(['middleware' => ['permission:update-job-opportunity']], function () {
        Route::get('edit/{id}', [JobOpportunityController::class, 'edit'])
            ->name('jobOpportunity.edit');
        Route::post('update/{id}', [JobOpportunityController::class, 'update'])
            ->name('jobOpportunity.update');
    });
    Route::group(['middleware' => ['permission:delete-job-opportunity']], function () {
        Route::post('delete/{id}', [JobOpportunityController::class, 'destroy'])
            ->name('jobOpportunity.delete');
    });
});
