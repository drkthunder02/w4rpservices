<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Log;

//Models
use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\RentalMoon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;
//use App\Models\MoonRent\MoonRental;
//use App\Models\Moon\AllianceMoon;
use App\Models\MoonRent\AllianceRentalMoon;
use App\Models\Moon\AllianceMoonRequest;

//Library
use App\Library\Moons\MoonCalc;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

class MoonsAdminController extends Controller
{
    /**
     * Constructor for the class
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Function to display moon requests
     */
    public function displayMoonRequests() {
        $requests = AllianceMoonRequest::where([
            'status' => 'Pending',
        ])->get();

        return view('moons.admin.moonrequest')->with('requests', $requests);
    }

    /**
     * Function to approve a moon request
     */
    public function storeApprovedMoonRequest(Request $request) {
        //Validate the input request
        $this->validate($request, [
            'id' => 'required',
            'status' => 'required',
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
        ]);

        
        //Get the request data which holds all of the information for the request user
        $moon = AllianceMoonRequest::where([
            'id' => $request->id,
        ])->first();

        //Get the configuration data to use later in the function
        $config = config('esi');

        //If the request is approved, then update everything.
        if($request->status == 'Approved') {
            //Update the alliance moon request to either approved or denied
            AllianceMoonRequest::where([
                'id' => $request->id,
            ])->update([
                'status' => $request->status,
                'approver_name' => auth()->user()->getName(),
                'approver_id' => auth()->user()->getId(),
            ]);

            //Update the alliance moon in the table to the correct status
            AllianceMoon::where([
                'System' => $request->system,
                'Planet' => $request->planet,
                'Moon' => $request->moon,
            ])->update([
                'Corporation' => $moon->corporation_ticker,
                'Availability' => 'Deployed',
            ]);

            //Send an eve mail to the requestor stating they can set a moon up.
            //Setup the mail body
            $body = 'The moon request for ' . $request->system . ' - ' . $request->planet . ' - ' . $request->moon . ' has changed status.<br>';
            $body .= 'The request has been ' . $request->status . '.<br>';
            $body .= 'Please contact the FC Team should it be necessary to arrange a fleet to cover the structure drop.<br>';
            $body .= 'Sincerely,<br>';
            $body .= 'Warped Intentions Leadership<br>';

            
        } else {
            //If the status was Denied, then update the request, and mark the moon available again.
            AllianceMoonRequest::where([
                'id' => $request->id,
            ])->update([
                'status' => $request->status,
                'approver_name' => auth()->user()->getName(),
                'approver_id' => auth()->user()->getId(),
            ]);

            //Update the alliance moon in the table to the correct status
            AllianceMoon::where([
                'System' => (string)$request->system,
                'Planet' => (string)$request->planet,
                'Moon' => (string)$request->moon,
            ])->update([
                'Availability' => 'Available',
            ]);

            //Send an eve mail to the requestor stating they can set a moon up.
            //Setup the mail body
            $body = 'The moon request for ' . $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon . ' has changed status.<br>';
            $body .= 'The request has been ' . $request->status . '.<br>';
            $body .= 'Should you have questions please contact alliance leadership for further clarification.<br>';
            $body .= 'Sincerely,<br>';
            $body .= 'Warped Intentions Leadership<br>';
            
        }

        //Setup the mail model
        ProcessSendEveMailJob::dispatch($body, (int)$moon->requestor_id, 'character', 'Warped Intentions Moon Request', $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds(5));

        return redirect('/moons/admin/display/request')->with('success', 'Moon has been processed, and mail has been sent out.');
    }

    /**
     * Function to display the ability for the admins to update moons with who is renting,
     * and when it ends
     */
    public function updateMoonNew() {
        $this->middleware('role:Admin');

        //Declare the variables we need
        $system = null;
        $planet = null;
        $moon = null;
        $name = null;
        $spmnTemp = array();
        $spmn = array();

        //Get the moons and put in order by System, Planet, Moon number
        $moons = AllianceRentalMoon::orderBy('System', 'ASC')
                                   ->orderBy('Planet', 'ASC')
                                   ->orderBy('Moon', 'ASC')
                                   ->get();

        //Push our default value onto the stack
        array_push($spmn, 'N/A');

        //Form our array of strings for each system, planet, and moon combination
        foreach($moons as $m) {
            $temp = $m->system . " - " . $m->planet . " - " . $m->moon . " - " . $m->structure_name;
            array_push($spmnTemp, $temp);
        }

        //From the temporary array, build the final array
        foreach($spmnTemp as $key => $value) {
            $spmn[$value] = $value;
        }

        //Pass the data to the blade display
        return view('moons.admin.updatemoon')->with('spmn', $spmn);
    }

