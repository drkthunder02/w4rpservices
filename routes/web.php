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

    return view('login');
})->name('login');

/**
 * Login Display pages
 */
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/altlogin', 'Auth\LoginController@redirectToProviderAlt')->name('altlogin');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function(){
    /**
     * Admin Controller display pages
     */
    Route::get('/admin/dashboard/users', 'Dashboard\AdminController@displayUsersPaginated');
    Route::post('/admin/dashboard/users', 'Dashboard\AdminController@searchUsers');
    Route::get('/admin/dashboard/taxes', 'Dashboard\AdminController@displayTaxes');
    Route::get('/admin/dashboard/logins', 'Dashboard\AdminController@displayAllowedLogins');
    Route::post('/admin/add/role', 'Dashboard\AdminController@addRole');
    Route::post('/admin/remove/role', 'Dashboard\AdminController@removeRole');
    Route::post('/admin/add/permission', 'Dashboard\AdminController@addPermission');
    Route::post('/admin/modify/role', 'Dashboard\AdminController@modifyRole');
    Route::post('/admin/remove/user', 'Dashboard\AdminController@removeUser');
    Route::post('/admin/modify/user/display', 'Dashboard\AdminController@displayModifyUser');
    Route::post('/admin/add/allowedlogin', 'Dashboard\AdminController@addAllowedLogin');
    Route::post('/admin/rmoeve/allowedlogin', 'Dashboard\AdminController@removeAllowedLogin');
    Route::get('/admin/dashboard/journal', 'Dashboard\AdminController@showJournalEntries');
    Route::get('/admin/dashboard/test', 'Dashboard\AdminController@displayTestAdminDashboard');
    Route::get('/admin/dashboard', 'Dashboard\AdminDashboardController@displayAdminDashboard');

    /**
     * Blacklist Controller display pages
     */
    Route::get('/blacklist/display', 'Blacklist\BlacklistController@DisplayBlacklist');
    Route::get('/blacklist/display/add', 'Blacklist\BlacklistController@DisplayAddToBlacklist');
    Route::get('/blacklist/display/remove', 'Blacklist\BlacklistController@DisplayRemoveFromBlacklist');
    Route::get('/blacklist/display/search', 'Blacklist\BlacklistController@DisplaySearch');
    Route::post('/blacklist/add', 'Blacklist\BlacklistController@AddToBlacklist');
    Route::post('/blacklist/remove', 'Blacklist\BlacklistController@RemoveFromBlacklist');
    Route::post('/blacklist/search', 'Blacklist\BlacklistController@SearchInBlacklist');
    
    /**
     * Dashboard Controller Display pages
     */
    Route::get('/dashboard', 'Dashboard\DashboardController@index');
    Route::post('/dashboard/alt/delete', 'Dashboard\DashboardController@removeAlt');
    Route::get('/profile', 'Dashboard\DashboardController@profile');

    /**
     * Fuel Controller display pages
     */
    Route::get('/logistics/fuel/structures', 'Logistics\FuelController@displayStructures');

    /**
     * Mining Moon Tax display pages
     */
    //Route::get('/mining/display', 'Mining\MiningsController@displayMiningTax');

    /**
     * Moon Ledger Controller display pages
     */
    Route::get('/moons/ledger/display/moons', 'Moons\MoonLedgerController@displayMoonLedger');

    /**
     * Scopes Controller display pages
     */
    Route::get('/scopes/select', 'Auth\EsiScopeController@displayScopes');
    Route::post('redirectToProvider', 'Auth\EsiScopeController@redirectToProvider');

    /**
     * SRP Controller display pages
     */
    Route::get('/srp/form/display', 'SRP\SRPController@displaySrpForm');
    Route::post('/srp/form/display', 'SRP\SRPController@storeSRPFile');
    Route::get('/srp/display/costcodes', 'SRP\SRPController@displayPayoutAmounts');

    /**
     * SRP Admin Controller display pages
     */
    Route::get('/srp/admin/display', 'SRP\SRPAdminController@displaySRPRequests');
    Route::post('/srp/admin/process', 'SRP\SRPAdminController@processSRPRequest');
    Route::get('/srp/admin/statistics', 'SRP\SRPAdminController@displayStatistics');
    Route::get('/srp/admin/costcodes/display', 'SRP\SRPAdminController@displayCostCodes');
    Route::get('/srp/admin/costcodes/add', 'SRP\SRPAdminController@displayAddCostCode');
    Route::post('/srp/admin/costcodes/add', 'SRP\SRPAdminController@addCostCode');
    Route::post('/srp/admin/costcodes/modify', 'SRP\SRPAdminController@modifyCostCodes');
    Route::get('/srp/admin/display/history', 'SRP\SRPAdminController@displayHistory');
    Route::get('/srp/admin/update/shiptype/{id}/{value}', 'SRP\SRPAdminController@updateShipType');
    Route::get('/srp/admin/update/lossvalue/{id}/{value}', 'SRP\SRPAdminController@updateLossValue');


    /**
     * Supply Chain Contracts Controller display pages
     */
    Route::get('/supplychain/dashboard', 'Contracts\SupplyChainController@displaySupplyChainDashboard');
    Route::get('/supplychain/my/dashboard', 'Contracts\SupplyChainController@displayMySupplyChainDashboard');
    Route::get('/supplychain/contracts/new', 'Contracts\SupplyChainController@displayNewSupplyChainContract');
    Route::post('/supplychain/contracts/new', 'Contracts\SupplyChainController@storeNewSupplyChainContract');
    Route::get('/supplychain/contracts/delete', 'Contracts\SupplyChainController@displayDeleteSupplyChainContract');
    Route::post('/supplychain/contracts/delete', 'Contracts\SupplyChainController@deleteSupplyChainContract');
    Route::get('/supplychain/contracts/end', 'Contracts\SupplyChainController@displayEndSupplyChainContract');
    Route::post('/supplychain/contracts/end', 'Contracts\SupplyChainController@storeEndSupplyChainContract');
    Route::get('/supplychain/display/bids', 'Contracts\SupplyChainController@displaySupplyChainBids');
    Route::get('/supplychain/display/newbid/{contract}', 'Contracts\SupplyChainController@displaySupplyChainContractBid');
    Route::post('/supplychain/display/newbid', 'Contracts\SupplyChainController@storeSupplyChainContractBid');
    Route::get('/supplychain/delete/bid/{contractId}/{bidId}', 'Contracts\SupplyChainController@deleteSupplyChainContractBid');
    Route::get('/supplychain/modify/bid', 'Contracts\SupplyChainController@displayModifySupplyChainContractBid');
    Route::post('/supplychain/modify/bid', 'Contracts\SupplyChainController@modifySupplyChainContractBid');

    /**
     * Test Controller display pages
     */
    Route::get('/test/char/display', 'Test\TestController@displayCharTest');

});

?>