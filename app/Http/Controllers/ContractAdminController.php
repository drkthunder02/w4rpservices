<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

//Libraries
//use App\Library\Contracts\ContractHelper;

//Models
use App\User;
use App\Models\User\UserPermission;

class ContractAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayContractDashboard() {

        return view('contracts/admin/display');
    }

    public function displayNewContract() {

        return view('contracts/admin/new');
    }

    public function storeNewContract() {

        return redirect('contracts/admin/display');
    }

    public function storeAcceptContract(Request $request) {

        return redirect('contracts/admin/display');
    }

    public function deleteContract(Request $request) {

        return redirect('contracts/admin/display');
    }
}
