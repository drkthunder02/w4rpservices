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
})->name('/');

//Login display pages
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

//Dashboard Controller Display pages
Route::get('/dashboard', 'DashboardController@index');

//Moon Controller display pages
Route::get('/moons/display', 'MoonsController@displayMoons');
Route::get('/moons/addmoon', 'MoonsController@addMoon');
Route::get('/moons/updatemoon', 'MoonsController@updateMoon');
Route::get('/moons/display/worth', 'MoonsController@displayTotalWorthForm');
Route::get('/moons/admin/display', 'MoonsController@displayMoonsAdmin');
//Moon Controller POST requests
Route::post('storeMoon', 'MoonsController@storeMoon');
Route::post('storeUpdateMoon', 'MoonsController@storeUpdateMoon');
Route::post('displayTotalWorth', 'MoonsController@displayTotalWorth');
 
//Wiki Controller display pages
Route::get('/wiki/register', 'WikiController@displayRegister');
Route::get('/wiki/changepassword', 'WikiController@displayChangePassword');
//Wiki Controller POST requests
Route::post('storeRegister', 'WikiController@storeRegister');
Route::post('changePassword', 'WikiController@changePassword');

//Scopes Controller display pages
Route::get('/scopes/select', 'EsiScopeController@displayScopes');
Route::post('redirectToProvider', 'EsiScopeController@redirectToProvider');

//Fleet Controller display pages
Route::get('/fleets/display', 'FleetsController@displayFleets');
Route::get('/fleets/register', 'FleetsController@displayRegisterFleet');
Route::get('/fleets/{fleet_id}/addpilot/{id}', 'FleetsController@addPilot')->name('addpilot');
Route::get('/fleets/{fleet_id}/delete', 'FleetsController@deleteFleet')->name('deletefleet');
Route::post('/fleets/registerFleet', 'FleetsController@registerFleet');

//Admin Controller display pages
Route::get('/admin/dashboard', 'AdminController@displayDashboard');
Route::post('/admin/addRole', 'AdminController@addRole');
Route::post('/admin/removeRole', 'AdminController@removeRole');