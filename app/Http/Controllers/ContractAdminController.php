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

class ContractAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:contract.admin');
    }

    public function displayContractDashboard() {
        $today = Carbon::now();

        $contracts = Contract::where(['date', '>=', $today])->get();

        return view('contracts/admin/contractpanel');
    }

    public function displayNewContract() {

        return view('contracts/admin/newcontract');
    }

    public function storeNewContract() {

        return redirect('/contracts/admin/display');
    }

    public function storeAcceptContract(Request $request) {

        return redirect('/contracts/admin/display');
    }

    public function deleteContract(Request $request) {

        return redirect('/contracts/admin/display');
    }
}
