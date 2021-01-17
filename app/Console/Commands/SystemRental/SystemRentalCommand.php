<?php

namespace App\Console\Commands\SystemRental;

//Internal Library
use Illuminate\Console\Command;
use Carbon\Carbon;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

//Models
use App\Models\Rentals\RentalSystem;
use App\Models\Mail\SentMail;

class SystemRentalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:SystemRentals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mail out bill for system rentals.';

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
        //Create other variables
        $body = null;
        $delay = 30;

        //Get today's date
        $today = Carbon::now();
        $today->second = 2;
        $today->minute = 0;
        $today->hour = 0;

        //Get the esi configuration
        $config = config('esi');

        //Get all of the contacts for the system rentals
        $contacts = RentalSystem::select('contact_id')->orderBy('contact_id')->get();

        //For each of the contacts send a reminder mail about the total of the systems they are paying for
        foreach($contacts as $contact) {
            //Get all of the systems
            $systems = RentalSystem::where([
                'contact_id' => $contact->contact_id,
            ])->get();

            //Totalize the total cost of all of the systems
            $totalCost = $this->TotalizeCost($systems);

            //Build the body of the mail
            $body = "System Rental Cost is due for the following systems:<br>";
            foreach($systems as $system) {
                $body .= $system->system_name . "<br>";
            }

            //Create the rest of the email body
            $body .= "Total Cost: " . number_format($totalCost, 2, ".", ",");
            $body .= "Please remite payment to White Wolves Holding.<br>";
            $body .= "Sincerely,<br>";
            $body .= "Warped Intentions Leadership<br>";

            //Fill in the subject
            $subject = "Warped Intentions System Rental Bill Due";

            //Dispatch the mail job
            ProcessSendEveMailJob::dispatch($body, (int)$contact->contact_id, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($delay));

            //Increase the delay for the next mail job
            $delay += 60;

            //After the mail is dispatched, save the sent mail record
            $this->SaveSentRecord($config['primary'], $subject, $body, (int)$contact->contact_id, 'character');
        }
    }

    private function TotalizeCost($systems) {
        //Declare the starting total cost
        $totalCost = 0.00;

        foreach($systems as $system) {
            $totalCost += $system->rental_cost;
        }

        return $totalCost;
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
}
