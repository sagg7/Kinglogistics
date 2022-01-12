<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::group(['middleware' => ['permission:read-staff']], function () {
        Route::get('index', [UserController::class, 'index'])
            ->name('user.index');
        Route::get('search', [UserController::class, 'search'])
            ->name('user.search');
    });
    Route::group(['middleware' => ['permission:create-staff']], function () {
        Route::get('create', [UserController::class, 'create'])
            ->name('user.create');
        Route::post('store', [UserController::class, 'store'])
            ->name('user.store');
    });
    Route::group(['middleware' => ['permission:update-staff']], function () {
        Route::get('edit/{id}', [UserController::class, 'edit'])
            ->name('user.edit');
        Route::post('update/{id}', [UserController::class, 'update'])
            ->name('user.update');
    });
    Route::group(['middleware' => ['permission:delete-staff']], function () {
        Route::post('delete/{id}', [UserController::class, 'destroy'])
            ->name('user.delete');
    });
    // LEAVE OUT SELECTION IN CASE IT IS USED IN OTHER AREAS WITH DIFFERENT PERMISSIONS
    Route::get('selection', [UserController::class, 'selection'])
        ->name('user.selection');
    Route::get('profile', [UserController::class, 'profile'])
        ->name('user.profile');
    Route::get('searchActive', [UserController::class, 'staffOnTurn'])
        ->name('user.searchActive');

    Route::group(['middleware' => ['permission:read-dispatch-schedule']], function () {
        Route::get('dispatchSchedule', [UserController::class, 'dispatchSchedule'])
            ->name('user.dispatchSchedule');
    });
    Route::group(['middleware' => ['permission:create-dispatch-schedule|update-dispatch-schedule']], function () {
        Route::post('storeDispatchSchedule', [UserController::class, 'storeDispatchSchedule'])
            ->name('user.storeDispatchSchedule');
    });
});
