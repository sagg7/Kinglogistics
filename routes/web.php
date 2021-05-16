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
    return view('auth.login');
})
    ->middleware('guest');

Route::get('/dashboard', function () {
    return view('dashboard');
})/*->middleware(['auth'])*/->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
    require __DIR__.'/web/users.php';
    require __DIR__.'/web/rentals.php';
    require __DIR__.'/web/carriers.php';
    require __DIR__.'/web/incidents.php';
    require __DIR__.'/web/notifications.php';
    require __DIR__.'/web/drivers.php';
    require __DIR__.'/web/trailers.php';
});

require __DIR__.'/auth.php';
