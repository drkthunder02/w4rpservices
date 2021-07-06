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
        $this->connection = 'redis';
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
            $worth = 0.00;

            $ores = AllianceMoonOre::where([
                'moon_id' => $moon->moon_id,
            ])->get(['ore_name', 'quantity'])->toArray();

            if(sizeof($ores) == 1) {
                $ores[1]["ore_name"] = null;
                $ores[1]["quantity"] = 0.00;
                $ores[2]["ore_name"] = null;
                $ores[2]["quantity"] = 0.00;
                $ores[3]["ore_name"] = null;
                $ores[3]["quantity"] = 0.00;
            } else if(sizeof($ores) == 2) {
                $ores[2]["ore_name"] = null;
                $ores[2]["quantity"] = 0.00;
                $ores[3]["ore_name"] = null;
                $ores[3]["quantity"] = 0.00;
            } else if(sizeof($ores) == 3) {
                $ores[3]["ore_name"] = null;
                $ores[3]["quantity"] = 0.00;
            }

            //one of these two ways will work
            $worth = $mHelper->MoonTotalWorth($ores[0]["ore_name"], $ores[0]["quantity"], 
                                              $ores[1]["ore_name"], $ores[1]["quantity"], 
                                              $ores[2]["ore_name"], $ores[2]["quantity"], 
                                              $ores[3]["ore_name"], $ores[3]["quantity"]);
            
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
