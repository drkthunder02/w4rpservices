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
            //Declare variables for each instance of the foreach loop
            $moonId = null;
            $moonName = null;
            $oreId = null;
            $oreName = null;
            $oreQuantity = null;
            $systemId = null;
            $systemname = null;

            if($moon[0] != null) {
                var_dump($moon[0]);
            } else {
                
            }

        }

        dd();
    }
}
