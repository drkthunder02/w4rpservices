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

class FuelController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
        //$this->middleware('permission:logistics.structures');
    }

    public function displayStructures() {
        $aHelper = new AssetHelper;
        $sHelper = new StructureHelper;


    }

    public function displayStructureFuel() {
        $aHelper = new AssetHelper;
        $sHelper = new StructureHelper;
        $lava = new Lavacharts;
        $gates = array();
        $i = 1;

        $jumpGates = $sHelper->GetStructuresByType('Ansiblex Jump Gate');
        $cynoBeacons = $sHelper->GetStructuresByTypes('Pharolux Cyno Beacon');
        $cynoJammers = $sHelper->GetSTructureByTypes('Tenebrex Cyno Jammer');

        foreach($jumpGates as $jump) {
            //Liquid Ozone's item id is 16273
            $lo = $aHelper->GetAssetByType(16273, $jump->structure_id);

            $temp = [
                'id' => $i,
                'structure_id' => $jump->structure_id,
                'structure_name' => $jump->structure_name,
                'solar_system_name' => $jump->solar_system_name,
                'fuel_expires' => $jump->fuel_expires,
                'liquid_ozone' => $lo,
                'row' => 'Liquid Ozone ' . $i,
                'div' => 'Liquid-Ozone-' . $i . '-div',
            ];

            array_push($gates, $temp);
            $i++;
        }

        foreach($gates as $gate) {
            $gateChart = $lava->DataTable();
            $gateChart->addStringColumn($gate->structure_name)
                      ->addNumberColumn('Liquid Ozone')
                      ->addRow([$gate->row, $gate->lo]);

            $lava->GaugeChart($gate->row, $gateChart, [
                'width' => 300,
                'redFrom' => 0,
                'redTo' => 50000,
                'yellowFrom' => 50000,
                'yellowTo' => 150000,
                'greenFrom' => 150000,
                'greenTo' => 1000000,
                'majorTicks' => [
                    'Critical',
                    'Ok',                    
                ],
            ]);
        }

        return view('logistics.display.fuel')->with('lava', $lava)
                                             ->with('gates', $gates);
    }
}
