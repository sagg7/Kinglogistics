<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CarrierPaymentController;
use App\Http\Controllers\Carriers\DashboardController;
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
    require __DIR__.'/carriers/trailers.php';
    require __DIR__.'/carriers/zones.php';
    require __DIR__.'/carriers/shippers.php';
    require __DIR__.'/carriers/expenses.php';
    require __DIR__.'/carriers/paperwork.php';
    require __DIR__.'/carriers/reports.php';
    require __DIR__.'/web/trucks.php';
    require __DIR__.'/carriers/tracking.php';
    require __DIR__.'/web/dashboard.php';
    require __DIR__.'/carriers/trips.php';
    require __DIR__.'/carriers/accounting.php';
    require __DIR__.'/web/incidents.php';
    require __DIR__.'/carriers/jobOpportunities.php';
    require __DIR__.'/carriers/equipment.php';

    Route::get('carrier/payment/downloadPDF/{id}', [CarrierPaymentController::class, 'downloadPDF'])
        ->name('carrier.downloadPaymentPDF');

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])
            ->name('carrier.profile');
        Route::post('update/{id}/{profile?}', [CarrierController::class, 'update'])
            ->name('carrier.profile.update');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::view('broadcasting/carriers', 'test.broadcasting');
