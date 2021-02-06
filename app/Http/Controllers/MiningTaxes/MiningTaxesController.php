<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;

//Library Helpers
use App\Library\Lookups\LookupHelper;
use App\Library\Structures\StructureHelper;

//Models
use App\Models\Moon\ItemComposition;
use App\Models\Moon\MineralPrice;
use App\Models\MiningTax\Ledger;
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Invoice;

class MiningTaxesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function DisplayUpcomingExtractions() {
        
        //Declare variables
        $structures = array();
        $sHelper = new StructureHelper;

        //Get all the current observers from the database
        $observers = Observer::all();

        foreach($observers as $obs) {
            $extraction = $sHelper->GetExtractions();
        }
    }

    public function DisplayMiningMoons() {

    }

    public function DisplayAccruedTaxes() {

    }

    public function DisplayLedgers() {

    }
}
