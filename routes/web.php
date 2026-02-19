<?php

use App\Http\Controllers\AfterActionReports;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Blacklist;
use App\Http\Controllers\Contracts;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Finances;
use App\Http\Controllers\Logistics;
use App\Http\Controllers\MiningTaxes;
use App\Http\Controllers\SRP;
use App\Http\Controllers\Test;
use Illuminate\Support\Facades\Route;

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
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return view('login');
})->name('notloggedin');

/**
 * Login Display pages
 */
Route::get('/login', [Auth\LoginController::class, 'redirectToProvider'])->name('login');
Route::get('/callback', [Auth\LoginController::class, 'handleProviderCallback'])->name('callback');
Route::get('/logout', [Auth\LoginController::class, 'logout']);

Route::middleware('auth')->group(function () {
    /**
     * Admin Controller display pages
     */
    Route::get('/admin/dashboard/users', [Dashboard\AdminDashboardController::class, 'displayUsersPaginated']);
    Route::post('/admin/dashboard/users', [Dashboard\AdminDashboardController::class, 'searchUsers']);
    Route::get('/admin/dashboard/taxes', [Dashboard\AdminDashboardController::class, 'displayTaxes']);
    Route::get('/admin/dashboard/logins', [Dashboard\AdminDashboardController::class, 'displayAllowedLogins']);
    Route::post('/admin/add/role', [Dashboard\AdminDashboardController::class, 'addRole']);
    Route::post('/admin/remove/role', [Dashboard\AdminDashboardController::class, 'removeRole']);
    Route::post('/admin/add/permission', [Dashboard\AdminDashboardController::class, 'addPermission']);
    Route::post('/admin/modify/role', [Dashboard\AdminDashboardController::class, 'modifyRole']);
    Route::post('/admin/remove/user', [Dashboard\AdminDashboardController::class, 'removeUser']);
    Route::post('/admin/modify/user/display', [Dashboard\AdminDashboardController::class, 'displayModifyUser']);
    Route::post('/admin/add/allowedlogin', [Dashboard\AdminDashboardController::class, 'addAllowedLogin']);
    Route::post('/admin/remove/allowedlogin', [Dashboard\AdminDashboardController::class, 'removeAllowedLogin']);
    Route::get('/admin/dashboard', [Dashboard\AdminDashboardController::class, 'displayAdminDashboard']);
    Route::get('/admin/dashboard/journal', [Dashboard\AdminDashboardController::class, 'displayJournalEntries']);

    /**
     * After Action Report display pages
     */
    Route::get('/reports/display/all', [AfterActionReports\AfterActionReportsController::class, 'DisplayAllReports']);
    Route::get('/reports/display/report/form', [AfterActionReports\AfterActionReportsController::class, 'DisplayReportForm']);
    Route::get('/reports/display/comment/form/{id}', [AfterActionReports\AfterActionReportsController::class, 'DisplayCommentForm']);
    Route::post('/reports/store/new/report', [AfterActionReports\AfterActionReportsController::class, 'StoreReport']);
    Route::post('/reports/store/new/comments', [AfterActionReports\AfterActionReportsController::class, 'StoreComment']);

    /**
     * After Action Reports Admin display pages
     */

    /**
     * Blacklist Controller display pages
     */
    Route::get('/blacklist/display', [Blacklist\BlacklistController::class, 'DisplayBlacklist']);
    Route::get('/blacklist/display/add', [Blacklist\BlacklistController::class, 'DisplayAddToBlacklist']);
    Route::get('/blacklist/display/remove', [Blacklist\BlacklistController::class, 'DisplayRemoveFromBlacklist']);
    Route::get('/blacklist/display/search', [Blacklist\BlacklistController::class, 'DisplaySearch']);
    Route::post('/blacklist/add', [Blacklist\BlacklistController::class, 'AddToBlacklist']);
    Route::post('/blacklist/remove', [Blacklist\BlacklistController::class, 'RemoveFromBlacklist']);
    Route::post('/blacklist/search', [Blacklist\BlacklistController::class, 'SearchInBlacklist']);

    /**
     * Dashboard Controller Display pages
     */
    Route::get('/dashboard', [Dashboard\DashboardController::class, 'index']);
    Route::post('/dashboard/alt/delete', [Dashboard\DashboardController::class, 'removeAlt']);
    Route::get('/profile', [Dashboard\DashboardController::class, 'profile']);

    /**
     * Finance Controller Display pages
     */
    Route::get('/finances', [Finances\FinanceController::class, 'displayOutlook']);
    Route::get('/finances/card', [Finances\FinanceController::class, 'displayCards']);

    /**
     * Jump Bridge Fuel Display pages
     */
    Route::get('/jumpbridges/fuel', [Logistics\FuelController::class, 'displayStructures']);

    /**
     * Mining Moon Tax display pages
     */
    Route::get('/miningtax/display/detail/invoice/{invoice}', [MiningTaxes\MiningTaxesController::class, 'displayInvoice']);
    Route::get('/miningtax/display/invoices', [MiningTaxes\MiningTaxesController::class, 'DisplayInvoices']);
    Route::get('/miningtax/display/extractions', [MiningTaxes\MiningTaxesController::class, 'DisplayUpcomingExtractions']);
    Route::get('/miningtax/display/ledgers', [MiningTaxes\MiningTaxesController::class, 'DisplayMoonLedgers']);
    Route::get('/miningtax/display/availablemoons', [MiningTaxes\MiningTaxesController::class, 'displayAvailableMoons']);
    Route::get('/miningtax/display/allmoons', [MiningTaxes\MiningTaxesController::class, 'displayAllMoons']);
    Route::get('/miningtax/admin/display/detail/invoice/{invoice}', [MiningTaxes\MiningTaxesAdminController::class, 'displayInvoice']);
    Route::get('/miningtax/admin/display/unpaid', [MiningTaxes\MiningTaxesAdminController::class, 'DisplayUnpaidInvoice']);
    Route::post('/miningtax/admin/update/invoice', [MiningTaxes\MiningTaxesAdminController::class, 'UpdateInvoice']);
    Route::post('/miningtax/admin/delete/invoice', [MiningTaxes\MiningTaxesAdminController::class, 'DeleteInvoice']);
    Route::get('/miningtax/admin/display/paid', [MiningTaxes\MiningTaxesAdminController::class, 'DisplayPaidInvoices']);
    Route::any('/miningtax/admin/display/unpaid/search', [MiningTaxes\MiningTaxesAdminController::class, 'SearchUnpaidInvoice']);
    Route::get('/miningtax/admin/display/form/operations', [MiningTaxes\MiningTaxesAdminController::class, 'displayMiningOperationForm']);
    Route::post('/miningtax/admin/display/form/operations', [MiningTaxes\MiningTaxesAdminController::class, 'storeMiningOperationForm']);

    /**
     * Moon Rental Tax display pages
     */
    Route::post('/moonrental/display/form', [MiningTaxes\MiningTaxesController::class, 'DisplayMoonRentalForm']);
    Route::post('/moonrental/display/form/store', [MiningTaxes\MiningTaxesController::class, 'storeMoonRentalForm']);

    /**
     * Scopes Controller display pages
     */
    Route::get('/scopes/select', [Auth\EsiScopeController::class, 'displayScopes']);
    Route::post('redirectToProvider', [Auth\EsiScopeController::class, 'redirectToProvider']);

    /**
     * SRP Controller display pages
     */
    Route::get('/srp/form/display', [SRP\SRPController::class, 'displaySrpForm']);
    Route::post('/srp/form/display', [SRP\SRPController::class, 'storeSRPFile']);
    Route::get('/srp/display/costcodes', [SRP\SRPController::class, 'displayPayoutAmounts']);

    /**
     * SRP Admin Controller display pages
     */
    Route::get('/srp/admin/display', [SRP\SRPAdminController::class, 'displaySRPRequests']);
    Route::post('/srp/admin/process', [SRP\SRPAdminController::class, 'processSRPRequest']);
    Route::get('/srp/admin/statistics', [SRP\SRPAdminController::class, 'displayStatistics']);
    Route::get('/srp/admin/costcodes/display', [SRP\SRPAdminController::class, 'displayCostCodes']);
    Route::get('/srp/admin/costcodes/add', [SRP\SRPAdminController::class, 'displayAddCostCode']);
    Route::post('/srp/admin/costcodes/add', [SRP\SRPAdminController::class, 'addCostCode']);
    Route::post('/srp/admin/costcodes/modify', [SRP\SRPAdminController::class, 'modifyCostCodes']);
    Route::get('/srp/admin/display/history', [SRP\SRPAdminController::class, 'displayHistory']);
    Route::get('/srp/admin/update/shiptype/{id}/{value}', [SRP\SRPAdminController::class, 'updateShipType']);
    Route::get('/srp/admin/update/lossvalue/{id}/{value}', [SRP\SRPAdminController::class, 'updateLossValue']);

    /**
     * Supply Chain Contracts Controller display pages
     */
    Route::get('/supplychain/dashboard', [Contracts\SupplyChainController::class, 'displaySupplyChainDashboard']);
    Route::get('/supplychain/my/dashboard', [Contracts\SupplyChainController::class, 'displayMySupplyChainDashboard']);
    Route::get('/supplychain/contracts/new', [Contracts\SupplyChainController::class, 'displayNewSupplyChainContract']);
    Route::post('/supplychain/contracts/new', [Contracts\SupplyChainController::class, 'storeNewSupplyChainContract']);
    Route::get('/supplychain/contracts/delete', [Contracts\SupplyChainController::class, 'displayDeleteSupplyChainContract']);
    Route::post('/supplychain/contracts/delete', [Contracts\SupplyChainController::class, 'deleteSupplyChainContract']);
    Route::get('/supplychain/contracts/end', [Contracts\SupplyChainController::class, 'displayEndSupplyChainContract']);
    Route::post('/supplychain/contracts/end', [Contracts\SupplyChainController::class, 'storeEndSupplyChainContract']);
    Route::get('/supplychain/display/bids', [Contracts\SupplyChainController::class, 'displaySupplyChainBids']);
    Route::get('/supplychain/display/newbid/{contract}', [Contracts\SupplyChainController::class, 'displaySupplyChainContractBid']);
    Route::post('/supplychain/display/newbid', [Contracts\SupplyChainController::class, 'storeSupplyChainContractBid']);
    Route::get('/supplychain/delete/bid/{contractId}/{bidId}', [Contracts\SupplyChainController::class, 'deleteSupplyChainContractBid']);
    Route::get('/supplychain/modify/bid', [Contracts\SupplyChainController::class, 'displayModifySupplyChainContractBid']);
    Route::post('/supplychain/modify/bid', [Contracts\SupplyChainController::class, 'modifySupplyChainContractBid']);

    /**
     * Test Controller display pages
     */
    Route::get('/test/char/display', [Test\TestController::class, 'displayCharTest']);
    Route::get('/test/miningtax/invoice', [Test\TestController::class, 'DebugMiningTaxesInvoices']);
    Route::get('/test/miningtax/observers', [Test\TestController::class, 'DebugMiningObservers']);

});
