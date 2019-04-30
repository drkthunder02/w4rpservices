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
    Route::post('/moons/admin/display', 'MoonsAdminController@updateMoonPaid');

    //Wiki Controller display pages
    Route::get('/wiki/register', 'WikiController@displayRegister');
    Route::post('/wiki/register', 'WikiController@storeRegister');
    Route::get('/wiki/changepassword', 'WikiController@displayChangePassword');
    Route::post('/wiki/changepassword', 'WikiController@changePassword');

    //Admin Controller display pages
    Route::get('/admin/dashboard', 'AdminController@displayDashboard');
    Route::post('/admin/add/role', 'AdminController@addRole');
    Route::post('/admin/remove/role', 'AdminController@removeRole');
    Route::post('/admin/add/permission', 'AdminController@addPermission');
    Route::post('/admin/remove/user', 'AdminController@removeUser');
    Route::post('/admin/modify/role', 'AdminController@modifyRole');
    Route::post('/admin/add/allowedlogin', 'AdminController@addAllowedLogin');
    Route::post('/admin/rmoeve/allowedlogin', 'AdminController@removeAllowedLogin');

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

    //Scopes Controller display pages
    Route::get('/scopes/select', 'EsiScopeController@displayScopes');
    Route::post('redirectToProvider', 'EsiScopeController@redirectToProvider');

    //Contract Controller display pages
    Route::get('/contracts/display/all', 'ContractController@displayContracts');
    Route::get('/contracts/display/public', 'ContractController@displayPublicContracts');
    Route::get('/contracts/display/private', 'ContractController@displayPrivateContracts');
    Route::get('/contracts/display/newbid/{id}', 'ContractController@displayNewBid');
    Route::get('/contracts/modify/bid/{id}', 'ContractController@displayModifyBid');
    Route::get('/contracts/display/bids/{id}', 'ContractController@displayBids');
    Route::get('/contracts/delete/bid/{id}', 'ContractController@deleteBid');
    Route::post('/contracts/modify/bid', 'ContractController@modifyBid');
    Route::post('/contracts/bids/store', 'ContractController@storeBid');

    //Contract Admin Controller display pages
    Route::get('/contracts/admin/display', 'ContractAdminController@displayContractDashboard');
    Route::get('/contracts/admin/new', 'ContractAdminController@displayNewContract');
    Route::post('/contracts/admin/new', 'ContractAdminController@storeNewContract');
    Route::post('/contracts/admin/store', 'ContractAdminController@storeAcceptContract');
    Route::get('/contracts/admin/delete/{id}', 'ContractAdminController@deleteContract');
    Route::get('/contracts/admin/end/{id}', 'ContractAdminController@displayEndContract');
    
});

//Login display pages
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
