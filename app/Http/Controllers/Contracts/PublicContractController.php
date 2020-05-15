<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Models
use App\Models\Market\MarketRegionOrder;
use App\Models\PublicContracts\PublicContract;
use App\Models\PublicContracts\PublicContractItem;

class PublicContractController extends Controller
{
    /**
     * Private variables
     */
    private $regions = [
        'Immensea' => 10000025,
        'Catch' => 10000014,
        'Tenerifis' => 10000061,
        'The Forge' => 10000002,
        'Impass' => 10000031,
        'Esoteria' => 10000039,
        'Detorid' => 10000005,
        'Omist' => 10000062,
        'Feythabolis' => 10000056,
        'Insmother' => 10000009,
    ];

    /**
     * Contracts construct
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    /**
     * Display the contracts in a region
     */
    public function displayRegionalContracts() {
        //Declare variables
        $arrContracts = array();
        
        //Get the contracts by region
        foreach($region as $key => $value) {
            $contracts = PublicContract::where([
                'region_id' => $value,
            ])->get()->toArray();

            //Compile the array
            foreach($contracts as $contract) {
                array_push($arrContracts, $contract);
            }
        }

        return view('contracts.regional.user.displaycontracts');
    }
}
