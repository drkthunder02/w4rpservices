<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Library
use Commands\Library\CommandHelper;
use App\Library\Moons\MoonCalc;
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\Moon\Moon;
use App\Models\MoonRent\MoonRental;
use App\Models\Jobs\JobSendEveMail;
use App\Models\Mail\SentMail;
use App\Models\Mail\EveMail;

class MoonMailerCommand extends Command
{
    /**
     * Next update will include checking for if the moon has been paid in advance.
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:MoonMailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mail out the moon rental bills automatically';

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
     * @return mixed
     */
    public function handle()
    {
        //Create the new command helper container
        $task = new CommandHelper('MoonMailer');
        //Add the entry into the jobs table saying the job has started
        $task->SetStartStatus();

        //Create other variables
        $body = null;
        $delay = 5;

        //Get today's date.
        $today = Carbon::now();
        $today->second = 2;
        $today->minute = 0;
        $today->hour = 0;

        //Get the esi configuration
        $config = config('esi');

        //Get all contacts from the rentals group
        $contacts = MoonRental::select('Contact')->groupBy('Contact')->get();
        
        //For each of the contacts totalize the moon rental, and create the mail to send to them,
        //then update parameters of the moon
        foreach($contacts as $contact) {
            //Get the moons the renter is renting.  Also only get the moons which are not paid as of today
            $rentals = MoonRental::where(['Contact' => $contact->Contact])->get();
                        
            //Totalize the cost of the moons
            $cost = $this->TotalizeMoonCost($rentals);
            
            //Get the list of moons in a list format
            $listItems = $this->GetMoonList($rentals);

            //Build the mail body
            $body = "Moon Rent is due for the following moons:<br>";
            foreach($listItems as $item) {
                $body .= $item . "<br>";
            }
            $body .= "The price for the next month's rent is " . number_format($cost, 2, ".", ",") . "<br>";
            $body .= "Please remit payment to Spatial Forces on the 1st should you continue to wish to rent the moon.<br>";
            $body .= "Sincerely,<br>";
            $body .= "Warped Intentions Leadership<br>";
            
            //Dispatch the mail job
            $mail = new JobSendEveMail;
            $mail->sender = $config['primary'];
            $mail->subject = "Warped Intentions Moon Rental Payment Due for " . $today->englishMonth;
            $mail->body = $body;
            $mail->recipient = (int)$contact->Contact;
            $mail->recipient_type = 'character';
            ProcessSendEveMailJob::dispatch($mail)->onQueue('mail')->delay($delay);
            //Increment the delay for the mail to not hit rate limits
            $delay += 30;

            $this->SendMail($mail);

            //Update the moon as not being paid for the next month?
            foreach($rentals as $rental) {
                $previous = new Carbon($rental->Paid_Until);

                if($today->greaterThan($previous)) {
                    $this->UpdateNotPaid($rental);
                }
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }

    private function SendMail() {
        return;
    }

    private function UpdateNotPaid(MoonRental $rental) {
        MoonRental::where([
            'System' => $rental->System,
            'Planet'=> $rental->Planet,
            'Moon'=> $rental->Moon,
        ])->update([
            'Paid' => 'No',
        ]);
    }

    private function GetMoonList($moons) {
        //Declare the variable to be used as a global part of the function
        $list = array();

        //For each of the moons, build the System Planet and Moon.
        foreach($moons as $moon) {
            $temp = 'Moon: ' . $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon;
            array_push($list, $temp);
        }

        //Return the list
        return $list;
    }

    private function GetRentalMoons($contact) {
        $rentals = MoonRental::where([
            'Contact' => $contact,
        ])->get();
        
        return $rentals;
    }

    private function TotalizeMoonCost($rentals) {
        //Delcare variables and classes
        $moonCalc = new MoonCalc;
        $totalCost = 0.00;
        $price = null;

        foreach($rentals as $rental) {
            $moon = Moon::where([
                'System' => $rental->System,
                'Planet' => $rental->Planet,
                'Moon' => $rental->Moon,
            ])->first();

            //Get the updated price for the moon
            $price = $moonCalc->SpatialMoonsOnlyGooMailer($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                          $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
            
            //Check the type and figure out which price to add in
            if($rental->Type == 'alliance') {
                $totalCost += $price['alliance'];
            } else{
                $totalCost += $price['outofalliance'];
            }
        }

        //Return the total cost back to the calling function
        return $totalCost;
    }

    private function GetRentalType($rentals) {
        $alliance = 0;
        $outofalliance = 0;
  
        //Go through the data and log whether the renter is in the alliance,
        //or the renter is out of the alliance
        foreach($rentals as $rental) {
            if($rental->Type == 'alliance') {
                $alliance++;
            } else {
                $outofalliance++;
            }
        }

        //Return the rental type
        if($alliance > $outofalliance) {
            return 'alliance';
        } else {
            return 'outofalliance';
        }
    }
}
