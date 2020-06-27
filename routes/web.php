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
})->name('/');

/**
 * Login Display pages
 */
Route::get('/login', 'Auth\LoginController@redirectToProvider');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback');
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
    Route::get('/admin/dashboard/wiki', 'Dashboard\AdminController@displayWikiDashboard');
    Route::post('/admin/dashboard/wiki/deleteuser', 'Dashboard\AdminController@deleteWikiUser');
    Route::post('/admin/dashboard/wiki/adduser', 'Dashboard\AdminController@addWikiUserGroup');
    Route::post('/admin/dashboard/wiki/removegroup', 'Dashboard\AdminController@removeWikiUserGroup');
    Route::post('/admin/dashboard/wiki/removeallgroups', 'Dashboard\AdminController@removeWikiUserAllGroups');
    Route::post('/admin/dashboard/wiki/newgroup', 'Dashboard\AdminController@insertNewWikiUserGroup');
    Route::post('/admin/dashboard/wiki/purge', 'Dashboard\AdminController@purgeWikiUsers');
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
     * Dashboard Controller Display pages
     */
    Route::get('/dashboard', 'Dashboard\DashboardController@index');
    Route::post('/dashboard/alt/delete', 'Dashboard\DashboardController@removeAlt');
    Route::get('/profile', 'Dashboard\DashboardController@profile');

    /**
     * Flex Admin Controller display pages
     */
    Route::get('/flex/display', 'Flex\FlexAdminController@displayFlexStructures');
    Route::get('/flex/display/add', 'Flex\FlexAdminController@displayAddFlexStructure');
    Route::post('/flex/display/add', 'Flex\FlexAdminController@addFlexStructure');
    Route::post('/flex/display/remove', 'Flex\FlexAdminController@removeFlexStructure');
    Route::post('/flex/display/update', 'Flex\FlexAdminController@updateFlexStructure');

    /**
     * Fuel Controller display pages
     */
    Route::get('/logistics/fuel/structures', 'Logistics\FuelController@displayStructures');

    /**
     * Moon Controller display pages
     */
    Route::get('/moons/display/rentals', 'Moons\MoonsController@displayRentalMoons');
    Route::get('/moons/display/form/worth', 'Moons\MoonsController@displayTotalWorthForm');
    Route::post('/moons/worth', 'Moons\MoonsController@displayTotalWorth');
    Route::get('/moons/display/all', 'Moons\MoonsController@displayMoons');
    Route::get('/moons/display/request', 'Moons\MoonsController@displayRequestAllianceMoon');
    Route::post('/moon/display/request', 'Moons\MoonsController@storeRequestAllianceMoon');
    Route::get('/moons/display/rental/request', 'Moons\MoonsController@displayRentalMoonPage');

    /**
     * Moon Admin Controller display pages
     */
    Route::get('/moons/admin/display/rentals', 'Moons\MoonsAdminController@displayRentalMoonsAdmin');
    Route::get('/moons/admin/updatemoon', 'Moons\MoonsAdminController@updateMoon');
    Route::post('/moons/admin/updatemoon', 'Moons\MoonsAdminController@storeUpdateMoon');
    Route::post('/moons/admin/display/rentals', 'Moons\MoonsAdminController@storeMoonRemoval');
    Route::get('/moons/admin/display/request', 'Moons\MoonsAdminController@displayMoonRequests');
    Route::post('/moons/admin/display/request', 'Moons\MoonsAdminController@storeApprovedMoonRequest');

    /**
     * Moon Ledger Controller display pages
     */
    Route::get('/moons/ledger/display/moons', 'Moons\MoonLedgerController@displayMoonLedger');
    Route::get('/moons/ledger/display/rentals', 'Moons\MoonLedgerController@displayRentalMoonLedger');
    Route::get('/moons/ledger/register', 'Moons\MoonLedgerController@registerStructures');

    /**
     * Moon Rental Controller display pages
     */
    Route::get('/rentals/moons/alliance/display', 'Moons\RentalMoonsAdminController@displayAllianceUsageRentalMoons');
    Route::get('/rentals/moons/alliance/request', 'Moons\RentalMoonsAdminController@displayRentalMoonForAllianceForm');
    Route::post('/rentals/moons/alliance/request', 'Moons\RentalMoonsAdminController@storeREntalMoonForAlliance');
    Route::get('/moons/composition/display', 'Moons\RentalMoonsAdminController@displayItemCompositionForm');
    Route::post('/moons/composition/display', 'Moons\RentalMoonsAdminController@displayItemCompositionResults');

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
     * Structure Request Controller display pages
     */
    Route::get('/structures/display/form', 'Logistics\StructureRequestController@displayForm');
    Route::post('/structures/display/form', 'Logistics\StructureRequestController@storeForm');

    /**
     * Structure Request Admin Controller display pages
     */
    Route::get('/structures/display/requests', 'Logistics\StructureRequestAdminController@displayRequests');
    Route::post('/structures/display/requests/delete', 'Logistics\StructureRequestAdminController@deleteRequest');

    /**
     * Test Controller display pages
     */
    Route::get('/test/char/display', 'Test\TestController@displayCharTest');

    /**
     * Wiki Controller display pages
     */
    Route::get('/wiki/register', 'Wiki\WikiController@displayRegister');
    Route::post('/wiki/register', 'Wiki\WikiController@storeRegister');
    Route::get('/wiki/changepassword', 'Wiki\WikiController@displayChangePassword');
    Route::post('/wiki/changepassword', 'Wiki\WikiController@changePassword');
    Route::post('/wiki/purge', 'Wiki\WikiController@purgeUsers');

    /**
     * Wormhole Controller display pages
     */
    Route::get('/wormholes/form', 'Wormholes\WormholeController@displayWormholeForm');
    Route::post('/wormholes/form', 'Wormholes\WormholeController@storeWormhole');
    Route::get('/wormholes/display', 'Wormholes\WormholeController@displayWormholes');

});
