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

Route::get('/login', 'LoginController@redirectToProvider');
Route::get('/callback', 'LoginController@handleProviderCallback');

Route::get('/dashboard', 'DashboardController@index');
Route::get('/dashboard/addmoon', 'DashboardController@addMoon');
Route::get('/dashboard/updatemoon', 'DashboardController@updateMoon');
Route::get('/dashboard/moons', 'DashboardController@displayMoons');
Route::get('/dashboard/profile', 'DashboardController@profile');
//Route::get('/callback', 'DashboardController@callback');
Route::get('/dashboard/moonmine/display', 'MoonsController@moonminedisplay');
Route::post('moonmine', 'MoonsController@moonmine');

Route::post('moons', 'MoonsController@addMoon');
