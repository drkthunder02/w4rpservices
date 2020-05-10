<?php

namespace App\Library\SRP;

//Internal Libraries
use Carbon\Carbon;

//Models
use App\Models\SRP\SrpFleetType;
use App\Models\SRP\SrpPayout;
use App\Models\SRP\SRPShip;
use App\Models\SRP\SrpShipType;

class SRPHelper {

    public function GetAllianceSRPActual($start, $end) {
        $actual = 0.00;

        $actual = SRPShip::where([
            'approved' => 'Approved',
        ])->whereBetween('created_at', [$start, $end])
          ->sum('paid_value');

        return $actual;
    }

    public function GetAllianceSRPLoss($start, $end) {
        $loss = 0.00;

        $loss = SRPShip::where([
            'approved' => 'Approved',
        ])->whereBetween('created_at', [$start, $end])
          ->sum('loss_value');

        return $loss;
    }

    public function GetTimeFrame($months) {
        $start = Carbon::now()->startOfMonth();
        $start->hour = 23;
        $start->minute = 59;
        $start->second = 59;
        $end = Carbon::now()->subMonths($months);
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        $date = [
            'start' => $start,
            'end' => $end,
        ];

        return $date;
    }

    /**
     * Returns a set of dates from now until the amount of months has passed
     * 
     * @var integer
     * @returns array
     */
    public function GetTimeFrameInMonths($months) {
        //Declare an array of dates
        $dates = array();
        //Setup the start of the array as the basis of our start and end dates
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        if($months == 1) {
            $dates = [
                'start' => $start,
                'end' => $end,
            ];

            return $dates;
        }

        //Create an array of dates
        for($i = 0; $i < $months; $i++) {
            if($i == 0) {
                $dates[$i]['start'] = $start;
                $dates[$i]['end'] = $end;
            }
            
            $start = Carbon::now()->startOfMonth()->subMonths($i);
            $end = Carbon::now()->endOfMonth()->subMonths($i);
            $end->hour = 23;
            $end->minute = 59;
            $end->second = 59;
            $dates[$i]['start'] = $start;
            $dates[$i]['end'] = $end;
        }

        //Return the dates back to the calling function
        return $dates;
    }
}

?>