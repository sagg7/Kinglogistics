<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\Carriers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:carrier')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })
        ->middleware('guest:carrier');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest:carrier')
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest:carrier')
        ->name('login');
});

Route::middleware('auth:carrier')->group(function () {
    require __DIR__.'/carriers/drivers.php';
    require __DIR__.'/carriers/turns.php';
    require __DIR__.'/carriers/trucks.php';
    require __DIR__.'/carriers/trailers.php';
    require __DIR__.'/carriers/zones.php';
    require __DIR__.'/carriers/shippers.php';

    Route::get('/dashboard', function () {
        return view('subdomains.carriers.dashboard');
    })
        ->name('dashboard');


    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])
            ->name('driver.profile');
        Route::post('update/{id}', [CarrierController::class, 'update'])
            ->name('profile.update');
    });


    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
