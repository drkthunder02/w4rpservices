<?php

namespace App\Jobs\Commands\RentalMoons;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Log;

//Models
use App\Models\MoonRentals\AllianceRentalMoon;

class UpdateMoonRentalPaidState implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 1600;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set the queue connection up
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get all of the moons from the rental database
        $moons = AllianceRentalMoons::all();

        /**
         * For each of the moons check the rental until date, the paid until date,
         * and compare them to today's current date.
         * 
         * If the paid date is later than the rental until date, then update the rental until
         * date to match the paid date.  If the paid until date is today or less, then update the
         * paid column of the moon to not paid.  If the moon hasn't been paid 2 weeks after the first
         * of the month, then remove the renter, and send an eve mail to alliance leadership, and the renter
         * denoting failure of payment has resulted in the moon rental for the current month being
         * revoked.
         */
        foreach($moon as $rental) {
            
        }
    }
}
