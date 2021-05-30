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
        //Declare variables
        $lookup = new LookupHelper;
        //Create the collection of lines for the input file.
        $lines = new Collection;

        ///universe/moons/{moon_id}/

        //Create the file handler
        $data = Storage::get('public/alliance_moons.txt');
        //Split the string into separate arrays based on the line
        $tempLines = preg_split("/\n/", $data);

        foreach($tempLines as $temp) {
            $stuff = preg_split("/\t/", $temp);
            var_dump($stuff);
        }

        dd();
    }
}
