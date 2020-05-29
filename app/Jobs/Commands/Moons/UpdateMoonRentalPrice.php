<?php

namespace App\Jobs\Commands\Moons;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
use Carbon\Carbon;
use Log;

//Library
use App\Library\Moons\MoonCalc;

//Models
use App\Models\MoonRentals\AllianceRentalMoon;

class UpdateMoonRentalPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3200;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the helper for moon calculations
        $moonHelper = new MoonCalc;
        //Get all the moons from the rental database
        $moons = AllianceRentalMoon::all();
        //Get the configuration from the database for the pricing calculate of rental moons
        $config = DB::table('Config')->get();

        /**
         * Calculate the worth of each moon along with the rental prices.
         */
        foreach($moons as $rental) {
            
        }
    }
}
