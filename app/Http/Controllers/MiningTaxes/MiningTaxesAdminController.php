<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Application Library

//Models
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Ledger;


class MiningTaxesAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
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
        ])->get()->paginate(50);

        return view('miningtax.admin.display.unpaid')->with('invoices', $invoices);
    }

    /**
     * Mark an invoice paid
     */
    public function UpdateInvoice() {
        $this->validate($request, [
            'invoice_id' => 'required',
            'status' => 'required',
        ]);

        Invoice::where([
            'invoice_id' => $request->invoice_id,
        ])->update([
            'status' => $request->status,
        ]);

        return redirect('/admin/dashboard/miningtaxes')->with('success', 'Invoice successfully updated.');
    }

    /**
     * Delete an invoice and mark items paid
     */
    public function DeleteInvoice() {
        $this->validate($request, [
            'invoice_id' => 'required',
        ]);

        Invoice::where([
            'invoice_id' => $request->invoice_id,
        ])->update([
            'status' => 'Deleted',
        ]);

        return redirect('/admin/dashboard/miningtaxes')->with('error', 'Invoice successfully deleted.');
    }

    /**
     * Display past paid invoices
     */
    public function DisplayPaidInvoices() {
        $invoices = Invoice::where([
            'status' => 'Paid',
        ])->orWhere([
            'status' => 'Paid Late',
        ])->get()->paginate(50);

        return view('miningtax.admin.display.paidinvoices')->with('invoices', $invoices);
    }

    /**
     * Display admin mining ledgers by month
     */
    public function DisplayMonthlyMiningLedgers() {
        
    }
    
}
