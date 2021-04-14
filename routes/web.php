<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();

Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::get('upgrade', function () {return view('pages.upgrade');})->name('upgrade');
	 Route::get('map', function () {return view('pages.maps');})->name('map');
	 Route::get('icons', function () {return view('pages.icons');})->name('icons');
	 Route::get('table-list', function () {return view('pages.tables');})->name('table');
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);

    Route::get('leased', function () {return view('leased.list');})->name('Leased');
    Route::get('leased/create', function () {return view('leased.create');})->name('Leased Create');
    Route::get('trailer', function () {return view('chassis.list');})->name('Chassis');
    Route::get('trailer/create', function () {return view('chassis.create');})->name('Chassis Create');
    Route::post('leased/store', ['as' => 'leased.store', 'uses' => 'App\Http\Controllers\LeasedController@store']);
    Route::post('trailer/store', ['as' => 'chassis.store', 'uses' => 'App\Http\Controllers\TrailerController@store']);
    Route::post('rent/store', ['as' => 'rent.store', 'uses' => 'App\Http\Controllers\RentalsController@Store']);
    Route::get('rent/create/{id}', ['as' => 'rent.create', 'uses' => 'App\Http\Controllers\RentalsController@create']);
    Route::get('getLeased', ['as' => 'getLeased', 'uses' => 'App\Http\Controllers\LeasedController@getLeased']);

});
