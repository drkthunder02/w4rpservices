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
    if(Auth::check()) {
        return redirect('/dashboard');
    }

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
    Route::post('/moon/admin/addmoon', 'MoonsAdminController@storemoon');
    Route::get('/moons/admin/display', 'MoonsAdminController@displayMoonsAdmin');
    Route::get('/moons/admin/updatemoon', 'MoonsAdminController@updateMoon');
    Route::post('/moons/admin/updatemoon', 'MoonsAdminController@storeUpdateMoon');
    Route::get('/moons/admin/journal', 'MoonsAdminController@showJournalEntries');

    //Wiki Controller display pages
    Route::get('/wiki/register', 'WikiController@displayRegister');
    Route::post('/wiki/register', 'WikiController@storeRegister');
    Route::get('/wiki/changepassword', 'WikiController@displayChangePassword');
    Route::post('/wiki/changepassword', 'WikiController@changePassword');

    //Fleet Controller display pages
    Route::get('/fleets/display', 'FleetsController@displayFleets');
    Route::get('/fleets/register', 'FleetsController@displayRegisterFleet');
    Route::post('/fleets/register', 'Fleetscontroller@registerFleet');
    Route::get('/fleets/{fleet_id}/addpilot/{id}', 'FleetsController@addPilot')->name('addpilot');
    Route::get('/fleets/{fleet_id}/addpilot/{name}', 'Fleetscontroller@addPilotName');
    Route::get('/fleets/{fleet_id}/delete', 'FleetsController@deleteFleet')->name('deletefleet');

    //Admin Controller display pages
    Route::get('/admin/dashboard', 'AdminController@displayDashboard');
    Route::post('/admin/addRole', 'AdminController@addRole');
    Route::post('/admin/removeRole', 'AdminController@removeRole');
    Route::post('/admin/addPermission', 'AdminController@addPermission');

    //Register Structures Controller display pages
    Route::get('/structures/register', 'RegisterStructureController@displayRegisterstructure');
    Route::post('/structures/register', 'RegisterstructureController@storeStructure');
    //Structure Controller display pages
    Route::get('/structures/taxes/display', 'StructureController@displayTaxes');
    Route::get('/structures/admin/taxes/display', 'StructureController@chooseCorpTaxes');
    Route::get('/structures/admin/taxes/display/execute', 'StructureController@displayCorpTaxes');
    Route::get('/structures/admin/taxes/industry', 'StructureController@displayIndustryTaxes');
    Route::get('/structures/admin/taxes/reprocessing', 'StructureController@displayReprocessingTaxes');

    //Scopes Controller display pages
    Route::get('/scopes/select', 'EsiScopeController@displayScopes');
    Route::post('redirectToProvider', 'EsiScopeController@redirectToProvider');

    //Jump Bridge Controller display pages
    Route::get('/jumpbridges/overall', 'JumpBridgeController@displayAll');
    Route::get('/jumpbridges/corps', 'JumpBridgeController@displayCorpUsage');
    Route::post('/jumpbridges/getcorps', 'JumpBridgeController@ajaxCorpUsage');
    Route::get('/jumpbridges/structures', 'JumpBridgeController@displayStructureUsage');
    Route::get('/jumpbridges/getstructures', 'JumpBridgeController@ajaxStructureUsage');

    //Help Desk Controller display pages
    Route::get('/helpdesk/tickets', 'HelpDeskController@displayMyTickets');
    Route::get('/helpdesk/tickets/edit', 'HelpDeskController@editTicket');
    Route::get('/helpdesk/tickets/new', 'HelpDeskController@displayNewTicket');
    Route::post('/helpdesk/tickets/new', 'HelpDeskController@storeTicket');

    //Finances Controller display pages
    Route::get('/finances/admin', 'FinancesController@displayFinances');
});

//Login display pages
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
