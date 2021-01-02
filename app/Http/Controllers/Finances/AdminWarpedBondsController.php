<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminWarpedBondsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
        $this->middleware('permission:admin.bonds');
    }

    public function DisplayNewBondForm() {

    }

    public function StoreNewBond() {

    }

    public function DeleteBond() {

    }
}
