<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:client')->group(function () {
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

Route::middleware('auth:client')->group(function () {
    Route::get('/dashboard', function () {
        return view('subdomains.clients.dashboard');
    })
        ->name('dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
