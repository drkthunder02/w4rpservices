<?php

namespace App\Console\Commands\MoonRental;

//Application Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Internal Library
use App\Library\Moons\MoonCalc;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MoonRental\AllianceMoon;
use App\Models\MoonRental\AllianceMoonOre;

//Jobs
use App\Jobs\Commands\MoonRental\UpdateAllianceMoonRentalWorth;

class ExecuteUpdateAllianceMoonRentalWorth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mr:worth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update alliance moon rental worth.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //UpdateAllianceMoonRentalWorth::dispatch();

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
            ])->get(['ore_name', 'quantity'])->toArray();

            //one of these two ways will work
            $worth = $mHelper->MoonTotalWorth($ores[0][0], $ores[0][1], $ores[1][0], $ores[1][1], $ores[2][0], $ores[2][1], $ores[3][0], $ores[3][1]);
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

        return 0;
    }
}
