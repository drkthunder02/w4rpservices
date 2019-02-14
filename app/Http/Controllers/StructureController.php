<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Library\Structures\StructureTaxHelper;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Corporation\CorpStructure;
use App\Models\Finances\StructureIndustryTaxJournal;
use App\Models\Esi\EsiToken;

use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

class StructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:structure.operator');
    }

    public function displayReprocessingTaxes() {
        $this->middleware('role:Admin');

        $months = 3;
        $taxes = array();
        $corpId = 98287666;

        //Declare the structure tax helper class
        $sHelper = new StructureTaxHelper();

        //Get the dates we are working with
        $dates = $sHelper->GetTimeFrameInMonths($months);

        foreach($dates as $date) {
            $taxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($sHelper->GetTaxes($corpId, 'Refinery', $date['start'], $date['end']), 2, '.', ','),
                'revenue' => number_format($sHelper->GetRevenue($corpId, 'Refinery', $date['start'], $date['end']), 2, '.', ',')
            ];
        }

        //Return the view with the data passed to it
        return view('structures.user.reprocessingtaxes')->with('taxes', $taxes);
    }

    public function displayIndustryTaxes() {
        $this->middleware('role:Admin');

        $months = 3;
        $taxes = array();

        //Declare the structure tax helper class
        $sHelper = new StructureTaxHelper();

        //Get the dates we are working with
        $dates = $sHelper->GetTimeFrameInMonths($months);

        foreach($dates as $date) {
            $tax = StructureIndustryTaxJournal::select('amount')
                                ->whereBetween('date', [$date['start'], $date['end']])
                                ->sum('amount');
            $taxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($tax, 2, '.', ',')
            ];    
        }

        return view('structures.user.industrytaxes')->with('taxes', $taxes);
    }

    public function chooseCorpTaxes() {
        $this->middleware('role:Admin');

        $corps = CorpStructure::pluck('corporation_name', 'corporation_id');
        return view('structures.admin.choosecorporation')->with('corps', $corps);
    }

    public function displayCorpTaxes(Request $request) {
        $this->middleware('role:Admin');

        $corpId = $request->corpId;
        $months = 3;
        
        //Declare the structure tax helper class
        $sHelper = new StructureTaxHelper();
        
        //Get the dates we are working with
        $dates = $sHelper->GetTimeFrameInMonths($months);

        foreach($dates as $date) {
            $totalTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($sHelper->GetTaxes($corpId, 'Market', $date['start'], $date['end']), 2, '.', ','),
                'revenue' => number_format($sHelper->GetRevenue($corpId, 'Market', $date['start'], $date['end']), 2, '.', ',')
            ];
        }

        //Return the view with the data passed to it
        return view('structures.admin.choosecorptaxes')->with('totalTaxes', $totalTaxes);
    }

    public function displayTaxes() {
        //Declare new Lookup helper
        $helper = new LookupHelper();

        //Declare the structure tax helper class
        $sHelper = new StructureTaxHelper();

        $months = 3;
        $totalTaxes = array();
        
        //Get the character's corporation from the lookup table or esi
        $corpId = $helper->LookupCharacter(Auth::user()->character_id);
        $corpId = $corpId[0]->corporation_id;
        dd($corpId);
        //Get the dates we are working with
        $dates = $sHelper->GetTimeFrameInMonths($months);
        
        //Get the market taxes for this month from the database
        foreach($dates as $date) {
            $totalTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($sHelper->GetTaxes($corpId, 'Market', $date['start'], $date['end']), 2, '.', ','),
                'revenue' => number_format($sHelper->GetRevenue($corpId, 'Market', $date['start'], $date['end']), 2, '.', ',')
            ];
        }

        return view('structures.user.taxes')->with('totalTaxes', $totalTaxes);
    }

    public function displayTaxHistory(Request $request) {
        //Declare new Lookup helper
        $helper = new LookupHelper();

        //Get the months from the request
        $months = $request->months;

        //Get the character's corporation from the lookup table or esi
        $corpId = $helper->LookupCharacter(Auth::user()->character_id);

        //Declare the structure tax helper class
        $sHelper = new StructureTaxHelper();

        //Get the dates we are working with
        $dates = $sHelper->GetTimeFrameInMonths($months);

        //Build the array for displaying the data on the view
        $totalTaxes = array();
        
        foreach($dates as $date) {
            $totalTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($sHelper->GetTaxes($corpId, 'Market', $date['start'], $date['end']), 2, '.', ','),
                'revenue' => number_format($sHelper->GetRevenue($corpId, 'Market', $date['start'], $date['end']), 2, '.', ',')
            ];
        }

        return view('structures.user.taxhistory')->with(compact('totalTaxes', 'months'));
    }
}
