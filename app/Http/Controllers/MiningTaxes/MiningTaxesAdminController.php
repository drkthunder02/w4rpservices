<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

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
use App\Models\Structure\Structure;
use App\Models\MiningTax\MiningOperation;

class MiningTaxesAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:mining.officer');
    }

    /**
     * Display the form for mining operations held by the alliance
     */
    public function displayMiningOperationForm() {
        //Declare variables
        $config = config('esi');
        $lookup = new LookupHelper;
        $sHelper = new StructureHelper($config['primary'], $config['corporation']);
        $coll = new Collection;
        $structures = array();
        
        //Get all of the structures
        $athanors = $sHelper->GetStructuresByType('Athanor');
        $tataras = $sHelper->GetStructuresByType('Tatara');

        //Cycle through each athanor and add it to the stack
        foreach($athanors as $athanor) {
            $structures->push([
                $athanor->structure_id => $athanor->structure_name,
            ]);
        }
        //Cycle through each tatara and add it to the stack
        foreach($tataras as $tatara) {
            $structures->push([
                $tatara->structure_id => $tatara->structure_name,
            ]);
        }
        //Sort all of the structures
        $structures->sort();

        //Get the current mining operations.
        $operations = MiningOperation::where([
            'processed' => 'No',
        ])->get();

        return view('miningtax.admin.display.miningops.form')->with('structures', $structures)
                                                             ->with('operations', $operations);
    }

    /**
     * Store the results from the mining operations form
     */
    public function storeMiningOperationForm(Request $request) {
        //Validate the data
        $this->validate($request, [
            'name' => 'required',
            'date' => 'required',
            'structure' => 'required',
        ]);

        //Get the name of the structure from the table
        $moon = Observer::where([
            'observer_id' => $request->structure,
        ])->get();

        dd($moon);

        //Save the mining operation into the database
        $operation = new MiningOperation;
        $operation->structure_id = $request->structure;
        $operation->structure_name = $moon->observer_name;
        $operation->authorized_by_id = auth()->user()->getId();
        $operation->authorized_by_name = auth()->user()->getName();
        $operation->operation_name = $request->name;
        $operation->operation_date = $request->date;
        $operation->processed = 'No';
        $operation->processed_on = null;
        $operation->save();

        return redirect('/admin/dashboard')->with('success', 'Operation added successfully.');
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
     * Display an invoice based on it's id
     * 
     * @var $invoiceId
     */
    public function displayInvoice($invoiceId) {
        $ores = array();
        $totalPrice = 0.00;

        $invoice = Invoice::where([
            'invoice_id' => $invoiceId,
        ])->first();

        $items = Ledger::where([
            'invoice_id' => $invoiceId,
        ])->get();

        foreach($items as $item) {
            if(!isset($ores[$item['ore_name']])) {
                $ores[$item['ore_name']] = 0;
            }
            $ores[$item['ore_name']] = $ores[$item['ore_name']] + $item['quantity'];

            $totalPrice += $item['amount'];
        }

        return view('miningtax.admin.display.details.invoice')->with('ores', $ores)
                                                              ->with('invoice', $invoice)
                                                              ->with('totalPrice', $totalPrice);
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
            'modified_by_id' => auth()->user()->getId(),
            'modified_by_name' => auth()->user()->getName(),
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
