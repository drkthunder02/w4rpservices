<?php

namespace App\Jobs\Commands\RentalMoons;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//Library
use App\Library\Moons\MoonCalc;

//Models
use App\Models\MoonRentals\AllianceRentalMoon;

/**
 * This job performs the action of updating the moon rental price once per day.
 */
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

        /**
         * Calculate the worth of each moon along with the rental prices.
         * For all of the moon calculations, the price of the mineral is averaged,
         * thereby, the price of the moon worth is also averaged based on the averaging
         * of the mineral price.
         */
        foreach($moons as $rental) {
            //Calculate the total worth of the moon
            $totalWorth = $moonHelper->SpatialMoonsTotalWorth(
                $rental->first_ore, $rental->first_quantity,
                $rental->second_ore, $rental->second_quantity,
                $rental->third_ore, $rental->third_quantity,
                $rental->fourth_ore, $rental->fourth_quantity,
            );

            //Calculate the rental prices of the moon
            $rentalPrice = $moonHelper->SpatialMoons(
                $rental->first_ore, $rental->first_quantity,
                $rental->second_ore, $rental->second_quantity,
                $rental->third_ore, $rental->third_quantity,
                $rental->fourth_ore, $rental->fourth_quantity,
            );

            //Update the moon in the database
            AllianceRentalMoon::where([
                'region' => $rental->region,
                'system' => $rental->system,
                'planet' => $rental->planet,
                'moon' => $rental->moon,
            ])->update([
                'moon_worth' => $totalWorth,
                'alliance_rental_price' => $rentalPrice['alliance'],
                'out_of_alliance_rental_price' => $rentalPrice['outofalliance'],
            ]);

            Log::info('Alliance Rental Moon: ' . $rental->region . ' : ' . $rental->system . ' : ' . $rental->planet . ' : ' . $rental->moon . ' : ' . $rental->structure_name);
            Log::info('Total Worth: ' . $totalWorth);
            Log::info('Alliance Rental Price: ' . $rentalPrice['alliance']);
            Log::info('Out of Alliance Rental Price: ' . $rentalPrice['outofalliance']);
        }
    }
}
