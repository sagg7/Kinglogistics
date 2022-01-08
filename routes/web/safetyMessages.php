<?php

use App\Http\Controllers\SafetyMessageController;
use Illuminate\Support\Facades\Route;

Route::prefix('safetyMessage')->group(function () {
    Route::group(['middleware' => ['permission:read-safety-messages']], function () {
        Route::get('index', [SafetyMessageController::class, 'index'])
            ->name('safetyMessage.index');
        Route::get('search', [SafetyMessageController::class, 'search'])
            ->name('safetyMessage.search');
        /*Route::get('show/{id}', [SafetyMessageController::class, 'show'])
            ->name('safetyMessage.show');*/
    });
    Route::group(['middleware' => ['permission:create-safety-messages']], function () {
        Route::get('create', [SafetyMessageController::class, 'create'])
            ->name('safetyMessage.create');
        Route::post('store', [SafetyMessageController::class, 'store'])
            ->name('safetyMessage.store');
    });
    Route::group(['middleware' => ['permission:update-safety-messages']], function () {
        Route::get('edit/{id}', [SafetyMessageController::class, 'edit'])
            ->name('safetyMessage.edit');
        Route::post('update/{id}', [SafetyMessageController::class, 'update'])
            ->name('safetyMessage.update');
    });
    Route::group(['middleware' => ['permission:delete-safety-messages']], function () {
        Route::post('delete/{id}', [SafetyMessageController::class, 'destroy'])
            ->name('safetyMessage.delete');
    });
});
