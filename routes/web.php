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
    Route::post('/moon/admin/addmoon', 'MoonsAdminController@storeMoon');
    Route::get('/moons/admin/display', 'MoonsAdminController@displayMoonsAdmin');
    Route::get('/moons/admin/updatemoon', 'MoonsAdminController@updateMoon');
    Route::post('/moons/admin/updatemoon', 'MoonsAdminController@storeUpdateMoon');
    Route::get('/moons/admin/journal', 'MoonsAdminController@showJournalEntries');

    //Wiki Controller display pages
    Route::get('/wiki/register', 'WikiController@displayRegister');
    Route::post('/wiki/register', 'WikiController@storeRegister');
    Route::get('/wiki/changepassword', 'WikiController@displayChangePassword');
    Route::post('/wiki/changepassword', 'WikiController@changePassword');

    //Admin Controller display pages
    Route::get('/admin/dashboard', 'AdminController@displayDashboard');
    Route::post('/admin/addRole', 'AdminController@addRole');
    Route::post('/admin/removeRole', 'AdminController@removeRole');
    Route::post('/admin/add/permission', 'AdminController@addPermission');
    Route::post('/admin/remove/user', 'AdminController@removeUser');
    Route::post('/admin/modify/role', 'AdminController@modifyRole');

    //Register Structures Controller display pages
    Route::get('/structures/register', 'RegisterStructureController@displayRegisterstructure');
    Route::post('/structures/register', 'RegisterStructureController@storeStructure');
    
    //Structure Admin Controller display pages
    Route::get('/structures/admin/dashboard', 'StructureAdminController@displayDashboard');
    Route::post('/structures/admin/add/taxratio', 'StructureAdminController@storeTaxRatio');
    Route::post('/structures/admin/update/taxratio', 'StructureAdminController@updateTaxRatio');

    //Structure Controller display pages
    Route::get('/structures/taxes/display', 'StructureController@displayTaxes');
    Route::get('/structures/admin/taxes/display', 'StructureController@chooseCorpTaxes');
    Route::get('/structures/admin/taxes/display/execute', 'StructureController@displayCorpTaxes');
    Route::get('/structures/admin/taxes/industry', 'StructureController@displayIndustryTaxes');
    Route::get('/structures/admin/taxes/reprocessing', 'StructureController@displayReprocessingTaxes');
    Route::get('/structures/admin/display', 'StructureController@displayAdminPanel');

    //Taxes Controller display pages
    Route::get('/taxes/display', 'TaxesController@displayTaxSummary');

    //Scopes Controller display pages
    Route::get('/scopes/select', 'EsiScopeController@displayScopes');
    Route::post('redirectToProvider', 'EsiScopeController@redirectToProvider');

    //Clone Saver display pages
    Route::get('/clones/register', 'CloneSaverController@displayRegister');
    Route::get('/clones/display', 'CloneSaverController@displayClones');
    Route::get('/clones/remove', 'CloneSaverController@displayRemove');
    Route::post('/clones/register', 'CloneSaverController@storeRegister');
    Route::post('/clones/remove', 'CloneSavercontroller@deleteRegister');
});

//Login display pages
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
