<?php

use App\Http\Controllers\SafetyMessageController;
use Illuminate\Support\Facades\Route;

Route::prefix('safetyMessage')->group(function () {
    Route::get('index', [SafetyMessageController::class, 'index'])
        ->name('safetyMessage.index');
    Route::get('create', [SafetyMessageController::class, 'create'])
        ->name('safetyMessage.create');
    Route::post('store', [SafetyMessageController::class, 'store'])
        ->name('safetyMessage.store');
    Route::get('store', [SafetyMessageController::class, 'store'])
        ->name('safetyMessage.store');
    Route::get('search', [SafetyMessageController::class, 'search'])
        ->name('safetyMessage.search');
    /*Route::get('show/{id}', [SafetyMessageController::class, 'show'])
        ->name('safetyMessage.show');*/
    Route::get('edit/{id}', [SafetyMessageController::class, 'edit'])
        ->name('safetyMessage.edit');
    Route::post('update/{id}', [SafetyMessageController::class, 'update'])
        ->name('safetyMessage.update');
    Route::post('delete/{id}', [SafetyMessageController::class, 'destroy'])
        ->name('safetyMessage.delete');
});
