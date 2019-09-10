<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use DB;

//App Library
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Structures\StructureHelper;

//App Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Structure\Structure;
use App\Models\Structure\Service;

class MoonLedgerController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:corp.lead');
    }

    public function displaySelection() {
        //Declare variables
        $structures = array();

        

        return view('moons.ledger.displayselect')->with('structures', $structures);
    }

    public function displayLedger(Request $request) {
        //Validate the request
        $this->validate($request, [
            'id' => 'required',
        ]);

        //Declare variables
        $esiHelper = new Esi;

        //Create the authentication container for ESI, and check for the correct scopes
        $config = config('esi');
    }
}
