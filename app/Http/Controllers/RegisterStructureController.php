<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;

use App\Models\Corporation\CorpStructure;
use App\Library\Esi;

class RegisterStructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Director');
    }

    public function displayRegisterStructure() {
        //Check to see if the user has the read corp journal esi scope before allowing to register a structure
        if(Auth()->user()->hasEsiScope('esi-wallet.read_corporation_wallets.v1')) {
            return view('structures.register');
        } else {
            return view('dashboard')->with('error', 'You need to setup your esi scope for read corporation wallets');
        }
    }

    public function storeStructure(Request $request) {
        $this->validate($request, [
            'corporation_id' => 'required',
            'corporation_name' => 'required',
            'system' => 'required',
            'structure_name' => 'required',
            'tax' => 'required',
            'structure_type' => 'required',
        ]);

        $tax = floatval($request->tax);

        $structure = new CorpStructure();
        $structure->character_id = Auth::user()->character_id;
        $structure->corporation_id = $request->corporation_id;
        $structure->corporation_name = $request->corporation_name;
        $structure->region = $request->region;
        $structure->system = $request->system;
        $structure->structure_name = $request->structure_name;
        $structure->tax = $tax;
        $structure->structure_type = $request->structure_type;
        $structure->save();

        //Return the view and the message of user updated
        return redirect('/dashboard')->with('success', 'Structure Added to Database');
    }
}
