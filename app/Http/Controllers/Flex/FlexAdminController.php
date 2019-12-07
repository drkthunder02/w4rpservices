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
use App\Library\Lookups\NewLookupHelper;
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

        dd($structures);

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
        $lookup = new NewLookupHelper;

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
        $flex->save();

        return redirect('/flex/display')->with('success', 'Flex Structure Added.');
    }

    /**
     * Funciton to remove flex structure from the database
     */
    public function removeFlexStructure(Request $request) {
        $this->validate($request, [
            'requestor_name' => 'required',
            'system' => 'required',
            'structure_type' => 'required',
        ]);

        FlexStructure::where([
            'requestor_name' => $request->requestor_name,
            'system' => $request->system,
            'structure_type' => $request->structure_type,
        ])->delete();

        return redirect('/flex/display')->with('success', 'Flex Structure Entry Removed.');
    }

}
