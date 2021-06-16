<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
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
    'prefix' => 'profile',
    'middleware' => 'auth:drivers'
], function () {

    Route::get('', [ProfileController::class, 'getProfile']);

});
