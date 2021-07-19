<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverLocationController;
use App\Http\Controllers\Api\DriverNotificationsController;
use App\Http\Controllers\Api\LoadController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\SafetyAdvicesController;
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
        'prefix' => 'profile'
    ], function () {
        Route::get('', [ProfileController::class, 'getProfile']);
    });

    Route::group([
        'prefix' => 'loads',
    ], function () {
        Route::get('', [LoadController::class, 'index']);
    });

    Route::group([
        'prefix' => 'chat'
    ], function () {

        Route::post('shipper', [ChatController::class, 'sendMessageAsShipper']);
        Route::post('driver', [ChatController::class, 'sendMessageAsDriver']);
        Route::get('conversation', [ChatController::class, 'getConversation']);

    });

    Route::group([
        'prefix' => 'safety'
    ], function () {
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
        Route::get('advices', [DriverNotificationsController::class, 'getSafetyAdvicesList']);
        Route::post('mark-as-read', [DriverNotificationsController::class, 'markNotificationAsRead']);
    });

});
