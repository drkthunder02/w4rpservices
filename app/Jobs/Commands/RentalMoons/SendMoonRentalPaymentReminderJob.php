<?php

namespace App\Jobs\Commands\RentalMoons;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Carbon\Carbon;

//Library
use App\Library\Lookups\LookupHelper;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

//Models
use App\Models\MoonRentals\AllianceRentalMoon;
use App\Models\Mail\SentMail;


/**
 * This job will send out a reminder about the moon rental payment being due
 * when it's due based on the rental paid date versus rental date.  If the paid
 * date is in the future from today, then we don't need to send out a reminder.
 * If the paid date is today or less, then we need to send out a reminder about
 * paying for the moon rental.
 */
class SendMoonRentalPaymentReminderJob implements ShouldQueue
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
        //Set the queue connection
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get today's date
        $today = Carbon::now();
        $today->second = 1;
        $today->minute = 0;
        $today->hour = 0;
        //Declare some other variables
        $totalCost = 0.00;
        $moonList = array();
        $delay = 30;
        $lookup = new LookupHelper;
        $config = config('esi');

        //Get all of the contacts from the rentals group
        $contacts = AllianceMoonRental::select('rental_contact_id')->groupBy('rental_contact_id')->get();

        //For each of the contacts, totalize the moon rentals, and create a reminder mail
        foreach($contacts as $contact) {
            //Get the moons the renter is renting, but only get the ones whose paid date is not after today.
            $dues = $this->GetMoonDueList($contact->rental_contact_id);

            //Get the list of moons for the mail body.
            $alls = $this->GetMoonRentalList($contact->rental_contact_id);

            //Totalize the cost for the moons whose rent is due
            $cost = $this->TotalizeMoonCost($contact->rental_contact_id);

            //For each of the rentals, build the mail body, and totalize the cost of the moon
            $body = "Moon Rent is due for the following moons:<br>";
            foreach($rentalsDue as $due) {
                $body .= $due . "<br>";
            }
            //Put the price for the moons 
            $body .= "The price for next month's rent is " . number_format($cost, 0, ".", ",") . "<br>";
            $body .= "Rental Payment is due on the 1st of the month.  If the rental payment is not remitted to Spatial Forces by the 3rd of the month, the rental claim shall be forfeited.<br>";
            $body .= "Rental Payment should be transferred to Spatial Forces.<br>";
            $body .= "In the description of the payment please put the following transaction identification: " . $transId . "<br>";
            $body .= "<br>";
            $body .= $contact->contact_name . " is responsible for payment of the moons.<br>";
            $body .= "The following moons are being rented:<br>";
            foreach($alls as $all) {
                $body .= $all . "<br>";
            }
            $body .= "<br>";
            $body .= "Sincerely,<br>";
            $body .= "Warped Intentions Leadership<br>";

            //Create the subject line
            $subject = "Warped Intentions Moon Rental Payment Due for " . $today->englishMonth;
            //Dispatch the mail job if the contact type is a character, otherwise
            //dispatch the job to the ceo of the corporation instead.  If the contact is an alliance
            //dispatch the job to the ceo of the holding corporation.
            if($contact->contact_type == 'Character') {
                ProcessSendEveMailJob::dispatch($body, (int)$contact->rental_contact_id, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($delay));
                //Increment the delay to get ready for the next mail job
                $delay += 30;
            } else if($contact->contact_type == 'Corporation') {
                //Get the CEO of the corporation from the lookup helper
                $corporation = $lookup->GetCorporationInfo($contact->rental_contact_id);
                $charId = $corporation->ceo_id;
                //Send out the mail
                ProcessSendEveMailJob::dispatch($body, (int)$charId, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($delay));
                //Increment the delay to get ready for the next mail job
                $delay += 30;
            } else if($contact->contact_type == 'Alliance') {
                //Get the holding corporation from the lookup helper
                $alliance = $lookup->GetAllianceInfo($contact->rental_contact_id);
                //Get the CEO of the corporation of the holding corp from the lookup helper
                $corporation = $lookup->GetCorporationInfo($alliance->executor_corporation_id);
                $charId = $corporation->ceo_id;
                //Send out the mail
                ProcessSendEveMailJob::dispatch($body, (int)$charId, 'character', $subject, $config['primaryh'])->onQueue('mail')->delay(Carbon::now()->addSeconds($delay));
                //Increment the detaly to get ready for the next mail job
                $delay += 30;
            }
        }
    }

    private function GetMoonRentalsList($contact) {
        //Declare the variables
        $list = array();

        $moons = AllianceMoonRental::where([
            'rental_contact_id' => $contact,
        ])->get();

        foreach($moons as $moon) {
            $temp = 'Rental: ' . $moon->region . ' - ' . $moon->system . ' - ' . $moon->planet . ' - ' . $moon->moon;
            array_push($list, $temp);
        }

        //Return the list
        return $list;
    }

    private function GetMoonDueList($contact) {
        //Declare the variables
        $list = array();

        $moons = AllianceMoonRental::where([
            'rental_contact_id' => $contact,
        ])->where('paid_until', '<=', $today)->get();

        //Create the list
        foreach($moons as $moon) {
            $temp = 'Rental: ' . $moon->region . ' - ' . $moon->system . ' - ' . $moon->planet . ' - ' . $moon->moon;
            array_push($list, $temp);
        }

        //Return the list
        return $list;
    }

    private function TotalizeCost($rentals, $rentalType) {
        //Declare the stuff we need
        $totalCost = 0.00;

        //Totalize the cost
        foreach($rentals as $rental) {
            if($rentalType = 'In Alliance') {
                $totalCost += $rental->alliance_rental_price;
            } else {
                $totalCost += $rental->out_of_alliance_rental_price;
            }
        }

        return $totalCost;
    }
}
