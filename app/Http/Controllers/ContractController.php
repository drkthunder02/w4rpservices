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
    public function displayBids($contractId) {
        $bids = Bids::where(['contract_id' => $contractId, 'character_name' => auth()->user()->getName()])->get();

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
        $contracts = Contract::where(['end_date', '>=', $today])->get();

        //Check if no contracts were pulled from the database
        if($contracts != null) {
            //Foreach each contract we need to gather all of the bids
            foreach($contracts as $contract) {
                //Get all of the bids for the current contract
                $bids = Bid::where(['contract_id' => $contract->id])->get();
                //Build the data structure
                $temp = [
                    'contract' => $contract,
                    'bids' => $bids,
                ];

                //Push the new contract onto the stack
                array_push($data, $temp);
            }
        } else {
            $data = null;
        }


        return view('contracts.publiccontracts')->with('data', $data);
    }

    /**
     * Controller function to display current private contracts
     */
    public function displayPrivateContracts() {
        //Calucate today's date to know which contracts to display
        $today = Carbon::now();

        //Fetch all of the current contracts from the database
        $contracts = Contract::where(['end_date', '>=', $today])->get();

        return view ('contracts.privatecontracts')->with('contracts', $contracts);
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

        //Create the model object to save data to
        $bid = new Bid;
        $bid->contract_id = $request->contract_id;
        $bid->bid = $request->bid;
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

        return redirect('contracts/display/public')->with('success', 'Bid deleted.');
    }
}
