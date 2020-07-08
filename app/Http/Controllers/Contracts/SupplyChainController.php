<?php

namespace App\Http\Controllers\Contracts;

//Internal Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

//Libraries
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\User\User;
use App\Models\Contracts\SupplyChainBid;
use App\Models\Contracts\SupplyChainContract;

class SupplyChainController extends Controller
{
    /**
     * Constructor
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Renter');
    }

    /**
     * Display the supply chain dashboard
     */
    public function displaySupplyChainDashboard() {
        $contracts = SupplyChainContract::where([
            'state' => 'open',
        ])->get();

        return view('supplychain.dashboard.main')->with('contracts', $contracts);
    }

    /**
     * Show the user's open contracts
     */
    public function displayMyOpenContractsDashboard() {
        $contracts = SupplyChainContract::where([
            'issuer_id' => auth()->user()->getId(),
            'state' => 'open',
        ])->get();

        return view('supplychain.dashboard.main')->with('contracts', $contracts);
    }

    /**
     * Show the user's closed contracts
     */
    public function displayMyClosedContractsDashboard() {
        $contracts = SupplyChainContract::where([
            'issuer_id' => auth()->user()->getId(),
            'state' => 'closed',
        ])->get();

        return view('supplychain.dashboard.main')->with('contracts', $contracts);
    }

    /**
     * Show the past contracts bidded on
     */
    public function displayPastContractsDashboard() {
        $contracts = array();

        $acceptedBids = SupplyChainBid::where([
            'bid_type' => 'accepted',
        ])->get();

        foreach($acceptedBids as $bid) {
            $contracts = null;

            $temp = SupplyChainContract::where([
                'state' => 'closed',
            ])->get()->toArray();

            $temp2 = SupplyChainContract::where([
                'state' => 'completed',
            ])->get()->toArray();

            array_push($contracts, $temp);
            array_push($contracts, $temp2);
        }

        return view('supplychain.dashboard.past')->with('contracts', $contracts);
    }

    /**
     * Display new contract page
     */
    public function displayNewSupplyChainContract() {
        return view('supplychain.forms.newcontract');
    }

    /**
     * Store new contract page
     */
    public function storeNewSupplyChainContract(Request $request) {
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'end_date' => 'required',
            'delivery_by' => 'required',
            'body' => 'required',
        ]);

        $contract = new SupplyChainContract;
        $contract->issuer_id = auth()->user()->getId();
        $contract->issuer_name = auth()->user()->getName();
        $contract->title = $request->title;
        $contract->type = $request->type;
        $contract->end_date = $request->end_date;
        $contract->delivery_by = $request->delivery_by;
        $contract->body = $request->body;
        $contract->state = 'open';
        $contract->save();

        $this->NewSupplyChainContractMail();

        return redirect('/supplychain/dashboard')->with('success', 'New Contract created.');
    }

    /**
     * Display the delete contract page
     */
    public function displayDeleteSupplyChainContract() {
        return view('supplychain.forms.delete');
    }

    /**
     * Delete a supply chain contract
     */
    public function deleteSupplyChainContract(Request $request) {
        $this->validate($request, [
            'contract' => 'required',
        ]);

        /**
         * Remove the supply chain contract if it's yours.
         */
        $count = SupplyChainContract::where([
            'issuer_id' => auth()->user()->getId(),
            'id' => $request->contract,
        ])->count();

        if($count > 0) {
            //Remove the supply chain contract
            SupplyChainContract::where([
                'issuer_id' => auth()->user()->getId(),
                'id' => $request->contract,
            ])->delete();
        }
        
        //Remove all the bids from the supply chain contract
        SupplyChainBid::where([
            'contract_id' => $request->contract,
        ])->delete();

        return redirect('/supplychain/dashboard')->with('success', 'Supply Chain Contract deleted successfully.');
    }

    /**
     * Display the end supply chain contrage page
     */
    public function displayEndSupplyChainContract() {
        return view('supplychain.forms.end');
    }

    /**
     * Process the end supply chain contract page
     */
    public function storeEndSupplyChainContract(Request $request) {
        $this->validate($request, [

        ]);

        return redirect('/supplychain/dashboard')->with('success', 'Contract ended, and mails sent to the winning bidder.');
    }

    /**
     * Display supply chain contract bids page
     */
    public function displaySupplyChainBids() {

        return view('supplychain.dashboard.bids');
    }

    /**
     * Display expired supply chain contracts page
     */
    public function displayExpiredSupplyChainContracts() {

        return view('supplychain.dashboard.expired');
    }

    /**
     * Display the new bid on a supply chain contract page
     */
    public function displaySupplyChainContractBid(Request $request) {
        $this->validate($request, [
            'contract_id' => 'required',
        ]);

        $contractId = $request->contract_id;

        return view('supplychain.forms.enterbid')->with('contractId', $contractId);
    }

    /**
     * Enter a new bid on a supply chain contract
     */
    public function storeSupplyChainContractBid(Request $request) {
        $this->validate($request, [

        ]);

        return redirect('/supplychain/dashboard')->with('success', 'Successfully stored supply chain contract bid.');
    }

    /**
     * Delete a bid on a supply chain contract
     */
    public function deleteSupplyChainContractBid(Request $request) {
        $this->validate($request, [

        ]);

        return redirect('/suppplychain/dashboard')->with('success', 'Deleted supply chain contract bid.');
    }

    /**
     * Modify a bid on a supply chain contract
     */
    public function modifySupplyChainContractBid(Request $request) {
        $this->validate($request, [

        ]);

        return redirect('/supplychain/dashboard')->with('success', 'Modified supply chain contract bid.');
    }

    /**
     * Send out a new supply chain contract mail
     */
    private function NewSupplyChainContractMail(SupplyChainContract $contract) {
        //Get the config for the esi
        $config = config('esi');
        $todayDate = Carbon::now()->toFormat('d-m-Y');

        $subject = 'New Supply Chain Contract ' . $todayDate;
        $body = "A supply chain contract is available.<br>";
        $body .= "Contract: " . $contract->title . "<br>";
        $body .= "Notes: " . $contract->body . "<br>";
        $body .= "Delivery Date: " . $contract->delivery_date . "<br>";
        $body .= "<br>Sincerely on behalf of,<br>" . $contract->issuer_name . "<br>";
        ProcessSendEveMailJob::dispatch($body, 145223267, 'mailing_list', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds(30));
    }

    /**
     * Send out a mail when the supply chain contract has been deleted
     */
    private function DeleteSupplyChainContractMail($contract) {
        //Get the esi config
        $config = config('esi');

        $subject = 'Production Contract Removal';
        $body = "A production contract has been deleted.<br>";
        $body .= "Contract: " . $contract->title . "<br>";
        $body .= "Notes: " . $contract->note . "<br>";
        $body .= "<br>Sincerely on behalf of,<br>" . $contract->issuer_name;
        ProcessSendEveMailJob::dispatch($body, 145223267, 'mailing_list', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds(30));
    }

    /**
     * Tidy up datatables from a completed supply chain contract
     */
    private function TidySupplyChainContract($contract, $bid) {
        SupplyChainContract::where([
            'contract_id' => $contract->contract_id,
        ])->update([
            'state' => 'finished',
        ]);
    }
}
