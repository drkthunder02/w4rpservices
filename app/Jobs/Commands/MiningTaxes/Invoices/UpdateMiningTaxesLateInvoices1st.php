<?php

namespace App\Jobs\Commands\MiningTaxes\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//Application Library
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MiningTax\Invoice;
use App\Models\User\User;
use App\Models\User\UserAlt;

//Jobs
use App\Jobs\Commands\Eve\SendEveMail;

class UpdateMiningTaxesLateInvoices1st implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Number of job retries
     * 
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = 'redis';
        $this->onQueue('miningtaxes');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $lookup = new LookupHelper;
        $config = config('esi');
        $mailDelay = 15;
        $today = Carbon::now();

        //Get all of the invoices that are still pending.
        $invoices = Invoice::where([
            'status' => 'Pending',
        ])->get();

        //Cycle through the invoices, and see if they are late or not.
        foreach($invoices as $invoice) {
            $dueDate = Carbon::create($invoice->date_due);

            if($dueDate->greaterThan($today->subDays(7))) {
                //Update the invoice in the database
                Invoice::where([
                    'invoice_id' => $invoice->invoice_id,
                ])->update([
                    'status' => 'Late',
                ]);

                //Build the mail
                $subject = 'Warped Intentions Mining Taxes - Invoice Late';
                $sender = $config['primary'];
                $recipientType = 'character';
                $recipient = $invoice->character_id;

                $body = "Dear " . $invoice->character_name . ",<br><br>";
                $body .= "The Mining Invoice: " . $invoice->invoice_id . " is late.<br>";
                $body .= "Please remite " . number_format($invoice->invoice_amount, 2, ".", ",") . "to Spatial Forces.<br>";
                $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";

                //Send a reminder to the user through eve mail about the late invoice
                SendEveMail::dispatch($body, $recipient, $recipientType, $subject, $sender)->delay(Carbon::now()->addSeconds($mailDelay));

                $mailDelay += 20;
            }
        }
    }

    /**
     * Set the tags for Horzion
     * 
     * @var array
     */
    public function tags() {
        return ['UpdateMiningTaxesLateInvoices', 'MiningTaxes', 'Invoices'];
    }
}
