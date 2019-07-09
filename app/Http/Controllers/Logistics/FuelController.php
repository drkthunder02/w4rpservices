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

//Library Helpers
use App\Library\Assets\AssetHelper;
use App\Library\Structures\StructureHelper;

//Models
use App\Models\Structure\Structure;

class FuelController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:logistics.manager');
    }

    public function displayStructures() {
        //Declare variables
        $jumpGates = array();

        //Declare the class helpers
        $sHelper = new StructureHelper(null, null, null);
        $aHelper = new AssetHelper(null, null, null);
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
        }

        return view('logistics.display.fuel')->with('jumpGates', $jumpGates);
    }

    /**
     * Get the structure id passed to it and display fuel gauage for structure
     */
    public function displayStructureFuel($id) {
        //Declare class variables
        $lava = new Lavacharts;
        $aHelper = new AssetHelper(null, null, null);

        $structure = Structure::where(['structure_id' => $id])->first();
        $name = $structure['structure_name'];

        //Get the quantity of liquid ozone in the structure
        $liquidOzone = $aHelper->GetAssetByType(16273, $id);
        
        $gauge = $lava->DataTable();
        $gauge->addStringColumn('Fuel')
              ->addNumberColumn('Units')
              ->addRow(['Liquid Ozone', $liquidOzone]);
        $lava->GaugeChart('Liquid Ozone', $gauge, [
            'width' => 400,
            'redFrom' => 0,
            'redTo' => 75000,
            'yellowFrom' => 75000,
            'yellowTo' => 150000,
            'greenFrom' => 150000,
            'greenTo' => 1000000,
            'majorTicks' => [
                'Empty',
                'Ok',
            ],
        ]);

        //Return the view
        return view('logistics.display.fuelgauge')->with('lava', $lava)
                                                   ->with('name', $name);
    }
}