    /**
     * Function to remove a renter from a moon
     * New function based on new table.  Will
     * update description in a future update.
     */
    public function storeMoonRemovalNew(Request $request) {
        //Check for the correct role for the user to utilize this function
        $this->middleware('role:Admin');

        //Validate the request
        $this->validate($request, [
            'remove' => 'required',
        ]);

        //Explode the remove request to an array of strings
        $str_array = explode(" - ", $request->remove);

        //Decode the value for the SPM into a system, planet, and moon
        $system = $str_array[0];
        $planet = $str_array[1];
        $moon = $str_array[2];

        //Update the moon rental
        AllianceMoonRental::where([
            'system' => $system,
            'planet' => $planet,
            'moon' => $moon,
        ])->update([
            'rental_type' => 'Not Rented',
            'rental_until' => null,
            'rental_contact_id' => 0,
            'rental_contact_type' => 'Not Rented',
            'paid' => 'Not Rented',
            'paid_until' => null,
            'alliance_use_until' => null,
        ]);
    }

    /**
     * Function to display the moons to admins
     * New function based on new table.  Will
     * update description in a future update.
     */
    public function displayRentalMoonsAdminNew() {
        //Declare variables for the function
        $lookupHelper = new LookupHelper;
        $moonCalc = new MoonCalc;
        $contactId = null;
        $contactType = null;
        $paid = null;
        $paidUntil = null;
        $corpTicker = null;
        $table = array();
        //Setup the carbon date using Carbon\Carbon
        $lastMonth = Carbon::now()->subMonth();
        $today = Carbon::now();

        //Get the moon rentals from the database
        $rentalMoons = AllianceRentalMoon::all();

        //For each of the moons compile different data for the view for formatting
        foreach($rentalMoons as $moon) {
            //Check if a current rental for the moon is on going
            if(($moon->rental_type == 'In Alliance' || $moon->rental_type == 'Out of Alliance') && ($moon->paid == 'Yes')) {
                $paid = $moon->paid;
                $paidUntil = new Carbon($moon->paid_until);
                $paidUntil = $paidUntil->format('m-d');

                //Set the rental date up
                $rentalTemp = new Carbon($moon->rental_end);
                $rentalEnd = $rentalTemp->format('m-d');

                //Set the contact name based on the contact type
                if($moon->contact_type == 'Alliance') {
                    $allianceInfo = $lookupHelper->GetAllianceInfo($moon->contact);
                    $contact = $allianceInfo->name;
                    $ticker = $allianceInfo->ticker;
                } else if($moon->contact_type == 'Corporation') {
                    $corporationInfo = $lookupHelper->GetCorporationInfo($moon->contact);
                    $contact = $corporationInfo->name;
                    $ticker = $corporationInfo->ticker;
                } else if($moon->contact_type == 'Character') {
                    $characterInfo = $lookupHelper->GetCharacterInfo($moon->contact);
                    $contact = $characterInfo->name;
                    $ticker = $characterInfo->ticker;
                } else {
                    $contact = 'N/A';
                    $ticker = 'N/A';
                    $type = 'N/A';
                }

                //Set up the moon rental type
                if($moon->rental_type == 'In Alliance') {
                    $type = 'W4RP';
                } else if($moon->rental_type == 'Out of Alliance') {
                    $type = 'OOA';
                } else {
                    $type = 'N/A';
                }
                
            //Check if the moon is currently being utilized by the alliance
            } else if($moon->rental_type == 'Alliance') {
                //If the moon is in use by the alliance then the moon isn't paid for
                $paid = 'No';

                //Setup the rental end time as the end of the month
                $rentalTemp = $today->endOfMonth();
                $rentalEnd = $rentalTemp->format('m-d');

                //Setup the paid time as the same as the rental end
                $paidUntiltemp = $rentalTemp;
                $paidUntil = $rentalEnd;

                //Set the other information for the spreadsheet
                $contact = 'Spatial Forces';
                $renter = 'Spatial Forces';
                $ticker = 'SP3C';
                $type = 'Alliance';

            //The last case is the moon is not utilized by the Alliance or is not being rented
            } else {
                //If the moon is not being rented, or being utilized by the alliance then set paid to No
                $paid = 'No';

                //Setup the rental time to end as last month to show it's free
                $rentalTemp = $lastMonth;
                $rentalEnd = $rentalTemp->format('m-d');

                //Setup the paid until as last month to show it's free
                $paidUntilTemp = $lastMonth;
                $paidUntil = $lastMonth->format('m-d');

                //Setup the other variables with the correct information
                $contact = 'None';
                $renter = 'None';
                $ticker = 'N/A';
                $type = 'N/A';
            }

            //Set the color for the table
            if($moon->rental_type != 'Alliance') {
                if($rentalTemp->diffInDays($today) < 3) {
                    $color = 'table-warning';
                } else if($today > $rentalTemp) {
                    $color = 'table-success';
                } else {
                    $color = 'table-danger';
                }
            } else {
                $color = 'table-info';
            }

            //Add the data to the html string to be passed to the view
            array_push($table, [
                'SPM' => $moon->system . " - " . $moon->planet . " - " . $moon->moon,
                'StructureName' => $moon->structure_name,
                'AlliancePrice' => $moon->alliance_rental_price,
                'OutOfAlliancePrice' => $moon->out_of_alliance_rental_price,
                'RentalEnd' => $moon->rental_until,
                'RowColor' => $color,
                'Paid' => $moon->paid,
                'PaidUntil' => $moon->paid_until,
                'Contact' => $contact,
                'Type' => $moon->rental_type,
                'Renter' => $ticker,
            ]);
        }

        return view('moons.admin.adminmoon')->with('table', $table);
    }

