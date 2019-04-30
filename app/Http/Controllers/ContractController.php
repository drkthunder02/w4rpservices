<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

//Libraries
use App\Library\Lookups\LookupHelper;
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
        //Calculate today's date to know which contracts to display
        $today = Carbon::now();

        //Declare our array variables
        $bids = array();
        $contracts = array();
        $i = 0;

        //Fetch all of the current contracts from the database
        $contractsTemp = Contract::where('end_date', '>=', $today)->get()->toArray();

        //Count the number of bids, and add them to the arrays
        for($i = 0; $i < sizeof($contractsTemp); $i++) {
            $tempCount = Bid::where(['contract_id' => $contractsTemp[$i]['contract_id']])->count('contract_id');
            $bids = Bid::where(['contract_id' => $contractsTemp[$i]['contract_id']])->get()->toArray();

            //Assemble the finaly array
            $contracts[$i] = $contractsTemp[$i];
            $contracts[$i]['bid_count'] = $tempCount;
            $contracts[$i]['bids'] = $bids;
        }        

        //Call for the view to be displayed
        return view('contracts.allcontracts')->with('contracts', $contracts);
    }

    /**
     * Controller function to display all current public contracts
     */
    public function displayPublicContracts() {
        //Calculate today's date to know which contracts to display
        $today = Carbon::now();

        //Declare our array variables
        $bids = array();
        $contracts = array();
        $i = 0;

        //Fetch all of the current contracts from the database
        $contractsTemp = Contract::where('end_date', '>=', $today)
                             ->where(['type' => 'public'])->get()->toArray();

        //Count the number of bids, and add them to the arrays
        for($i = 0; $i < sizeof($contractsTemp); $i++) {
            $tempCount = Bid::where(['contract_id' => $contractsTemp[$i]['contract_id']])->count('contract_id');
            $bids = Bid::where(['contract_id' => $contractsTemp[$i]['contract_id']])->get()->toArray();

            //Assemble the finaly array
            $contracts[$i] = $contractsTemp[$i];
            $contracts[$i]['bid_count'] = $tempCount;
            $contracts[$i]['bids'] = $bids;
            foreach($bids as $bid) {
                if(!isset($contracts[$i]['lowestbid'])) {
                    $contracts[$i]['lowestbid']['amount'] = $bid->amount;
                    $contracts[$i]['lowestbid']['corporation_name'] = $bid->corporation_name;
                } else {
                    if($bid->amount < $contract[$i]['lowestbid']['amount']) {
                        $contracts[$i]['lowestbid']['amount'] = $bid->amount;
                        $contracts[$i]['lowestbid']['corporation_name'] = $bid->corporation_name;
                    }
                }
            }
        }        

        //Call for the view to be displayed
        return view('contracts.publiccontracts')->with('contracts', $contracts);
    }

    /**
     * Controller function to display current private contracts
     */
    public function displayPrivateContracts() {
        //Declare our array variables
        $bids = array();
        $contracts = array();
        $lowestBid = null;

        //Calucate today's date to know which contracts to display
        $today = Carbon::now();

        //Fetch all of the current contracts from the database
        $contractsTemp = Contract::where('end_date', '>=', $today)
                             ->where(['type' => 'private'])->get();
        
        //Count the number of bids, and add them to the arrays
        for($i = 0; $i < sizeof($contractsTemp); $i++) {
            $tempCount = Bid::where(['contract_id' => $contractsTemp[$i]['contract_id']])->count('contract_id');
            $bids = Bid::where(['contract_id' => $contractsTemp[$i]['contract_id']])->get()->toArray();

            //Assemble the finaly array
            $contracts[$i] = $contractsTemp[$i];
            $contracts[$i]['bid_count'] = $tempCount;
            $contracts[$i]['bids'] = $bids;
            foreach($bids as $bid) {
                if(!isset($contracts[$i]['lowestbid'])) {
                    $contracts[$i]['lowestbid']['amount'] = $bid->amount;
                } else {
                    if($bid->amount < $contract[$i]['lowestbid']['amount']) {
                        $contracts[$i]['lowestbid']['amount'] = $bid->amount;
                    }
                }
            }
        }

        return view ('contracts.privatecontracts')->with('contracts', $contracts);
    }

    /**
     * Controller function to display expired contracts
     * 
     */
    public function displayExpiredContracts() {
        //Calculate today's date to know which contracts to display
        $today = Carbon::now();

        //Retrieve the contracts from the database
        $contracts = Contract::where('end_date', '<', $today)->get();

        return view('contracts.expiredcontracts')->with('contracts', $contracts);
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

        //Delcare some class variables we will need
        $lookup = new LookupHelper;

        $amount = 0.00;

        //Convert the amount to a whole number from abbreviations
        if($request->suffix == 'B') {
            $amount = $request->bid * 1000000000.00;
        } else if($request->suffix == 'M') {
            $amount = $request->bid * 1000000.00;
        } else {
            $amount = $request->bid * 1.00;
        }

        //Get the character id and character name from the auth of the user calling
        //this function
        $characterId = auth()->user()->getId();
        $characterName = auth()->user()->getName();
        //Use the lookup helper in order to find the user's corporation id and name
        $corporationId = $lookup->LookupCharacter($characterId);
        $corporationName = $lookup->LookupCorporationName($corporationId);

        //Before saving a bid let's check to see if the user already placed a bid on the contract
        $found = Bid::where([
            'contract_id' => $request->contract_id,
            'character_id' => $characterId,
        ])->first();

        if(isset($found->contract_id)) {
            return redirect('/contracts/display/all')->with('error', 'You have already placed a bid for this contract.  Please modify the existing bid.');
        } else {
            //Create the model object to save data to
            $bid = new Bid;
            $bid->contract_id = $request->contract_id;
            $bid->bid_amount = $amount;
            $bid->character_id = $characterId;
            $bid->character_name = $characterName;
            $bid->corporation_id = $corporationId;
            $bid->corporation_name = $corporationName;
            $bid->save();

            //Redirect to the correct page
            return redirect('/contracts/display/all')->with('success', 'Bid accepted.');
        }
    }

    /**
     * Controller function to delete a bid
     */
    public function deleteBid($id) {
        //Delete the bid entry from the database
        Bid::where([
            'id' => $id,
        ])->delete();

        return redirect('/contracts/display/public')->with('success', 'Bid deleted.');
    }

    /**
     * Controller function to display modify bid page
     */
    public function displayModifyBid($id) {
        //With the bid id number, look up the bid in the database to get the contract information
        $bid = Bid::where(['id' => $id])->first();

        //Retrieve the contract from the database
        $contract = Contract::where(['contract_id' => $bid->contract_id])->first()->toArray();

        return view('contracts.modifybid')->with('contract', $contract)
                                          ->with('bid', $bid);
    }

    /**
     * Controller function to modify a bid
     */
    public function modifyBid(Request $request) {
        $this->validate($request, [
            'bid',
        ]);

        $amount = $request->bid;
        $type = $request->type;
        $contractId = $request->contract_id;

        if($request->suffix == 'B') {
            $amount = $amount * 1000000000.00;
        } else if($request->suffix == 'M') {
            $amount = $amount * 1000000.00;
        } else {
            $amount = $amount * 1.00;
        }
        
        Bid::where([
            'character_id' => auth()->user()->getId(),
            'contract_id' => $contractId,
        ])->update([
            'bid_amount' => $amount,
        ]);

        if($type == 'public') {
            return redirect('/contracts/display/public')->with('success', 'Bid modified.');
        } else {
            return redirect('/contracts/display/private')->with('success', 'Bid modified');
        }
        
    }
}
