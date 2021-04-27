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
//Leased
    Route::get('leased', function () {return view('leased.list');})->name('Leased');
    Route::get('leased/create', function () {return view('leased.create');})->name('Leased Create');
    Route::post('leased/store', ['as' => 'leased.store', 'uses' => 'App\Http\Controllers\LeasedController@store']);
    Route::get('getLeased', ['as' => 'getLeased', 'uses' => 'App\Http\Controllers\LeasedController@getLeased']);

//Trailers
    Route::get('trailers', function () {return view('trailer.list');})->name('Trailers');
    Route::get('trailer/create', ['as' => 'trailer.create', 'uses' => 'App\Http\Controllers\TrailerController@create']);
    Route::post('trailer/store', ['as' => 'trailer.store', 'uses' => 'App\Http\Controllers\TrailerController@store']);

//Drivers
    Route::get('driver/create', ['as' => 'driver.create', 'uses' => 'App\Http\Controllers\LeasedController@createDriver']);
    Route::post('driver/store', ['as' => 'driver.store', 'uses' => 'App\Http\Controllers\LeasedController@storeDriver']);

//Rentals
    Route::post('rental/store', ['as' => 'rental.store', 'uses' => 'App\Http\Controllers\RentalsController@store']);
    Route::get('rental/create/{id}', ['as' => 'rental.create', 'uses' => 'App\Http\Controllers\RentalsController@create']);
    Route::post('inspection/store', ['as' => 'inspection.store', 'uses' => 'App\Http\Controllers\RentalsController@storeInspection']);
    Route::get('inspection/create/{id}', ['as' => 'rental.create', 'uses' => 'App\Http\Controllers\RentalsController@createInspection']);
    Route::post('rental/uploadPhoto', ['as' => 'rental.uploadPhoto', 'uses' => 'App\Http\Controllers\RentalsController@uploadPhoto']);
    Route::get('rentals', function () {return view('rentals.list');})->name('Rentals');
    Route::get('getRented', ['as' => 'getRented', 'uses' => 'App\Http\Controllers\RentalsController@getRented']);

    Route::get('endInspection/create/{id}', ['as' => 'rental.createEndRental', 'uses' => 'App\Http\Controllers\RentalsController@createEndRental']);
    Route::post('endRental', ['as' => 'rental.end', 'uses' => 'App\Http\Controllers\RentalsController@storeEndRental']);
    Route::post('rental/destroy/{id}', ['as' => 'rental.destroy', 'uses' => 'App\Http\Controllers\RentalsController@destroy']);


});