    /**
     * Function to display the moons to admins
     */
    public function displayRentalMoonsAdmin() {
        $this->middleware('role:Admin');

        $lookupHelper = new LookupHelper;
        $contact = '';
        $paid = '';
        $rentalEnd = '';
        $renter = '';
        $ticker = '';

        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Get all of the moons from the database
        $moons = RentalMoon::orderBy('System', 'asc')->get();
        //Declare the html variable and set it to null
        $table = array();
        //Set carbon dates as needed
        $lastMonth = Carbon::now()->subMonth();
        $today = Carbon::now();

        foreach($moons as $moon) {
            //Get the rental data for the moon
            $count = MoonRental::where([
                'System' => $moon->System,
                'Planet' => $moon->Planet,
                'Moon' => $moon->Moon,
            ])->count();

            //Check if their is a current rental for a moon going on
            if($count == 0) {
                //If we don't find a rental record, mark the moon as not paid
                $paid = 'No';

                //If we don't find a rental record, set the rental date as last month
                $rentalTemp = $lastMonth;
                $rentalEnd = $rentalTemp->format('m-d');

                //If we don't find a rental record, set the paid until date as last month
                $paidUntilTemp = $lastMonth;
                $paidUntil = $paidUntilTemp->format('m-d');

                //Set the contact info
                $contact = 'None';

                //Set the renter info
                $renter = 'None';

                //Set the ticker info
                $ticker = 'N/A';

                //Set the type info as it's needed
                $type = 'N/A';
            } else {
                //Get the rental data for the moon
                $rental = MoonRental::where([
                    'System' => $moon->System,
                    'Planet' => $moon->Planet,
                    'Moon' => $moon->Moon,
                ])->first();

                //If we find a rental record, mark the moon as whether it's paid or not
                $paid = $rental->Paid;
                $paidUntil = new Carbon($rental->Paid_Until);
                $paidUntil = $paidUntil->format('m-d');

                //Set the rental date up
                $rentalTemp = new Carbon($rental->RentalEnd);
                $rentalEnd = $rentalTemp->format('m-d');

                //Set the contact name
                $contact = $lookupHelper->CharacterIdToName($rental->Contact);
                
                //Set up the renter whether it's a corporation in W4RP or another alliance
                $ticker = $rental->RentalCorp;
                $type = $rental->Type;
            }

            //Set the color for the table
            if($rentalTemp->diffInDays($today) < 3 ) {
                $color = 'table-warning';
            } else if( $today > $rentalTemp) {
                $color = 'table-success';
            } else {
                $color = 'table-danger';
            }

            //Calculate the price of the moon based on what is in the moon
            $price = $moonCalc->SpatialMoons($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
            
            //Add the data to the html string to be passed to the view
            array_push($table, [
                'SPM' => $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon,
                'StructureName' => $moon->StructureName,
                'AlliancePrice' => $price['alliance'],
                'OutOfAlliancePrice' => $price['outofalliance'],
                'RentalEnd' => $rentalEnd,
                'RowColor' => $color,
                'Paid' => $paid,
                'PaidUntil' => $paidUntil,
                'Contact' => $contact,
                'Type' => $type,
                'Renter' => $ticker,
            ]);
        }

        return view('moons.admin.adminmoon')->with('table', $table);
    }

    /**
     * Function to remove a renter from a moon
     */
    public function storeMoonRemoval(Request $request) {
        $this->middleware('role:Admin');

        $this->validate($request, [
            'remove' => 'required',
        ]);

        $str_array = explode(" - ", $request->remove);

        //Decode the value for the SPM into a system, planet, and moon for the database to update
        $system = $str_array[0];
        $planet = $str_array[1];
        $moon = $str_array[2];

        $found = MoonRental::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $moon,
        ])->count();

