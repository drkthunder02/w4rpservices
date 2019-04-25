<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

//Libraries
//use App\Library\Contracts\ContractHelper;

//Models
use App\User;
use App\Models\User\UserPermission;

class ContractController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayContracts() {

        return view('contracts/display');
    }

    public function storeBid(Request $request) {

        return redirect('contracts/display');
    }

    public function deleteBid(Request $request) {

        return redirect('contracts/display');
    }
}
