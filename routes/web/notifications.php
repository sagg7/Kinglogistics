<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('notification')->group(function () {
    Route::get('index', [NotificationController::class, 'index'])
        ->name('notification.index');
    Route::get('create', [NotificationController::class, 'create'])
        ->name('notification.create');
    Route::post('store', [NotificationController::class, 'store'])
        ->name('notification.store');
    Route::get('search', [NotificationController::class, 'search'])
        ->name('notification.search');
    Route::get('edit/{id}', [NotificationController::class, 'edit'])
        ->name('notification.edit');
    Route::post('update/{id}', [NotificationController::class, 'update'])
        ->name('notification.update');
    Route::post('delete/{id}', [NotificationController::class, 'destroy'])
        ->name('notification.delete');
});
