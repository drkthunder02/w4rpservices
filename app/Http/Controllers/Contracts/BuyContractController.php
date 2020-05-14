<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyContractController extends Controller
{
    /**
     * Contracts construct
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    /**
     * Display region form to pick different regions such as Esoteria, Catch, Immensea
     */
    public function displayRegionalContractForm() {
        return view('contracts.regional.user.displayregion');
    }

    /**
     * Display the contracts in a region
     */
    public function displayRegionalContracts() {
        $this->validate('request', [
            'region_id' => 'required',
        ]);

        return view('contracts.regional.user.displaycontracts');
    }
}
