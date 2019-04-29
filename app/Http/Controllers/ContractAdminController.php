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
use App\Models\Contracts\AcceptedBid;

class ContractAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:contract.admin');
    }

    public function displayContractDashboard() {
        $today = Carbon::now();

        $contracts = Contract::where('end_date', '>=', $today)->get();

        return view('contracts.admin.contractpanel')->with('contracts', $contracts);
    }

    public function displayNewContract() {
        return view('contracts.admin.newcontract');
    }

    public function storeNewContract(Request $request) {
        $this->validate($request, [
            'name',
            'date',
            'body',
            'type',
        ]);

        $date = new Carbon($request->date);

        //Store the contract in the database
        $contract = new Contract;
        $contract->title = $request->name;
        $contract->end_date = $request->date;
        $contract->body = $request->body;
        $contract->type = $request->type;
        $contract->save();

        return redirect('/contracts/admin/display')->with('success', 'Contract written.');
    }

    public function storeAcceptContract(Request $request) {
        $this->validate($request, [
            'contract_id',
            'bid_id',
            'character_id',
            'bid_amount',
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

    public function deleteContract(Request $request) {
        $this->validate($request, [
            'contract_id',
        ]);

        Contract::where([
            'contract_id' => $request->contract_id,
        ])->delete();

        return redirect('/contracts/admin/display')->with('success', 'Contract has been deleted.');
    }

    public function displayEndContract($id) {
        $contractId = $id;

        return view('contracts.admin.endcontract')->with('contractId', $contractId);
    }

    public function storeEndContract(Request $request) {

    }
}
