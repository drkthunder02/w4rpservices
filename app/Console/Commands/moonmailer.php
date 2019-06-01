<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

//Jobs
use App\Jobs\SendEveMailJob;

//Library
use Commands\Library\CommandHelper;
use App\Library\Moons\MoonCalc;

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

        //Get today's date.
        $today = Carbon::now();
        $today->second = 1;
        $today->minute = 0;
        $today->hour = 0;

        //Get all contacts from the rentals group
        $contacts = MoonRental::select('Contact')->groupBy('Contact')->get();

        //For each of the contacts totalize the moon rental, and create the mail to send to them,
        //then update parameters of the moon
        foreach($contacts as $contact) {
            //Get the moons the renter is renting
            $rentals = $this->GetRentalMoons($contact);
            
            //Totalize the cost of the moons
            $cost = $this->TotalizeMoonCost($rentals);

            //Get the list of moons in a list format
            $listItems = $this->GetMoonList($rentals);

            //Build the mail body
            $body = "Moon Rent is due for the following moons:<br>";
            foreach($listItems as $item) {
                $body .= $item . "<br>";
            }
            $body .= "The price for the next month's rent is " . $cost . "<br>";
            $body .= "Please remit payment to Spatial Forces on the 1st should you continue to wish to rent the moon.<br>";
            $body .= "Sincerely,<br>";
            $body .= "Warped Intentions Leadership<br>";

            //Dispatch the mail job
            $mail = new EveMail;
            $mail->sender = 93738489;
            $mail->subject = "Warped Intentions Moon Rental Payment Due";
            $mail->body = $body;
            $mail->recipient = (int)$contact->Contact;
            $mail->recipient_type = 'character';
            SendEveMailJob::dispatch($mail)->onQueue('mail');

            //After the mail is dispatched, saved the sent mail record
            $this->SaveSentRecord($mail->sender, $mail->subject, $mail->body, $mail->recipient, $mail->recipient_type);

            //Update the moon as not being paid for the next month?
            foreach($rentals as $rental) {
                if($today > $rental->Paid_Until)
                $this->UpdateNotPaid($rental);
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }

    private function UpdateNotPaid(MoonRental $rental) {
        $today = Carbon::now();

        if($today >= $rental->Paid_Until) {
            MoonRental::where([
                'System' => $rental->System,
                'Planet'=> $rental->Planet,
                'Moon'=> $rental->Moon,
            ])->update([
                'Paid' => 'No',
            ]);
        }
    }

    private function SaveSentRecord($sender, $subject, $body, $recipient, $recipientType) {
        $sentmail = new SentMail;
        $sentmail->sender = $sender;
        $sentmail->subject = $subject;
        $sentmail->body = $body;
        $sentmail->recipient = $recipient;
        $sentmail->recipient_type = $recipientType;
        $sentmail->save();
    }

    private function GetMoonList($moons) {
        //Declare the variable to be used as a global part of the function
        $list = array();

        //For each of the moons, build the System Planet and Moon.
        foreach($moons as $moon) {
            $temp = 'System: ' . $moon->System;
            $temp .= 'Planet: ' . $moon->Planet;
            $temp .= 'Moon: ' . $moon->Moon;
            //Push the new string onto the array list
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

        foreach($rentals as $rental) {
            $moon = Moon::where([
                'System' => $rental->System,
                'Planet' => $rental->Planet,
                'Moon' => $rental->Moon,
            ])->first();

            //Get the updated price for the moon
            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                    $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
            dd($price);
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
