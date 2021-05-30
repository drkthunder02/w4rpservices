<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;

//Application Library
use App\Library\Helpers\LookupHelper;
use App\Library\Helpers\StructureHelper;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Ledger;
use App\Models\MiningTax\Payment;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\MineralPrice;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

class MiningTaxesAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Display the page to setup the form for corporations to rent a moon
     */
    public function DisplayMoonRentalForm() {

    }

    /**
     * Store the details for the form for corporations renting a specific moon
     */
    public function StoreMoonRentalForm() {

    }

    /**
     * Remove a moon from being rented from a specific corporation
     */
    public function DeleteMoonRental(Request $request) {

    }

    /**
     * Display current unpaid invoices
     */
    public function DisplayUnpaidInvoice() {
        $invoices = Invoice::where([
            'status' => 'Pending',
        ])->orWhere([
            'status' => 'Late',
        ])->orWhere([
            'status' => 'Deferred',
        ])->orderByDesc('invoice_id')->paginate(50);

        $totalAmount = Invoice::where([
            'status' => 'Pending',
        ])->orWhere([
            'status' => 'Late',
        ])->orWhere([
            'status' => 'Deferred',
        ])->sum('invoice_amount');

        return view('miningtax.admin.display.unpaid')->with('invoices', $invoices);
    }

    /**
     * Search unpaid invoices
     */
    public function SearchUnpaidInvoice(Request $request) {
        $invoices = Invoice::where('invoice_id', 'LIKE', '%' . $request->q . '%')
                           ->where(['status' => 'Pending'])
                           ->orWhere(['status' => 'Late'])
                           ->orWhere(['status' => 'Deferred'])
                           ->orderByDesc('invoice_id')
                           ->paginate(25);

        if(count($invoices) > 0) {
            return view('miningtax.admin.display.unpaid')->with('invoices', $invoices);
        }

        return view('miningtax.admin.display.unpaid')->with('error', 'No invoices found');
    }

    /**
     * Display page to modify an unpaid invoice
     */
    public function DisplayModifyInvoice() {

    }

    /**
     * Modify an unpaid invoice
     */
    public function ProcessModifyInvoice() {
        
    }

    /**
     * Mark an invoice paid
     */
    public function UpdateInvoice(Request $request) {
        $this->validate($request, [
            'invoiceId' => 'required',
            'status' => 'required',
        ]);

        Invoice::where([
            'invoice_id' => $request->invoiceId,
        ])->update([
            'status' => $request->status,
        ]);

        return redirect('/miningtax/admin/display/unpaid')->with('success', 'Invoice successfully updated.');
    }

    /**
     * Display past paid invoices
     */
    public function DisplayPaidInvoices() {
        $invoices = Invoice::where([
            'status' => 'Paid',
        ])->orWhere([
            'status' => 'Paid Late',
        ])->paginate(50);

        $totalAmount = Invoice::where([
            'status' => 'Paid',
        ])->orWhere([
            'status' => 'Paid Late',
        ])->sum('invoice_amount');

        return view('miningtax.admin.display.paidinvoices')->with('invoices', $invoices)
                                                           ->with('totalAmount', $totalAmount);
    }    
}
