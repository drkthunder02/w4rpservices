<?php

namespace App\Http\Controllers\Fuel;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;
use Charts;

//Library Helpers
use App\Library\Assets\AssetHelper;
use App\Library\Structures\StructureHelper;

//Models
use App\Models\Structure\Structure;

class FuelController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayStructures() {
        //Declare variables
        $jumpGates = array();
        $lava = new Lavacharts;

        //Declare the class helpers
        $sHelper = new StructureHelper(null, null, null);
        $aHelper = new AssetHelper(null, null, null);

        //Setup the charts
        $gauge = $lava->DataTable();
        $gauge->addStringColumn('Fuel')
              ->addNumberColumn('Units');

        //Get all of the jump gates
        $gates = $sHelper->GetStructuresByType('Ansiblex Jump Gate');

        foreach($gates as $gate) {
            $liquidOzone = $aHelper->GetAssetByType(16273, $gate->structure_id);
            $temp = [
                'name' => $gate->structure_name,
                'system' => $gate->solar_system_name,
                'fuel_expires' => $gate->fuel_expires,
                'liquid_ozone' => $liquidOzone,
                'link' => '/logistics/fuel/display/' . $gate->structure_id . '/',
            ];

            array_push($jumpGates, $temp);

            if($liquidOzone > 1000000) {
                $liquidOzone = 1000000;
            }

            $gauge->addRow([$gate->solar_system_name, $liquidOzone]);
        }

        $lava->GaugeChart('Liquid Ozone', $gauge, [
            'min' => 0,
            'max' => 1000000,
            'greenFrom' => 0,
            'greenTo' => 150000,
            'greenColor' => '#DC3912',
            'yellowFrom' => 150000,
            'yellowTo' => 300000,
            'yellowColor' => '#FF9900',
            'redFrom' => 300000,
            'redTo' => 1000000,
            'redColor' => '#109618',
            'majorTicks' => [
                'Empty',
                'Full',
            ],
        ]);

        return view('logistics.display.fuel')->with('jumpGates', $jumpGates)
                                             ->with('lava', $lava);
    }

}
