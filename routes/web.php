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

Route::group(['middleware' => ['auth']], function(){
    //Dashboard Controller Display pages
    Route::get('/dashboard', 'DashboardController@index');

    //Moon Controller display pages
    Route::get('/moons/display', 'MoonsController@displayMoons');
    Route::get('/moons/display/worth', 'MoonsController@displayTotalWorthForm');
    Route::post('displayTotalWorth', 'MoonsController@displayTotalWorth');

    //Moon Admin Controller display pages
    Route::get('/moons/admin/addmoon', 'MoonsAdminController@addMoon');
    Route::post('/moons/admin/storeMoon', 'MoonsAdminController@storeMoon');
    Route::get('/moons/admin/display', 'MoonsAdminController@displayMoonsAdmin');
    Route::post('/moons/admin/storeUpdateMoon', 'MoonsAdminController@storeUpdateMoon');
    Route::get('/moons/admin/updatemoon', 'MoonsAdminController@updateMoon');
    Route::get('/moons/admin/journal', 'MoonsAdminController@showJournalEntries');

    //Wiki Controller display pages
    Route::get('/wiki/register', 'WikiController@displayRegister');
    Route::get('/wiki/changepassword', 'WikiController@displayChangePassword');
    Route::post('/wiki/storeRegister', 'WikiController@storeRegister');
    Route::post('/wiki/changePassword', 'WikiController@changePassword');

    //Fleet Controller display pages
    Route::get('/fleets/display', 'FleetsController@displayFleets');
    Route::get('/fleets/register', 'FleetsController@displayRegisterFleet');
    Route::get('/fleets/{fleet_id}/addpilot/{id}', 'FleetsController@addPilot')->name('addpilot');
    Route::get('/fleets/{fleet_id}/addpilot/{name}', 'Fleetscontroller@addPilotName');
    Route::get('/fleets/{fleet_id}/delete', 'FleetsController@deleteFleet')->name('deletefleet');
    Route::post('/fleets/registerFleet', 'FleetsController@registerFleet');

    //Admin Controller display pages
    Route::get('/admin/dashboard', 'AdminController@displayDashboard');
    Route::post('/admin/addRole', 'AdminController@addRole');
    Route::post('/admin/removeRole', 'AdminController@removeRole');
    Route::post('/admin/addPermission', 'AdminController@addPermission');

    //Register Structures Controller display pages
    Route::get('/structures/register', 'RegisterStructureController@displayRegisterstructure');
    Route::post('/structures/store', 'RegisterStructureController@storeStructure');
    //Structure Controller display pages
    Route::get('/structures/taxes/display', 'StructureController@displayTaxes');
    Route::get('/structures/admin/taxes/display', 'StructureController@chooseCorpTaxes');
    Route::get('/structures/admin/taxes/display/execute', 'StructureController@displayCorpTaxes');

    //Scopes Controller display pages
    Route::get('/scopes/select', 'EsiScopeController@displayScopes');
    Route::post('redirectToProvider', 'EsiScopeController@redirectToProvider');
});

//Login display pages
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

//AJAX Controller display pages
Route::get('ajax',function() {
    return view('/ajax/message');
 });
 Route::post('/getmsg','AjaxController@index');