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
            'name' => 'required',
            'date' => 'required',
            'delivery' => 'required',
            'body' => 'required',
        ]);

        $contract = new SupplyChainContract;
        $contract->issuer_id = auth()->user()->getId();
        $contract->issuer_name = auth()->user()->getName();
        $contract->title = $request->name;
        $contract->end_date = $request->date;
        $contract->delivery_by = $request->delivery;
        $contract->body = $request->body;
        $contract->state = 'open';
        $contract->bids = 0;
        $contract->save();

        $this->NewSupplyChainContractMail($contract);

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
            'contractId' => 'required',
        ]);

        /**
         * Remove the supply chain contract if it's yours.
         */
        $count = SupplyChainContract::where([
            'issuer_id' => auth()->user()->getId(),
            'id' => $request->contractId,
        ])->count();

        if($count > 0) {
            //Remove the supply chain contract
            SupplyChainContract::where([
                'issuer_id' => auth()->user()->getId(),
                'id' => $request->contractId,
            ])->delete();
        }
        
        //Remove all the bids from the supply chain contract
        SupplyChainBid::where([
            'contract_id' => $request->contractId,
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
            'accept' => 'required',
            'contractId' => 'required',
        ]);

        //Check to make sure the user owns the contract
        $count = SupplyChainContract::where([
            'issuer_name' => auth()->user()->getName(),
            'contract_id' => $request->contractId,
        ])->count();

        //If the count is greater than 0, the user owns the contract.
        //Proceed with ending the contract
        if($count > 0) {

        } else {
            //If the count is zero, then redirect with error messsage
        }

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
            'bid' => 'required',
            'contract_id' => 'required',
        ]);

        $count = SupplyChainBid::where([
            'entity_id' => auth()->user()->getId(),
            'entity_name' => auth()->user()->getName(),
            'contract_id' => $request->contract_id,
        ])->count();

        //If the person already has a bid in, then deny them the option to place another bid on the same contract.
        //Otherwise, enter the bid into the database
        if($count > 0) {
            redirect('/supplychain/dashboard')->with('error', 'Unable to insert bid as one is already present for the supply chain contract.');
        } else {
            //Sanitize the bid amount

            //Create the database entry
            $bid = new SupplyChainBid;
            $bid->contract_id = $request->contract_id;
            $bid->bid_amount = $request->bid;
            $bid->entity_id = auth()->user()->getId();
            $bid->entity_name = auth()->user()->getName();
            $bid->entity_type = 'character';
            if(isset($request->notes)) {
                $bid->bid_note = $request->notes;
            }
            $bid->save();

            redirect('/supplychain/dashboard')->with('success', 'Bid succesfully entered into the contract.');
        }
    }

    /**
     * Delete a bid on a supply chain contract
     */
    public function deleteSupplyChainContractBid(Request $request) {
        $this->validate($request, [
            'contract_id' => 'required',
            'bid_id' => 'required',
        ]);

        //See if the user has put in a bid.  If not, then redirect to failure.
        $count = SupplyChainBid::where([
            'contract_id' => $request->contract_id,
            'entity_id' => auth()->user()->getId(),
            'bid_id' => $request->bid_id,
        ])->count();

        if($count > 0) {
            SupplyChainBid::where([
                'contract_id' => $request->contract_id,
                'entity_id' => auth()->user()->getId(),
                'bid_id' => $request->bid_id,
            ])->delete();

            return redirect('/suppplychain/dashboard')->with('success', 'Deleted supply chain contract bid.');
        } else {
            return redirect('/supplychain/dashboard')->with('error', 'No bid found to delete.');
        }
    }

    /**
     * Display the modify a bid on supply chain contract page
     */
    public function displayModifySupplyChainContractBid(Request $request) {
        $this->validate($request, [
            'contract_id' => 'required',
        ]);

        $contractId = $request->contract_id;

        return view('supplychain.forms.modifybid')->with('contractId', $contractId);
    }

    /**
     * Modify a bid on a supply chain contract
     */
    public function modifySupplyChainContractBid(Request $request) {
        $this->validate($request, [
            'bid_id' => 'required',
            'contract_id' => 'required',
            'bid_amount' => 'required',
        ]);

        //Check for the owner of the bid
        $count = SupplyChainBid::where([
            'bid_id' => $request->bid_id,
            'contract_id' => $request->contract_id,
            'entity_id' => auth()->user()->getId(),
        ])->count();

        if($count > 0) {
            if(isset($request->bid_note)) {
                SupplyChainBid::where([
                    'bid_id' => $request->bid_id,
                    'contract_id' => $request->contract_id,
                    'entity_id' => auth()->user()->getId(),
                ])->update([
                    'bid_amount' => $request->bid_amount,
                    'bid_note' => $request->bid_note,
                ]);
            } else {
                SupplyChainBid::where([
                    'bid_id' => $request->bid_id,
                    'contract_id' => $request->contract_id,
                    'entity_id' => auth()->user()->getId(),
                ])->update([
                    'bid_amount' => $request->bid_amount,
                ]);
            }

            return redirect('/supplychain/dashboard')->with('success', 'Modified supply chain contract bid.');
        } else {
            return redirect('/supplychain/dashboard')->with('error', 'Not able to modify supply chain contract bid.');
        }        
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
