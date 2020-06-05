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
use App\Models\Moon\AllianceMoon;
use App\Models\MoonRentals\AllianceRentalMoon;
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
    public function updateMoon() {
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
    public function storeMoonRemoval(Request $request) {
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
        AllianceRentalMoon::where([
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

        //Once the action is completed, redirect to the original page
        return redirect('/moons/admin/display/rentals')->with('success', 'Renter removed from the moon.');
    }

    /**
     * Function to display the moons to admins
     * New function based on new table.  Will
     * update description in a future update.
     */
    public function displayRentalMoonsAdmin() {
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
        $rentalMoons = AllianceRentalMoon::orderBy('system', 'asc')->get();

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
     * Function to store the updates from the moons.
     * New function based on new table.  Will update
     * the description in a future update
     */
    public function storeUpdateMoon(Request $request) {
        //Require the site administration role
        $this->middleware('role:Admin');

        //Declare some variables we will need
        $moonCalc = new MoonCalc;
        $lookup = new LookupHelper;
        $paid = false;
        $system = null;
        $planet = null;
        $mn = null;

        //Validate our request from the html form
        $this->validate($request, [
            'spmn' => 'required',
            'contact' => 'required',
            'contact_type' => 'required',
            'paid_until' => 'required',
            'rental_end' => 'required',
        ]);

        //Decode the spmn
        $str_array = explode(" - ", $request->spmn);
        $system = $str_array[0];
        $planet = $str_array[1];
        $mn = $str_array[2];
        $name = $str_array[3];

        //Update the paid value from the request value
        if($request->paid == 'Yes') {
            $paid = 'Yes';
        } else {
            $paid = 'No';
        }

        //Setup the rental end and paid until variables
        $rentalEnd = $request->rental_end . " 23:59:59";
        $paidUntil = $request->paid_until . " 23:59:59";

        //Check if the alliance is renting the moon for itself
        if($request->contact_type == 'Corporation' && $request->contact == 'Spatial Forces') {
            AllianceRentalMoon::where([
                'system' => $str_array[0],
                'planet' => $str_array[1],
                'moon' => $str_array[2],
            ])->update([
                'rental_type' => 'Alliance',
                'rental_until' => $request->rental_end . " 23:59:59",
                'rental_contact_id' => 98287666,
                'rental_contact_type' => 'Alliance',
                'paid' => 'No',
                'paid_until' => null,
                'alliance_use_until' => $request->rental_end . " 23:59:59",
            ]);
        } else if($request->contact_type == 'Character') {
            //Get the character id from the lookup helper
            $charId = $lookup->CharacterNameToId($request->contact);
            //Get the character information including the corporation from the lookup tables
            $char = $lookup->GetCharacterInfo($charId);
            //Get the corporation id from the lookup helper, followed by the alliance id
            //so we can determine if it's in alliance or out of alliance
            $corp = $lookup->GetCorporationInfo($char->corporation_id);

            if($corp->alliance_id == 99004116) {
                $type = 'In Alliance';
            } else {
                $type = 'Out of Alliance';
            }

            AllianceRentalMoon::where([
                'system' => $str_array[0],
                'planet' => $str_array[1],
                'moon' => $str_array[2],
            ])->update([
                'rental_type' => $type,
                'rental_until' => $request->rental_end . " 23:59:59",
                'rental_contact_id' => $charId,
                'rental_contact_type' => 'Character',
                'paid' => $paid,
                'paid_until' => $request->paid_until . " 23:59:59",
                'alliance_use_until' => null,                
            ]);

        } else if($request->contact_type == 'Corporation') {
            //Get the corporation id from the lookup helper
            $corpId = $lookup->CorporationNameToId($request->contact);
            //Get the corporation information to determine if they are in Warped Intentions or not.
            $corporation = $lookup->GetCorporationInfo($request->contact);

            if($corp->alliance_id == 99004116) {
                $type = 'In Alliance';
            } else {
                $type = 'Out of Alliance';
            }

            AllianceMoonRental::where([
                'system' => $str_array[0],
                'planet' => $str_array[1],
                'moon' => $str_array[2],
            ])->update([
                'rental_type' => $type,
                'rental_until' => $request->rental_end . " 23:59:59",
                'rental_contact_id' => $corpId,
                'rental_contact_type' => 'Corporation',
                'paid' => $paid,
                'paid_until' => $request->paid_until . " 23:59:59",
                'alliance_use_until' => null,
            ]);
        }
                
        //Redirect to the previous screen.
        return redirect('/moons/admin/updatemoon')->with('success', 'Moon Rental updated.');
    }
}
