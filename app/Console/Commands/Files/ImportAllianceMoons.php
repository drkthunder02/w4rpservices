<?php

//Namespace
namespace App\Console\Commands\Files;

//Internal Library
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use DB;

//Application Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MoonRental\AllianceMoonOre;
use App\Models\MoonRental\AllianceMoon;

class ImportAllianceMoons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:import:moons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import moons from tab-delimited text.';

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
        ///universe/moons/{moon_id}/
        //Declare variables
        $lookup = new LookupHelper;
        //Create the collection of lines for the input file.
        $moons = new Collection;

        AllianceMoon::truncate();
        AllianceMoonOre::truncate();

        //Create the file handler
        $data = Storage::get('public/alliance_moons.txt');
        //Split the string into separate arrays based on the line
        $lines = preg_split("/\n/", $data);
        //Take each line and split it again by tabs
        foreach($lines as $temp) {
            //Split the lines into separate arrays by tabs
            $separated = preg_split("/\t/", $temp);
            //Push the tabbed array into the collection
            $moons->push($separated);            
        }

        /**
         * The first pass through the collection of data is to get all of the ore data
         * and store it in the database.  From the database moon ore, we will create a list
         * of moons and store those in the database.  After the list of moons are created in the
         * database, the function will then update the value of all the moons.
         */

        //Start working our way through all of the moons
        //and saving the data to the database
        foreach($moons as $moon) {
            //If the first array is null then we are dealing with an ore
            if($moon[0] == null) {
                $moonInfo = $lookup->GetMoonInfo($moon[6]);
                $solarName = $lookup->SystemIdToName($moonInfo->system_id);

                if(AllianceMoon::where(['moon_id' => $moonInfo->moon_id])->count() == 0) {
                    //Save the moon into the database
                    $newMoon = new AllianceMoon;
                    $newMoon->moon_id = $moonInfo->moon_id;
                    $newMoon->name = $moonInfo->name;
                    $newMoon->system_id = $moonInfo->system_id;
                    $newMoon->system_name = $solarName;
                    $newMoon->worth_amount = 0.00;
                    $newMoon->rented = 'No';
                    $newMoon->rental_amount = 0.00;
                    $newMoon->save();
                }

                //Save a new entry into the database
                $ore = new AllianceMoonOre;
                $ore->moon_id = $moon[6];
                $ore->moon_name = $moonInfo->name;
                $ore->ore_type_id = $moon[3];
                $ore->ore_name = $moon[1];
                $ore->quantity = $moon[2];
                $ore->solar_system_id = $moon[4];
                $ore->planet_id = $moon[5];
                $ore->save();
            }
        }
    }
}
