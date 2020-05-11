<?php

namespace App\Jobs\Commands\Moons;

//Internal Libraries
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Library
use App\Library\Moons\MoonCalc;
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\Moon\RentalMoon;
use App\Models\MoonRent\MoonRental;
use App\Models\Mail\SentMail;

class MoonRentalInvoiceCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Today's date
     * 
     * @var Carbon
     */
    private $today;

    /**
     * Rental Contact
     * 
     * @var int
     */
    private $contact;

    /**
     * Moon Rentals
     * 
     * @var MoonRental
     */
    private $rentals;

    /**
     * ESI mail delay to not hit limit of 4 / min
     * 
     * @var int
     */
    private $delay;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contact, $mailDelay)
    {
        //Renter contact
        $this->contact = $contact;

        //Setup today's date
        $this->today = Carbon::now();
        $this->today->second = 1;
        $this->today->minute = 0;
        $this->today->hour = 0;

        //Setup the delay
        $this->delay = $mailDelay;

        //Null out unused variables when calling the construct
        $this->rentals = null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Create needed variables
        $moonCalc = new MoonCalc;
        $body = null;
        $delay = 60;
        $config = config('esi');

        //Get the rentals the contact is renting
        $this->rentals = MoonRental::where([
            'Contact' => $this->contact,
        ])->get();

        //Totalize the cost of the moons
        $cost = $this->TotalizeMoonCost();

        //Get the list of the moons in a list format
        $listItems = $this->GetMoonList();

        //Build a mail body to be sent to the renter
        $body = "Moon Rent is due for the following moons:<br>";
        foreach($listItems as $item) {
            $body .= $item . "<br>";
        }
        $body .= "The price for the next's month rent is " . number_format($cost, 2, ".", ",") . " ISK<br>";
        $body .= "Please remit payment to Spatial Forces on the 1st should you continue to wish to rent the moon.<br>";
        $body .= "Sincerely,<br>";
        $body .= "Warped Intentions Leadership<br>";

        //Create the moon invoice and save it to the database
        

        //Dispatch a new mail job
        $subject = "Warped Intentions Moon Rental Payment Due for " . $today->englishMonth;
        ProcessSendEveMailJob::dispatch($body, (int)$contact->Contact, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($this->delay));

        MoonRentalUpdate::dispatch($this->rentals)->onQueue('moons');
    }

    private function TotalizeMoonCost() {
        $totalCost = 0.00;
        $price = null;

        foreach($this->rentals as $rental) {
            $moon = RentalMoon::where([
                'System' => $rental->System,
                'Planet' => $rental->Planet,
                'Moon' => $rental->Moon,
            ])->first();

            $end = new Carbon($rental->Paid_Until);

            //If today is greater than the rental end, then calculate the moon cost
            if($today->greaterThanOrEqualTo($end)) {
                //Get the updated price for the moon
                $price = $moonCalc->SpatialMoons($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                 $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

                //Check the type and figure out which price to add in
                if($rental->Type == 'alliance') {
                    $totalCost += $price['alliance'];
                } else {
                    $totalCost += $price['outofalliance'];
                }
            }
        }

        //Return the total cost to the calling function
        return $totalCost;
    }

    private function GetMoonList() {
        //Declare the list variable as an array
        $list = array();

        //Foreach of the moons, build the system, planet, and moon
        foreach($this->rentals as $moon) {
            $temp = 'Moon: ' . $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon;
            array_push($list, $temp);
        }

        //Return the list
        return $list;
    }
}
