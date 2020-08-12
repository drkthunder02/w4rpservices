<?php

namespace App\Http\Controllers\SystemRentals;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;

//Models
use App\Models\SystemRentals\RentalSystem;

//Library
use App\Library\Lookups\LookupHelper;
use App\Library\Esi\Esi;

class RentalAdminController extends Controller
{
    //Constructor
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Function to display all active rental systems and
     * the information regarding the rental systems
     */
    public function displayRentalSystems() {
        //Get the rental systems from the database
        $rentals = RentalSystem::all();

        //Return the view with the data
        return view('rental.list')->with('rentals', $rentals);
    }

    /**
     * Function to display form for adding new rental system
     */
    public function displayAddRentalSystem() {
        return view('rental.add');
    }

    /**
     * Function to add new rental system to the database
     */
    public function addRentalSystem(Request $request) {
        $this->validate($request, [
            'contact_name' => 'required',
            'contact_corp_name' => 'required',
            'system' => 'required',
            'rental_cost' => 'required',
            'paid_until' => 'required',
        ]);

        //Declare the variables and classes needed
        $lookup = new LookupHelper;

        //From the character name find the character id
        $charId = $lookup->CharacterNameToId($request->contact_name);

        //From the corporation name find the corporation id
        $corpId = $lookup->CorporationNameToId($request->contact_corp_name);

        //From the system name find the system id
        $systemId = $lookup->SystemNameToId($request->system);

        //Sanitize the bid amount
        if(preg_match('(m|M|b|B)', $request->rental_cost) === 1) {
            if(preg_match('(m|M)', $request->rental_cost) === 1) {
                $cStringSize = strlen($request->rental_cost);
                $tempCol = str_split($request->rental_cost, $cStringSize - 1);
                $rentalCost = $tempCol[0];
                $rentalCost = $rentalCost * 1000000000.00;
            } else if(preg_match('(b|B)', $request->rental_cost) === 1) {
                $cStringSize = strlen($request->rental_cost);
                $tempCol = str_split($request->rental_cost, $cStringSize - 1);
                $rentalCost = $tempCol[0];
                $rentalCost = $rentalCost * 1000000000000.00;
            }
        } else {
            $rentalCost = $request->rental_cost;
        }

        //Create the database model
        $rental = new RentalSystem;
        $rental->contact_id = $charId;
        $rental->contact_name = $request->contact_name;
        $rental->corporation_id = $corpId;
        $rental->corporation_name = $request->contact_corp_name;
        $rental->system_id = $systemId;
        $rental->system_name = $request->system;
        $rental->rental_cost = $rentalCost;
        $rental->paid_until = $request->paid_until;
        $rental->save();

        return redirect('/system/rental/display')->with('success', 'Rental System Added.');
    }

    /**
     * Function to update paid until section of the rental system in the database
     */
    public function updateRentalSystem(Request $request) {
        $this->validate($request, [
            'paid_until' => 'required',
            'contact_id' => 'required',
            'corporation_id' => 'required',
            'system' => 'required',
        ]);

        RentalSystem::where([
            'character_id' => $request->contact_id,
            'corporation_id' => $request->corporation_id,
            'system_id' => $request->system,
        ])->update([
            'paid_until' => $request->paid_until,
        ]);

        return redirect('/rental/display')->with('success', 'Rental System updated.');
    }

    /**
     * Function to remove rental system from the database
     */
    public function removeRentalSystem(Request $request) {
        $this->validate($request, [
            'contact_id' => 'required',
            'corporation_id' => 'required',
            'system' => 'reuquired',
        ]);

        RentalSystem::where([
            'contact_id' => $request->contact_id,
            'corporation_id' => $request->corporation_id,
            'system_id' => $request->system,
        ])->delete();

        return redirect('/rental/display')->with('success', 'Removed renter from database.');
    }
}
