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

//Library
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\MoonRentals\AllianceRentalMoon;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

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
        //Declare variables
        $mailDelay = 5;

        //Get all of the moons from the rental database
        $moons = AllianceRentalMoons::all();

        //Set today's date
        $today = Carbon::now();

        //Get the esi configuration
        $esiConfig = config('esi');

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
            //Setup the rental date, paid until date, and today's date as functions of Carbon library
            $rentedUntil = new Carbon($rental->rental_until);
            $paidUntil = new Carbon($rental->paid_until);

            //If the paid date is larger than the rental date, then update the rental date
            if($paidUntil->greaterThan($rentedUntil)) {
                AllianceMoonRental::where([
                    'region' => $rental->region,
                    'system' => $rental->system,
                    'planet' => $rental->planet,
                    'moon' => $rental->moon,
                ])->update([
                    'rental_until' => $rental->paid_until,
                ]);
            }

            //If the paid date is today or less, then update the paid column of the moon as not paid
            if($paidUntil->greaterThanOrEqualTo($today)) {
                AllianceMoonRental::where([
                    'region' => $rental->region,
                    'system' => $rental->system,
                    'planet' => $rental->planet,
                    'moon' => $rental->moon,
                ])->update([
                    'paid' => 'No',
                ]);
            }

            //If the moon hasn't been paid for in two weeks, then remove the renter,
            //then send the renter and w4rp leadership a mail.
            if($paidUntil->greaterThanOrEqualTo($today->subWeeks(2))) {
                //Declare the lookup helper as it will be needed
                $lookupHelper = new LookupHelper;

                //Get the character id for Minerva Arbosa and Rock Onzo
                $minerva = $lookupHelper->CharacterNameToId('Minerva Arbosa');
                $rock = $lookupHelper->CharacterNameToId('Rock Onzo');

                //Remove the renter
                AllianceMoonRental::where([
                    'region' => $rental->region,
                    'system' => $rental->system,
                    'planet' => $rental->planet,
                    'moon' => $rental->moon,
                ])->update([
                    'rental_type' => 'Not Rented',
                    'rental_until' => null,
                    'rental_contact_id' => 0,
                    'rental_contact_type' => null,
                    'paid' => 'Not Rented',
                    'paid_until' => null,
                ]);

                //Send a mail over to the alliance leadership, and the former renter with
                //why the moon was removed.
                $subject = "W4RP Moon Rental Cancelled";

                //Dispatch the mail job
                ProcessSendEveMailJob::dispatch($body, (int)$rental->rental_contact_id, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($mailDelay));
                $mailDelay += 30;
                if($minerva != null) {
                    ProcessSendEveMailJob::dispatch($body, (int)$minerva, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($mailDelay));
                    $mailDelay += 30;
                }
                if($rock != null) {
                    ProcessSendEveMailJob::dispatch($body, (int)$rock, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($mailDelay));
                }
            }
        }
    }
}
