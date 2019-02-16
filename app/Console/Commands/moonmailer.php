<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\SendEveMail;
use Carbon\Carbon;

use Commands\Library\CommandHelper;
use App\Library\Moons\MoonMailer;
use App\Library\Moons\MoonCalc;
use DB;

use App\Models\Moon\Moon;
use App\Models\Moon\MoonRent;
use App\Models\Mail\EveMail;

class MoonMailerCommand extends Command
{
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
        $moonCalc = new MoonCalc();
        //Create other variables
        $price = null;
        $body = null;

        //Get all of the moons from the rental list
        $rentals = MoonRent::all();

        //Update the price for all moon rentals before sending out the mail
        foreach($rentals as $rental) {
            //Get the updated price for the moon
            $price = $moonCalc->SpatialMoonsOnlyGoo($rental->FirstOre, $rental->FirstQuantity, $rental->SecondOre, $rental->SecondQuantity, 
                                                    $rental->ThirdOre, $rental->ThirdQuantity, $rental->FourthOre, $rental->FourthQuantity);
            
            //Create the mail body depending on if the price should be in alliance or out  of alliance
            if($rental->Type == 'alliance') {
                $body = "Moon Rent is due for " . $rental->System . " Planet: " . $rental->Planet . " Moon: " . $rental->Moon . "<br>";
                $body .= "The price for next month's rent is " . $price['alliance'] . "<br>";
                $body .= "Please remite payment to Spatial Forces by the 1st should you continue to wish to rent the moon.<br>";
                $body .= "Sincerely,<br>";
                $body .= "Spatial Forces<br>";
            } else {
                $body = "Moon Rent is due for " . $rental->System . " Planet: " . $rental->Planet . " Moon: " . $rental->Moon . "<br>";
                $body .= "The price for next month's rent is " . $price['outofalliance'] . "<br>";
                $body .= "Please remite payment to Spatial Forces by the 1st should you continue to wish to rent the moon.<br>";
                $body .= "Sincerely,<br>";
                $body .= "Spatial Forces<br>";
            }    

            //Send a mail to the contact listing for the moon
            $mail = new EveMail;
            $mail->sender = 93738489;
            $mail->subject = "Moon Rental";
            $mail->body = $body;
            $mail->recipient = (int)$rental->Contact;
            $mail->recipient_type = 'character';
            $mail->save();

            //Dispatch the job and cycle to the next moon rental
            SendEveMail::dispatch($mail)->delay(Carbon::now()->addseconds(15)); 
        }

        //Remove all moon rentals from the database
        DB::table('moon_rents')->delete();

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
