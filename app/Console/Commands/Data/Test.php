<?php

namespace App\Console\Commands\Data;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Library\Helpers\LookupHelper;
use App\Library\Esi\Esi;
use App\Library\Moons\MoonCalc;
use App\Models\MoonRental\AllianceMoon;
use App\Models\MoonRental\AllianceMoonOre;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test ESI stuff.';

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
        //Declare variables
        $lookup = new LookupHelper;
        $mHelper = new MoonCalc;
        $months = 3;
        $rentalTax = 0.25;
        $worth1;
        $worth2;

        $moons = AllianceMoon::all();

        foreach($moons as $moon) {
            //Declare the arrays needed
            $ores = array();

            $ores = AllianceMoonOre::where([
                'moon_id' => $moon->moon_id,
            ])->get(['ore_id', 'quantity'])->toArray();

        }
    }
}