        if($found != 0) {
            MoonRental::where([
                'System' => $system,
                'Planet' => $planet,
                'Moon' => $moon,
            ])->delete();

            return redirect('/moons/admin/display/rentals')->with('success', 'Renter removed.');
        } 

        //Redirect back to the moon page, which should call the page to be displayed correctly
        return redirect('/moons/admin/display/rentals')->with('error', 'Something went wrong.');
    }

    /**
     * Function displays the ability for admins to update moons with who is renting, and when it ends.
     */
    public function updateMoon() {
        $this->middleware('role:Admin');

        //Declare some variables
        $system = null;
        $planet = null;
        $moon = null;
        $name = null;
        $spmnTemp = array();
        $spmn = array();

        //Get the moons and put in order by System, Planet, then Moon number
        $moons = RentalMoon::orderBy('System', 'ASC')
                       ->orderBy('Planet', 'ASC')
                       ->orderBy('Moon', 'ASC')
                       ->get();

        //Push our default value onto the stack
        array_push($spmn, 'N/A');

        //Form our array of strings for each system, planet, and moon combination.
        foreach($moons as $m) {
            $temp = $m->System . " - " . $m->Planet . " - " . $m->Moon . " - " . $m->StructureName;
            array_push($spmnTemp, $temp);
        }

        //From our temporary array with all the values in a numbered key, create the real array with the value being the key
        foreach($spmnTemp as $key => $value) {
            $spmn[$value] = $value;
        }

        //Return the view and the form from the blade display
        //Pass the data to the view as well
        return view('moons.admin.updatemoon')->with('spmn', $spmn);
    }

    /**
     * Store the updated moon
     */
    public function storeUpdateMoon(Request $request) {
        $this->middleware('role:Admin');

        //Declare some static variables as needed
        $moonCalc = new MoonCalc;
        $lookup = new LookupHelper;
        $paid = false;
        $system = null;
        $planet = null;
        $mn = null;
        $name = null;

        //Validate our request from the html form
        $this->validate($request, [
            'spmn' => 'required',
            'contact' => 'required',
            'paid_until' => 'required',
            'rental_end' => 'required',
        ]);

        //Decode the System, Planet, Moon, Name combinatio sent from the controller
        $str_array = explode(" - ", $request->spmn);
        $system = $str_array[0];
        $planet = $str_array[1];
        $mn = $str_array[2];
        $name = $str_array[3];

        //Take the  contact name and create a character_id from it
        if($request->contact == 'None') {
            $contact = -1;
        } else {
            $contact = $lookup->CharacterNameToId($request->contact);
        }

        //After we get the contact, from his name to the character Id, let's do some other functions before continuing.
        //Let's find the corporation and alliance information to ascertain whethery they are in Warped Intentions or another Legacy Alliance
        $char = $lookup->GetCharacterInfo($contact);
        //Takes the corp id and looks up the corporation info
        $corp = $lookup->GetCorporationInfo($char->corporation_id);
        $alliance = $lookup->GetAllianceInfo($corp->alliance_id);
        $allianceId = $corp->alliance_id;

        //Update the paid value for database entry
        if($request->paid == 'Yes') {
            $paid = 'Yes';
        } else {
            $paid = 'No';
        }
        
        //Create the rnetal ticker if the corp is in Warped Intentions, otherwise just display the alliance ticker
        if($allianceId == 99004116) {
            $renter = $corp->ticker;
        } else {
            $renter = $alliance->ticker;
        }

        //Create the paid until date
        if(isset($request->paid_until)) {
            $paidUntil = new Carbon($request->paid_until . '00:00:01');
        } else {
            $paidUntil = Carbon::now();
        }

        //Create the rental end date
        if(isset($request->rental_end)) {
            $rentalEnd = new Carbon($request->rental_end . '23:59:59');
        } else {
            $rentalEnd = Carbon::now();
        }

        //Calculate the price of the moon for when it's updated
        $moon = RentalMoon::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $mn,
        ])->first();

        //Calculate the price of the rental and store it in the database
        $price = $moonCalc->SpatialMoons($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                         $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

        //Count how many rentals we find for later database processing
        $count = MoonRental::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $mn,
            'Contact' => $contact,
        ])->count();
        
        //If the database entry isn't found, then insert it into the database,
        //otherwise, account for it being in the system already.
        //Also check for the weird condition of more than one moon entry existing
        if($count > 1) {
            //If more than one entry is found for a particular system, planet, moon combo, then delete all the entries, and put in 
            // a single new entry
            MoonRental::where([
                'System' => $system,
                'Planet' => $planet,
                'Moon' => $moon,
            ])->delete();

            if($allianceId == 99004116) {
                $store = new MoonRental;
                $store->System = $system;
                $store->Planet = $planet;
                $store->Moon = $mn;
                $store->RentalCorp = $renter;
                $store->RentalEnd = $rentalEnd;
                $store->Contact = $contact;
                $store->Price = $price['alliance'];
                $store->Type = 'alliance';
                $store->Paid = $paid;
                $store->Paid_Until = $paidUntil;
                $store->save();
            } else {
                $store = new MoonRental;
                $store->System = $system;
                $store->Planet = $planet;
                $store->Moon = $mn;
                $store->RentalCorp = $renter;
                $store->RentalEnd = $rentalEnd;
                $store->Contact = $contact;
                $store->Price = $price['outofalliance'];
                $store->Type = 'outofalliance';
                $store->Paid = $paid;
                $store->Paid_Until = $paidUntil;
                $store->save();
            }
        } else if($count == 1) {
            if($allianceId = 99004116) {
                MoonRental::where([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $mn,
                    'Contact' => $contact,
                ])->update([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $mn,
                    'RentalCorp' => $renter,
                    'RentalEnd' => $rentalEnd,
                    'Contact' => $contact,
                    'Price' => $price['alliance'],
                    'Type' => 'alliance',
                    'Paid' => $paid,
                    'Paid_Until' => $paidUntil,
                ]);
            } else {
                MoonRental::where([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $mn,
                    'Contact' => $contact,
                ])->update([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $mn,
                    'RentalCorp' => $renter,
                    'RentalEnd' => $rentalEnd,
                    'Contact' => $contact,
                    'Price' => $price['outofalliance'],
                    'Type' => 'outofalliance',
                    'Paid' => $paid,
                    'Paid_Until' => $paidUntil,
                ]);
            }
        } else {
            //If the entry is not found, then attempt to delete whatever existing data is there, then 
            //insert the new data
            MoonRental::where([
                'System' => $system,
                'Planet' => $planet,
                'Moon' => $moon,
            ])->delete();
            
            if($allianceId == 99004116) {
                $store = new MoonRental;
                $store->System = $system;
                $store->Planet = $planet;
                $store->Moon = $mn;
                $store->RentalCorp = $renter;
                $store->RentalEnd = $rentalEnd;
                $store->Contact = $contact;
                $store->Price = $price['alliance'];
                $store->Type = 'alliance';
                $store->Paid = $paid;
                $store->Paid_Until = $paidUntil;
                $store->save();
            } else {
                $store = new MoonRental;
                $store->System = $system;
                $store->Planet = $planet;
                $store->Moon = $mn;
                $store->RentalCorp = $renter;
                $store->RentalEnd = $rentalEnd;
                $store->Contact = $contact;
                $store->Price = $price['outofalliance'];
                $store->Type = 'outofalliance';
                $store->Paid = $paid;
                $store->Paid_Until = $paidUntil;
                $store->save();
            }
        }

        //Redirect to the update moon page
        return redirect('/moons/admin/updatemoon')->with('success', 'Moon Updated');
    }
}
