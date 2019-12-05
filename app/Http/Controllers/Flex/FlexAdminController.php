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
    public function displayFlexStructure() {

        //Get the structures from the database
        $structures = FlexStructure::all();

        return view('flex.view')->with('structures', $structures);
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

        ]);

        return redirect('/flex/display')->with('success', 'Flex Structure Added.');
    }

    /**
     * Funciton to remove flex structure from the database
     */
    public function removeFlexStructure(Request $request) {
        $this->validate($request, [

        ]);
    }

}
