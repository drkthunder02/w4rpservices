<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\Models\CorpStructure;

class RegisterStructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Director');
    }

    public function displayRegisterStructure() {
        return view('structures.register');
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
