<?php

namespace App\Http\Controllers\Structures;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

use App\Models\Corporation\CorpStructure;
use App\Models\Corporation\CorpTaxRatio;
use App\Library\Esi\Esi;

class RegisterStructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:structure.operator');
    }

    public function displayRegisterStructure() {
        //Check to see if the user has the read corp journal esi scope before allowing to register a structure
        if(Auth()->user()->hasEsiScope('esi-wallet.read_corporation_wallets.v1')) {
            return view('structures.register.register');
        } else {
            return view('dashboard')->with('error', 'You need to setup your esi scope for read corporation wallets');
        }
    }

    public function storeStructure(Request $request) {
        $this->validate($request, [
            'system' => 'required',
            'structure_name' => 'required',
            'tax' => 'required',
            'structure_type' => 'required',
        ]);

        $eHelper = new Esi;

        $tax = floatval($request->tax);

        $structure = new CorpStructure();
        $structure->character_id = Auth::user()->character_id;
        $structure->corporation_id = $eHelper->FindCorporationId(Auth::user()->character_id);
        $structure->corporation_name = $eHelper->FindCorporationName(Auth::user()->character_id);
        $structure->region = $request->region;
        $structure->system = $request->system;
        $structure->structure_name = $request->structure_name;
        $structure->structure_type = $request->structure_type;
        $structure->save();

        //Return the view and the message of user updated
        return redirect('/dashboard')->with('success', 'Structure Added to Database');
    }
}
