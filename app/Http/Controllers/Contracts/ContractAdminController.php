<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

//Libraries
use App\Library\Esi\Mail;

//Models
use App\User;
use App\Models\User\UserPermission;
use App\Models\Contracts\Contract;
use App\Models\Contracts\Bid;
use App\Models\Contracts\AcceptedBid;

class ContractAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:contract.admin');
    }

    /**
     * Contract display functions
     */
    public function displayContractDashboard() {
        $contracts = Contract::where(['finished' => false])->get();

        return view('contracts.admin.contractpanel')->with('contracts', $contracts);
    }

    public function displayPastContracts() {
        $contracts = Contract::where(['finished' => true])->get();

        return view('contracs.admin.past')->with('contracts', $contracts);
    }

    /**
     * New contract functionality
     */
    public function displayNewContract() {
        return view('contracts.admin.newcontract');
    }

    public function storeNewContract(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'date' => 'required',
            'body' => 'required',
            'type' => 'required',
        ]);

        $date = new Carbon($request->date);
        $body = nl2br($request->body);

        //Store the contract in the database
        $contract = new Contract;
        $contract->title = $request->name;
        $contract->end_date = $request->date;
        $contract->body = $body;
        $contract->type = $request->type;
        $contract->save();

        return redirect('/contracts/admin/display')->with('success', 'Contract written.');
    }

    /**
     * Used to store a finished contract in the database
     */
    public function storeAcceptContract(Request $request) {
        $this->validate($request, [
            'contract_id' => 'required',
            'bid_id' => 'required',
            'character_id' => 'required',
            'bid_amount' => 'required',
        ]);

        //Update the contract
        Contract::where([
            'contract_id' => $request->contract_id,
        ])->update([
            'finished' => true,
            'final_cost' => $request->bid_amount,
        ]);

        //Save the accepted bid in the database
        $accepted = new AcceptedBid;
        $accepted->contract_id = $request->contract_id;
        $accepted->bid_id = $request->bid_id;
        $accepted->bid_amount = $request->bid_amount;
        $accepted->save();

        return redirect('/contracts/admin/display')->with('success', 'Contract accepted and closed.');
    }

    /**
     * Delete a contract from every user
     */
    public function deleteContract($id) {

        Contract::where(['contract_id' => $id])->delete();

        Bid::where(['contract_id' => $id])->delete();

        return redirect('/contracts/admin/display')->with('success', 'Contract has been deleted.');
    }

    /**
     * End Contract Functionality
     */
    public function displayEndContract($id) {
        //Gather the information for the contract, and all bids on the contract
        $contract = Contract::where(['contract_id' => $id])->first()->toArray();
        $bids = Bid::where(['contract_id' => $id])->get()->toArray();

        return view('contracts.admin.displayend')->with('contract', $contract)
                                                  ->with('bids', $bids);
    }

    public function storeEndContract(Request $request) {
        $this->validate($request, [
            'contract_id' => 'required',
            'accept' => 'required',
        ]);

        //Declare class variables
        $mail = new Mail;
        $tries = 1;

        $contract = Contract::where(['contract_id' => $request->contract_id])->first()->toArray();
        $bid = Bid::where(['id' => $request->accept, 'contract_id' => $request->contract_id])->first()->toArray();

        //Send mail out to winner of the contract
        $subject = 'Contract Won';
        $body = 'You have been accepted to perform the following contract:<br>';
        $body .= $contract['contract_id'] . ' : ' . $contract['title'] . '<br>';
        $body .= 'Notes:<br>';
        $body .= $contract['body'] . '<br>';
        $body .= 'Please remit contract when the items are ready to Spatial Forces.  Description should be the contract identification number.  Request ISK should be the bid amount.';
        $body .= 'Sincerely,<br>Spatial Forces Contracting Department';
        while($mail->SendMail($bid['character_id'], 'character', $subject, $body)) {
            $tries++;
            if($tries == 5) {
                TidyContract($contract, $bid);

                return redirect('/contracts/admin/display')->with('error', 'Could not deliver mail.  Please manually send the mail to the winner.');
            }
        }
        
        TidyContract($contract, $bid);
        
        return redirect('/contracts/admin/display')->with('success', 'Contract finalized.  Mail took ' . $tries . ' attempt to send to the winner.');
    }

    private function TidyContract($contract, $bid) {
        Contract::where(['contract_id' => $contract['contract_id']])->update([
            'finished' => true,
        ]);

        //Create the accepted contract entry into the table
        $accepted = new AcceptedBid;
        $accepted->contract_id = $contract['contract_id'];
        $accepted->bid_id = $bid['id'];
        $accepted->bid_amount = $bid['bid_amount'];
        $accepted->notes = $bid['notes'];
        $accepted->save();
    }
}
