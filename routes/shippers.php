<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ShipperController;
use App\Http\Controllers\Shippers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:shipper')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })
        ->middleware('guest:shipper');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest:shipper')
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest:shipper')
        ->name('login');
});

Route::middleware('auth:shipper')->group(function () {
    require __DIR__.'/web/dashboard.php';
    require __DIR__.'/web/trips.php';
    require __DIR__.'/shippers/loads.php';
    require __DIR__.'/web/loadTypes.php';
    require __DIR__.'/shippers/zones.php';
    require __DIR__.'/shippers/tracking.php';
    require __DIR__.'/web/drivers.php';

    Route::get('/dashboard', function () {
        return view('subdomains.shippers.dashboard');
    })
        ->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])
            ->name('shipper.profile');
        Route::post('update/{id}/{profile?}', [ShipperController::class, 'update'])
            ->name('shipper.profile.update');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
