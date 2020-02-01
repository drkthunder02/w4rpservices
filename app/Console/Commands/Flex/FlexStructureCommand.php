<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Library
use Commands\Library\CommandHelper;

//Models
use App\Models\Flex\FlexStructure;
use App\Models\Mail\SentMail;
use App\Models\Jobs\JobSendEveMail;

class FlexStructureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:FlexStructures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mail out reminder for flex structure bills';

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
        $task = new CommandHelper('FlexStructureMailer');
        //Add the entry into the jobs table saying the job has started
        $task->SetStartStatus();

        //Create other variables
        $body = null;
        $delay = 5;

        //Get today's date
        $today = Carbon::now();
        $today->second = 2;
        $today->minute = 0;
        $today->hour = 0;

        //Get the esi configuration
        $config = config('esi');

        //Get all of the contacts for the flex structures
        $contacts = FlexStructure::select('requestor_id')->orderBy('requestor_id')->get();

        //For each of the contacts, send a reminder mail about the total of the structures they are paying for
        foreach($contacts as $contact) {
            //Get all of the structures for requestor
            $structures = FlexStructure::where([
                'requestor_id' => $contact->requestor_id,
            ])->get();

            //Totalize the total cost of everything
            $totalCost = $this->TotalizeCost($structures);

            //Build the body of the mail
            $body = "Flex Structure Overhead Cost is due for the following structures:<br>";
            foreach($structures as $structure) {
                $body += "System: " . $structure->system . " - " . $structure->structure_type . ": " . $structure->structure_cost . " ISK<br>";
            }
            $body += "Total Cost: " . number_format($totalCost, 2,".", ",");
            $body += "Please remit payment to Spatial Forces by the 3rd of the month.<br>";
            $body += "Sincerely,<br>";
            $body += "Warped Intentions Leadership<br>";

            //Dispatch the mail job
            $mail = new JobSendEveMail;
            $mail->sender = $config['primary'];
            $mail->subject = "Warped Intentions Flex Structures Payment Due for " . $today->englishMonth;
            $mail->body = $body;
            $mail->recipient = (int)$structure->requestor_id;
            $mail->recipient_type = 'character';
            ProcessSendEveMailJob::dispatch($mail)->onQueueu('mail')->delay($delay);

            //Increment the delay for the mail to not hit the rate limits
            $delay += 60;

            //After the mail is dispatched, save the sent mail record
            $this->SaveSentRecord($mail->sender, $mail->subject, $mail->body, $mail->recipient, $mail->recipient_type);
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }

    private function TotalizeCost($structures) {
        //Declare the total cost
        $totalCost = 0.00;
        
        foreach($structures as $structure) {
            $totalCost += $structure->structure_cost;
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
