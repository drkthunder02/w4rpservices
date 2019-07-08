<?php

namespace App\Http\Controllers;

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
        $this->middleware('permission:logistics.structures');
    }

    public function displayStructures() {
        $aHelper = new AssetHelper;
        $sHelper = new StructureHelper;


    }

    public function displayStructureFuel() {
        $aHelper = new AssetHelper;
        $sHelper = new StructureHelper;

        
    }
}
