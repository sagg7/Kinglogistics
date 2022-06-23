<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\DriverLocationController;
use App\Http\Controllers\Api\DriverNotificationsController;
use App\Http\Controllers\Api\LoadController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\OriginController;
use App\Http\Controllers\Api\SafetyAdvicesController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\ShipperController;
use App\Http\Controllers\Api\TrailerController;
use App\Http\Controllers\Api\TruckController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json(['msg' => 'Unavailable']);
});

Route::post('/login', [AuthController::class, "login"]);
Route::post('forgot-password', [ForgotPasswordController::class, "sendResetLink"]);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, "logout"]);
});

Route::group([
    'middleware' => 'auth:sanctum'
], function () {

    Route::group([
        'prefix' => 'dashboard'
    ], function () {
        Route::get('', [DashboardController::class, 'appBootstrap']);
    });

    Route::group([
        'prefix' => 'profile'
    ], function () {
        Route::get('', [ProfileController::class, 'getProfile']);
    });

    Route::group([
        'prefix' => 'load',
    ], function () {
        Route::get('records', [LoadController::class, 'getRecords']);
        Route::get('get-active', [LoadController::class, 'getActive']);
        Route::get('pending-to-respond', [LoadController::class, 'getPendingToRespond']);
        Route::get('get-trips', [LoadController::class, 'getTrips']);

        Route::post('accept', [LoadController::class, 'accept']);
        Route::post('reject', [LoadController::class, 'reject']);
        Route::post('loading', [LoadController::class, 'loading']);
        Route::post('to-location', [LoadController::class, 'toLocation']);
        Route::post('arrived', [LoadController::class, 'arrived']);
        Route::post('unloading', [LoadController::class, 'unloading']);
        Route::post('finished', [LoadController::class, 'finished']);
        Route::post('multi-status', [LoadController::class, 'multiStatus']);
        Route::post('update-end-box', [LoadController::class, 'updateEndBox']);
        Route::post('store-load', [LoadController::class, 'storeLoad']);

        Route::get('get-load-types', [LoadController::class, 'getLoadTypes']);
        Route::get('get-origins', [OriginController::class, 'getOrigins']);
        Route::get('get-destinations', [DestinationController::class, 'getDestinations']);

    });

    Route::group([
        'prefix' => 'chat'
    ], function () {

        Route::post('user', [ChatController::class, 'sendMessageAsUser']);
        Route::post('driver', [ChatController::class, 'sendMessageAsDriver']);
        Route::get('history', [ChatController::class, 'getChatHistory']);

    });

    Route::group([
        'prefix' => 'safety'
    ], function () {
        Route::get('{id}', [SafetyAdvicesController::class, 'find']);
        Route::post('send-advice', [SafetyAdvicesController::class, 'sendAdvice']);
    });

    Route::group([
        'prefix' => 'location',
    ], function () {
        Route::post('update', [DriverLocationController::class, 'updateDriverLocation']);
    });

    Route::group([
        'prefix' => 'notifications'
    ], function () {
        Route::get('', [DriverNotificationsController::class, 'index']);
        Route::get('advices', [DriverNotificationsController::class, 'getSafetyAdvicesNotifications']);
        Route::get('unread-advices', [DriverNotificationsController::class, 'getUnreadSafetyAdvicesNotifications']);
        Route::get('advice/{id}', [DriverNotificationsController::class, 'getSafetyAdvice']);
        Route::post('mark-as-read', [DriverNotificationsController::class, 'markNotificationAsRead']);
    });

    Route::group([
        'prefix' => 'shift',
    ], function () {

        Route::get('create', [ShiftController::class, 'create']);
        Route::get('check-status', [ShiftController::class, 'checkStatus']);
        Route::post('start', [ShiftController::class, 'start']);
        Route::post('end', [ShiftController::class, 'end']);

        Route::get('get-active-truck', [TruckController::class, 'getActiveTruck']);
        Route::get('get-trucks', [TruckController::class, 'getTrucks']);
        Route::get('get-trailers', [TrailerController::class, 'getTrailers']);
    });

    Route::prefix('shipper')->group(function () {
        Route::get('get-shippers', [ShipperController::class, 'getShippers']);
    });

});

Route::post('broadcast/auth', function () {
    return App\Models\User::find(1);
});
