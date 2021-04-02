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
})->name('notloggedin');

/**
 * Login Display pages
 */
Route::get('/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback')->name('callback');
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function(){
    /**
     * Admin Controller display pages
     */
    Route::get('/admin/dashboard/users', 'Dashboard\AdminDashboardController@displayUsersPaginated');
    Route::post('/admin/dashboard/users', 'Dashboard\AdminDashboardController@searchUsers');
    Route::get('/admin/dashboard/taxes', 'Dashboard\AdminDashboardController@displayTaxes');
    Route::get('/admin/dashboard/logins', 'Dashboard\AdminDashboardController@displayAllowedLogins');
    Route::post('/admin/add/role', 'Dashboard\AdminDashboardController@addRole');
    Route::post('/admin/remove/role', 'Dashboard\AdminDashboardController@removeRole');
    Route::post('/admin/add/permission', 'Dashboard\AdminDashboardController@addPermission');
    Route::post('/admin/modify/role', 'Dashboard\AdminDashboardController@modifyRole');
    Route::post('/admin/remove/user', 'Dashboard\AdminDashboardController@removeUser');
    Route::post('/admin/modify/user/display', 'Dashboard\AdminDashboardController@displayModifyUser');
    Route::post('/admin/add/allowedlogin', 'Dashboard\AdminDashboardController@addAllowedLogin');
    Route::post('/admin/remove/allowedlogin', 'Dashboard\AdminDashboardController@removeAllowedLogin');
    Route::get('/admin/dashboard', 'Dashboard\AdminDashboardController@displayAdminDashboard');
    Route::get('/admin/dashboard/journal', 'Dashboard\AdminDashboardController@displayJournalEntries');

    /**
     * After Action Report display pages
     */
    Route::get('/reports/display/all', 'AfterActionReports\AfterActionReportsController@DisplayAllReports');
    Route::get('/reports/display/report/form', 'AfterActionReports\AfterActionReportsController@DisplayReportForm');
    Route::get('/reports/display/comment/form/{id}', 'AfterActionReports\AfterActionReportsController@DisplayCommentForm');
    Route::post('/reports/store/new/report', 'AfterActionReports\AfterActionReportsController@StoreReport');
    Route::post('/reports/store/new/comments', 'AfterActionReports\AfterActionReportsController@StoreComment');

    /**
     * After Action Reports Admin display pages
     */

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
     * Mining Moon Tax display pages
     */
    Route::get('/miningtax/display/invoices', 'MiningTaxes\MiningTaxesController@DisplayInvoices');
    Route::get('/miningtax/display/extractions', 'MiningTaxes\MiningTaxesController@DisplayUpcomingExtractions');
    Route::get('/miningtax/display/ledgers', 'MiningTaxes\MiningTaxesController@DisplayMoonLedgers');
    Route::get('/miningtax/admin/display/unpaid', 'MiningTaxes\MiningTaxesAdminController@DisplayUnpaidInvoice');
    Route::post('/miningtax/admin/update/invoice', 'MiningTaxes\MiningTaxesAdminController@UpdateInvoice');
    Route::post('/miningtax/admin/delete/invoice', 'MiningTaxes\MiningTaxesAdminController@DeleteInvoice');
    Route::get('/miningtax/admin/display/paid', 'MiningTaxes\MiningTaxesAdminController@DisplayPaidInvoices');
    Route::any('/miningtax/admin/display/unpaid/search', function() {
        $q = Illuminate\Support\Facades\Input::get('q');
        if($q != "") {
            $invoices = App\Models\MiningTax\Invoice::where('invoice_id', 'LIKE', '%' . $q . '%')
                                ->where(['status' => 'Pending'])
                                ->orWhere(['status' => 'Late'])
                                ->orWhere(['status' => 'Deferred'])
                                ->orderByDesc('invoice_id')
                                ->paginate(25)
                                ->setPath('');
            $pagination = $invoices->appends(array('q' => Illuminate\Support\Facades\Input::get('q')));

            if(count($invoices) > 0) {
                return view('miningtax.admin.display.unpaid')->withDetails($invoices)->withQuery($q);
            }

            return view('miningtax.admin.display.unpaid')->with('error', 'No invoices found.  Try to search again!');
        }
    });
    
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