<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

//Libraries
use App\Library\Esi\Mail;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Models
use App\Models\User\User;
use App\Models\User\UserPermission;
use App\Models\Contracts\Contract;
use App\Models\Contracts\Bid;
use App\Models\Contracts\AcceptedBid;
use App\Models\Jobs\JobSendEveMail;

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

        //Send a mail out to all of the people who can bid on a contract
        $this->NewContractMail();

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
        $tries = 1;

        //Get the esi config
        $config = config('esi');

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

        //Setup the mail job
        $mail = new JobSendEveMail;
        $mail->subject = $subject;
        $mail->recipient_type = 'character';
        $mail->recipient = $bid['character_id'];
        $mail->body = $body;
        $mail->sender = $config['primary'];
        //Dispatch the mail job
        ProcessSendEveMailJob::dispatch($mail)->onQueue('mail');
        
        //Tidy up the contract by doing a few things.
        $this->TidyContract($contract, $bid);
        
        //Redirect back to the contract admin dashboard.
        return redirect('/contracts/admin/display')->with('success', 'Contract finalized.  Mail has been sent to the queue for processing.');
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

    private function NewContractMail() {
        //Get all the users with a specific permission set
        $users = UserPermission::where(['permission' => 'contract.canbid'])->get()->toArray();

        //Get the esi config
        $config = config('esi');

        //Cycle through the users with the correct permission and send a mail to go out with the queue system.
        foreach($users as $user) {
                $mail = new JobSendEveMail;
                $mail->sender = $config['primary'];
                $mail->subject = 'New Alliance Contract Available';
                $mail->recipient = $user['character_id'];
                $mail->recipient_type = 'character';
                $mail->body = "A new contract is available for the alliance contracting system.  Please check out <a href='https://services.w4rp.space'>Services Site</a>.";
                ProcessSendEveMailJob::dispatch($mail)->onQueue('mail');
        }
    }
}
