<?php

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
//Login display pages
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('/logout', 'Auth\LoginController@logout');
//Dashboard Controller Display pages
Route::get('/dashboard', 'DashboardController@index');

//Moon Controller display pages
Route::get('/moons/display', 'MoonsController@displayMoons');
Route::get('/moons/addmoon', 'MoonsController@addMoon');
Route::get('/moons/updatemoon', 'MoonsController@updateMoon');
//Moon Controller POST requests
Route::post('storeMoon', 'MoonsController@addMoon');
Route::post('storeUpdateMoon', 'MoonsController@storeUpdateMoon');

//Wiki Controller display pages
Route::get('/wiki/register', 'WikiController@displayRegister');
Route::get('/wiki/changepassword', 'WikiController@displayChangePassword');
//Wiki Controller POST requests
Route::post('storeRegister', 'WikiController@storeRegister');
Route::post('changePassword', 'WikiController@changePassword');

//Finance Controller display pages
Route::get('/finances/login', 'FinancesController@redirectToProvider');
Route::get('/finances/display/wallet', 'FinancesController@displayWallet');