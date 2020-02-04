<?php

namespace App\Http\Controllers\Flex;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;

//Models
use App\Models\Flex\FlexStructure;

//Library
use App\Library\Lookups\LookupHelper;
use App\Library\Esi\Esi;

class FlexAdminController extends Controller
{
    //Constructor
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Function to display all active flex structures and
     * the information regarding the flex structure
     */
    public function displayFlexStructures() {
        //Get the structures from the database
        $structures = FlexStructure::all();

        //Return the view with the data
        return view('flex.list')->with('structures', $structures);
    }

    /**
     * Function to display form for adding new flex structure
     */
    public function displayAddFlexStructure() {
        return view('flex.add');
    }

    /**
     * Function to add new flex structure to the database
     */
    public function addFlexStructure(Request $request) {
        $this->validate($request, [
            'requestor_name' => 'required',
            'requestor_corp_name' => 'required',
            'system' => 'required',
            'structure_type' => 'required',
            'structure_cost' => 'required',
        ]);

        //Delcare variables and classes
        $lookup = new LookupHelper;

        //From the character name find the character id
        $charId = $lookup->CharacterNameToId($request->requestor_name);

        //From the corporation name find the corporation id
        $corpId = $lookup->CorporationNameToId($request->requestor_corp_name);

        //From the system name find the system id
        $systemId = $lookup->SystemNameToId($request->system);

        //Create the database model
        $flex = new FlexStructure;
        $flex->requestor_id = $charId;
        $flex->requestor_name = $request->requestor_name;
        $flex->requestor_corp_id = $corpId;
        $flex->requestor_corp_name = $request->requestor_corp_name;
        $flex->system_id = $systemId;
        $flex->system = $request->system;
        $flex->structure_type = $request->structure_type;
        $flex->structure_cost = $request->structure_cost;
        if(isset($request->paid_until)) {
            $flex->paid_until = $request->paid_until;
        }
        $flex->save();

        return redirect('/flex/display')->with('success', 'Flex Structure Added.');
    }

    /**
     * Function to update paid until section of the flex structure in the database
     */
    public function updateFlexStructure(Request $request) {
        $this->validate($request, [
            'paid_until' => 'required',
            'requestor_id' => 'required',
            'requestor_corp_id' => 'required',
            'system_id' => 'required',
            'structure_type' => 'required',
        ]);

        FlexStructure::where([
            'requestor_id' => $request->requestor_id,
            'requestor_corp_id' => $request->requestor_corp_id,
            'system_id' => $request->system_id,
            'structure_type' => $request->structure_type,
        ])->update([
            'paid_until' => $request->paid_until,
        ]);
    }

    /**
     * Funciton to remove flex structure from the database
     */
    public function removeFlexStructure(Request $request) {
        $this->validate($request, [
            'requestor_id' => 'required',
            'requestor_corp_id' => 'required',
            'system_id' => 'required',
            'structure_type' => 'required',
        ]);

        FlexStructure::where([
            'requestor_id' => $request->requestor_id,
            'requestor_corp_id' => $request->requestor_corp_id,
            'system' => $request->system_id,
            'structure_type' => $request->structure_type,
        ])->delete();

        return redirect('/flex/display')->with('success', 'Flex Structure Entry Removed.');
    }

}
