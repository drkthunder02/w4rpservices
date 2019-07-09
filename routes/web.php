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
    /**
     * Dashboard Controller Display pages
     */
    Route::get('/dashboard', 'Dashboard\DashboardController@index');

    /**
     * Moon Controller display pages
     */
    Route::get('/moons/display', 'Moons\MoonsController@displayMoons');
    Route::get('/moons/display/form/worth', 'Moons\MoonsController@displayTotalWorthForm');
    Route::post('/moons/worth', 'Moons\MoonsController@displayTotalWorth');

    /**
     * Moon Admin Controller display pages
     */
    Route::get('/moons/admin/display', 'Moons\MoonsAdminController@displayMoonsAdmin');
    Route::get('/moons/admin/updatemoon', 'Moons\MoonsAdminController@updateMoon');
    Route::post('/moons/admin/updatemoon', 'Moons\MoonsAdminController@storeUpdateMoon');
    Route::get('/moons/admin/journal', 'Moons\MoonsAdminController@showJournalEntries');
    Route::post('/moons/admin/display', 'Moons\MoonsAdminController@storeMoonRemoval');
    Route::get('/moons/logistics/display', 'Moons\MoonsAdminController@displayMoonsLogistics');

    /**
     * Wiki Controller display pages
     */
    Route::get('/wiki/register', 'Wiki\WikiController@displayRegister');
    Route::post('/wiki/register', 'Wiki\WikiController@storeRegister');
    Route::get('/wiki/changepassword', 'Wiki\WikiController@displayChangePassword');
    Route::post('/wiki/changepassword', 'Wiki\WikiController@changePassword');
    Route::post('/wiki/purge', 'Wiki\WikiController@purgeUsers');

    /**
     * Admin Controller display pages
     */
    Route::get('/admin/dashboard', 'Dashboard\AdminController@displayDashboard');
    Route::post('/admin/add/role', 'Dashboard\AdminController@addRole');
    Route::post('/admin/remove/role', 'Dashboard\AdminController@removeRole');
    Route::post('/admin/add/permission', 'Dashboard\AdminController@addPermission');
    Route::post('/admin/remove/user', 'Dashboard\AdminController@removeUser');
    Route::post('/admin/modify/user/display', 'Dashboard\AdminController@displayModifyUser');
    Route::post('/admin/modify/user', 'Dashboard\AdminController@modifyUser');
    Route::post('/admin/add/allowedlogin', 'Dashboard\AdminController@addAllowedLogin');
    Route::post('/admin/rmoeve/allowedlogin', 'Dashboard\AdminController@removeAllowedLogin');

    /**
     * Scopes Controller display pages
     */
    Route::get('/scopes/select', 'Auth\EsiScopeController@displayScopes');
    Route::post('redirectToProvider', 'Auth\EsiScopeController@redirectToProvider');

    /**
     * Contract Controller display pages
     */
    Route::get('/contracts/display/all', 'Contracts\ContractController@displayContracts');
    Route::get('/contracts/display/public', 'Contracts\ContractController@displayPublicContracts');
    Route::get('/contracts/display/private', 'Contracts\ContractController@displayPrivateContracts');
    Route::get('/contracts/display/newbid/{id}', 'Contracts\ContractController@displayNewBid');
    Route::get('/contracts/modify/bid/{id}', 'Contracts\ContractController@displayModifyBid');
    Route::get('/contracts/display/bids/{id}', 'Contracts\ContractController@displayBids');
    Route::get('/contracts/delete/bid/{id}', 'Contracts\ContractController@deleteBid');
    Route::post('/contracts/modify/bid', 'Contracts\ContractController@modifyBid');
    Route::post('/contracts/bids/store', 'Contracts\ContractController@storeBid');

    /**
     * Contract Admin Controller display pages
     */
    Route::get('/contracts/admin/display', 'Contracts\ContractAdminController@displayContractDashboard');
    Route::get('/contracts/admin/past', 'Contracts\ContractAdminController@displayPastContracts');
    Route::get('/contracts/admin/new', 'Contracts\ContractAdminController@displayNewContract');
    Route::post('/contracts/admin/new', 'Contracts\ContractAdminController@storeNewContract');
    Route::post('/contracts/admin/store', 'Contracts\ContractAdminController@storeAcceptContract');
    Route::get('/contracts/admin/delete/{id}', 'Contracts\ContractAdminController@deleteContract');
    Route::get('/contracts/admin/end/{id}', 'Contracts\ContractAdminController@displayEndContract');
    Route::post('/contracts/admin/end', 'Contracts\ContractAdminController@storeEndContract');

    /**
     * SRP Controller display pages
     */
    Route::get('/srp/form/display', 'SRP\SRPController@displaySrpForm');
    Route::post('/srp/form/display', 'SRP\SRPController@storeSRPFile');

    /**
     * SRP Admin Controller display pages
     */
    Route::get('/srp/admin/display', 'SRP\SRPAdminController@displaySRPRequests');
    Route::post('/srp/admin/process', 'SRP\SRPAdminController@processSRPRequest');
    Route::get('/srp/admin/statistics', 'SRP\SRPAdminController@displayStatistics');
    Route::get('/srp/admin/costcodes/display', 'SRP\SRPAdminController@displayCostCodes');
    Route::post('/srp/admin/costcodes/add', 'SRP\SRPAdminController@addCostCode');
    Route::post('/srp/admin/costcodes/modify', 'SRP\SRPAdminController@modifyCostCodes');
    
    /**
     * Logistics Controller display pages
     */
    Route::get('/logistics/courier/form', 'Logistics\LogisticsController@displayContractForm');
    Route::post('/logistics/courier/form', 'Logistics\LogisticsController@displayContractDetails');
    Route::get('/logistics/contracts/display', 'Logistics\LogisticsController@displayLogisticsContracts');
    Route::get('/logistics/fuel/structures', 'Fuel\FuelController@displayStructures');
});

/**
 * Login Display pages
 */
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
