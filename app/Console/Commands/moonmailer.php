<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

//Jobs
use App\Jobs\SendEveMailJob;

//Library
use Commands\Library\CommandHelper;
use App\Library\Moons\MoonMailer;
use App\Library\Moons\MoonCalc;

//Models
use App\Models\Moon\Moon;
use App\Models\MoonRent\MoonRent;
use App\Models\Jobs\JobSendEveMail;

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

        //Declare the moon calc class variable to utilize the library to update the price
        $mailer = new MoonMailer;

        //Create other variables
        $body = null;

        //Get today's date.
        $today = Carbon::now();
        $today->second = 1;
        $today->minute = 0;
        $today->hour = 0;

        //Get all contacts from the rentals group
        $contacts = MoonRent::select('Contact')->groupBy('Contact')->get();

        //For each of the contacts totalize the moon rental, and create the mail to send to them,
        //then update parameters of the moon
        foreach($contacts as $contact) {
            //Get the moons the renter is renting
            $rentals = $moonMailer->GetRentalMoons($contact);
            
            //Totalize the cost of the moons
            $cost = $moonMailer->TotalizeMoonCost($rentals);

            //Get the list of moons in a list format
            $listItems = $moonMailer->GetMoonList($rentals);

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
            $mail = new JobSendEveMail;
            $mail->sender = 93738489;
            $mail->subject = "Warped Intentions Moon Rental Payment Due";
            $mail->body = $body;
            $mail->recipient = (int)$contact;
            $mail->recipient_type = 'character';
            SendEveMailJob::dispatch($mail);

            //After the mail is dispatched, saved the sent mail record
            $moonMailer->SaveSentRecord($mail->sender, $mail->subject, $mail->body, $mail->recipient, $mail->recipient_type);

            //Delete the record from the database
            foreach($rentals as $rental) {
                //Delete the moon rental
                $moonMailer->DeleteMoonRental($rental, $today);

                //Mark the moon as not paid for the next month
                $moonMailer->UpdateNotPaid($rental);
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
