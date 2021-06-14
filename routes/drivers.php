<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Carriers\DriverController;
use App\Http\Controllers\Drivers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:driver')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })
        ->middleware('guest:driver');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest:driver')
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest:driver')
        ->name('login');
});

Route::middleware('auth:driver')->group(function () {
    require __DIR__.'/drivers/paperwork.php';
    require __DIR__.'/drivers/incidents.php';

    Route::get('/dashboard', function () {
        return view('subdomains.drivers.dashboard');
    })
        ->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])
            ->name('driver.profile');
        Route::post('update/{id}', [DriverController::class, 'update'])
            ->name('profile.update');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
