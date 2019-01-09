<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function displayFinances() {
        $months = 1;
        $income = 0.00;
        $expenses = 0.00;
        $corpId = 98287666;
        $finances = array();
        $total = array();

        $sHelper = new StructureTaxHelper();

        $date = $sHelper->GetTimeFrameInMonths($months);

        
        $revenue = [
            'date' => $date['start']->toFormattedDateString(),
            'industry' => StructureIndustryTaxJournal::select('amount')->whereBetween('date', [$date['start'], $date['end']])->sum('amount'),
            'reprocessing' => $sHelper->GetRevenue($corpId, 'Refinery', $date['start'], $date['end']),
            'offices' => OfficeFeesJournal::select('amount')->whereBetween('date', [$date['start'], $date['end']])->sum('amount'),
            'market' => $sHelper->GetRevenue($corpId, 'Market', $date['start'], $date['end']),
            'rentals' => 9500000000000,
            'pi' => PlanetProductionTaxJournal::select('amount')->whereBetween('date', [$date['start'], $date['end']])->sum('amount'),
        ];

        $expenditures = [
            'fuel' => 9187200000,
            'sov' => 8666870000,
            'srp' => 1000000000,
        ];

        $income = $revenue['industry'] + $revenue['reprocessing'] + $revenue['offices'] + $revenue['market'] + $revenue['rentals'] + $revenue['pi'];
        $expenses = $expenditures['fuel'] + $expenditures['sov'] + $expenditures['srp'];

        $total[] = [
            'income' => $income,
            'expenses' => $expenses,
        ];
        
        $chart = app()->chartjs
                ->name('Income & Expenditure Chart')
                ->type('pie')
                ->size(['width' => 400, 'height' => 200])
                ->labels(['Income', 'Expenses'])
                ->datasets([
                    [
                        'backgroundColor' => ['#FF6384', '#36A2EB'],
                        'hoverBackgroundColor' => ['#FF6384', '#36A2EB'],
                        'data' => [$total['income'], $total['expenses']]
                    ]
                ])
                ->options([]);


        return view('finances.holding')->with('chart', $chart);
    }
}
