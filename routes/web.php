<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BrokerController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})
    ->middleware('guest');

Route::group(['middleware' => 'auth'], function () {
    Route::get('expired', [BrokerController::class, 'expired'])
        ->name('company.expired');
    Route::group(['middleware' => 'broker'], function () {
        require __DIR__.'/web/dashboard.php';
        require __DIR__.'/web/users.php';
        require __DIR__.'/web/rentals.php';
        require __DIR__.'/web/shippers.php';
        require __DIR__.'/web/carriers.php';
        require __DIR__.'/web/incidents.php';
        require __DIR__.'/web/incidentTypes.php';
        require __DIR__.'/web/trips.php';
        require __DIR__.'/web/loads.php';
        require __DIR__.'/web/loadTypes.php';
        require __DIR__.'/web/safetyMessages.php';
        require __DIR__.'/web/drivers.php';
        require __DIR__.'/web/trucks.php';
        require __DIR__.'/web/trailers.php';
        require __DIR__.'/web/trailerTypes.php';
        require __DIR__.'/web/zones.php';
        require __DIR__.'/web/paperwork.php';
        require __DIR__.'/web/jobOpportunities.php';
        require __DIR__.'/web/charges.php';
        require __DIR__.'/web/loans.php';
        require __DIR__.'/web/rateGroups.php';
        require __DIR__.'/web/rates.php';
        require __DIR__.'/web/chat.php';
        require __DIR__.'/web/s3storage.php';
        require __DIR__.'/web/tracking.php';
        require __DIR__.'/web/expenses.php';
        require __DIR__.'/web/incomes.php';
        require __DIR__.'/web/brokers.php';
        require __DIR__.'/web/bonuses.php';
        require __DIR__.'/web/reports.php';
        require __DIR__.'/common/roadLoad.php';
        require __DIR__.'/common/cities.php';
    });
});

require __DIR__ . '/auth.php';

Route::view('broadcasting/test', 'test.broadcasting');
