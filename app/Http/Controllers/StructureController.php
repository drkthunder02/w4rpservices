<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;
use App\Library\Structures\StructureHelper;

use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;

use App\Library\Esi;

class StructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:structure.operator');
    }

    public function displayTaxes() {
        //Make the helper esi class
        $helper = new Esi();
        

        //Get the character's corporation from esi
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);

        //Get the dates we are working with
        $dates = $sHelper->GetTimeFrame();
        $sHelper = new StructureTaxHelper();

        //Get the market taxes for this month from the database
        $totalTaxes = [
            'thisMonthMarket' => number_format($sHelper->GetTaxes($corpId, 'Market', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'thisMonthRefinery' => number_format($sHelper->GetTaxes($corpId, 'Refinery', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'lastMonthMarket' => number_format($sHelper->GetTaxes($corpId, 'Market', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'lastMonthRefinery' => number_format($sHelper->GetTaxes($corpId, 'Refinery', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'thisMonthRevMarket' => number_format($sHelper->GetRevenue($corpId, 'Market', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'thisMonthRevRefinery' => number_format($sHelper->GetRevenue($corpId, 'Refinery', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'lastMonthRevMarket' => number_format($sHelper->GetRevenue($corpId, 'Market', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'lastMonthRevRefinery' => number_format($sHelper->GetRevenue($corpId, 'Refinery', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'thisMonthStart' => $dates['ThisMonthStart']->toFormattedDateString(),
            'lastMonthStart' => $dates['LastMonthStart']->toFormattedDateString(),
        ];

        return view('structures.taxes')->with('totalTaxes', $totalTaxes);
    }
}
