<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

//Libraries
//use App\Library\Contracts\ContractHelper;

//Models
use App\User;
use App\Models\User\UserPermission;
use App\Models\Contracts\Contract;
use App\Models\Contracts\Bid;

class ContractController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:contract.canbid');
    }

    /**
     * Controller function to display the bids placed on contracts
     */
    public function displayBids($id) {
        $bids = Bids::where(['contract_id' => $id, 'character_name' => auth()->user()->getName()])->get();

        return view('contracts.bids')->with('bids', $bids);
    }

    /**
     * 
     * Controller function to display all current open contracts
     * 
     */
    public function displayContracts() {
        //Caluclate today's date to know which contracts to display
        $today = Carbon::now();

        //Declare array variables
        $bids = array();
        $data = array();
        $contracts = array();

        $contracts = Contract::where(['end_date', '>=', $today])->get();

        return view('contracts.allcontracts');
    }

    /**
     * Controller function to display all current public contracts
     */
    public function displayPublicContracts() {
        //Calculate today's date to know which contracts to display
        $today = Carbon::now();

        //Declare our array variables
        $bids = array();
        $data = array();

        //Fetch all of the current contracts from the database
        $contracts = Contract::where('end_date', '>=', $today)
                             ->where(['type' => 'public'])->get();

        //Check if no contracts were pulled from the database
        if($contracts != null) {
            //Foreach each contract we need to gather all of the bids
            foreach($contracts as $contract) {
                //Get all of the bids for the current contract
                $bids = Bid::where(['contract_id' => $contract->id])->get();
                //Build the data structure
                if(count($bids)) {
                    $temp = [
                        'contract' => $contract,
                        'bids' =>$bids,
                    ];
                } else {
                    $temp = [
                        'contract' => $contract,
                    ];
                }

                //Push the new contract onto the stack
                array_push($data, $temp);
            }
        } else {
            $data = null;
        }

        //Call for the view to be displayed
        return view('contracts.publiccontracts')->with('data', $data);
    }

    /**
     * Controller function to display current private contracts
     */
    public function displayPrivateContracts() {
        //Calucate today's date to know which contracts to display
        $today = Carbon::now();

        //Fetch all of the current contracts from the database
        $contracts = Contract::where('end_date', '>=', $today)
                             ->where(['type' => 'private'])->get();

        return view ('contracts.privatecontracts')->with('contracts', $contracts);
    }

    /**
     * Controller function to display a page to allow a bid
     * 
     */
    public function displayNewBid($id) {

        $contractId = $id;

        return view('contracts.enterbid')->with('contractId', $contractId);
    }

    /**
     * Controller function to store a new bid
     */
    public function storeBid(Request $request) {
        //Valid the request from the enter bid page
        $this->validate($request, [
            'contract_id' => 'required',
            'bid' => 'required',
        ]);

        $amount = 0.00;

        //Convert the amount to a whole number from abbreviations
        if($request->suffix == 'B') {
            $amount = $request->bid * 1000000000.00;
        } else if($request->suffix == 'M') {
            $amount = $request->bid * 1000000.00;
        } else {
            $amount = $request->bid;
        }

        //Create the model object to save data to
        $bid = new Bid;
        $bid->contract_id = $request->contract_id;
        $bid->bid_amount = $amount;
        $bid->accepted = false;
        $bid->save();

        //Redirect to the correct page
        return redirect('/contracts/display/public')->with('success', 'Bid accepted.');
    }

    /**
     * Controller function to delete a bid
     */
    public function deleteBid(Request $request) {
        //Validate the request from the previous page
        $this->validate($request, [
            'id' => 'required',
            'contract_id' => 'required',
        ]);

        //Delete the bid entry from the database
        Bid::where([
            'id' => $request->id,
            'contract_id' => $request->contract_id,
        ])->delete();

        return redirect('/contracts/display/public')->with('success', 'Bid deleted.');
    }

    /**
     * Controller function to display modify bid page
     */
    public function displayModifyBid($id) {
        $contract_id = $id;
        
        return view('contracts.modifybid')->with('contract_id', $contract_id);
    }

    /**
     * Controller function to modify a bid
     */
    public function modifyBid(Request $request) {
        $this->validate($request, [
            'bid_amount',
        ]);

        $type = $request->type;
        $contractId = $request->contract_id;
        $bidAmount = $request->bid_amount;
        
        Bid::where([
            'character_id' => auth()->user()->getId(),
            'contract_id' => $contractId,
        ])->update([
            'bid_amount' => $bidAmount,
        ]);

        if($type == 'public') {
            return redirect('/contracts/display/public')->with('success', 'Bid modified.');
        } else {
            return redirect('/contracts/display/private')->with('success', 'Bid modified');
        }
        
    }
}
