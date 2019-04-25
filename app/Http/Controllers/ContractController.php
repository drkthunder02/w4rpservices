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

    public function displayPublicContracts() {

        return view('contracts/publiccontract');
    }

    public function displayPrivateContracts() {

        return view ('contracts/privatecontract');
    }

    public function storeBid(Request $request) {

        return redirect('contracts/publiccontract');
    }

    public function deleteBid(Request $request) {

        return redirect('contracts/publiccontract');
    }
}
