<?php

namespace App\Jobs\Commands\MoonRental;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//Internal Library
use App\Library\Moons\MoonCalc;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MoonRental\AllianceMoon;
use App\Models\MoonRental\AllianceMoonOre;

class UpdateAllianceMoonRentalWorth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection('redis');
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $lookup = new LookupHelper;
        $mHelper = new MoonCalc;
        $months = 3;
        $rentalTax = 0.25;

        $moons = AllianceMoon::all();

        foreach($moons as $moon) {
            //Declare the arrays needed
            $ores = array();

            $ores = AllianceMoonOre::where([
                'moon_id' => $moon->moon_id,
            ])->get(['ore_id', 'quantity'])->toArray();

            //one of these two ways will work
            $worth = $mHelper->MoonTotalWorth($ores[0][0], $ores[0][1], $ores[1][0], $ores[1][1], $ores[2][0], $ores[2][1], $ores[3][0], $ores[3[1]]);
            dd($worth);
            $worth = $mHelper->MoonTotalWorth($ores[0], $ores[1], $ores[2], $ores[3], $ores[4], $ores[5], $ores[6], $ores[7]);
            $rentalAmount = $worth * $rentalTax * $months;

            AllianceMoon::where([
                'moon_id' => $moon->moon_id,
            ])->update([
                'worth_amount' => $worth,
                'rental_amount' => $rentalAmount,
            ]);
        }
    }
}
