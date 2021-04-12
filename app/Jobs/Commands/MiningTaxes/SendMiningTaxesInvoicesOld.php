<?php

namespace App\Jobs\Commands\MiningTaxes;

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
use App\Models\MiningTax\Ledger;
use App\Models\User\UserAlt;
use App\Models\User\User;

//Jobs
use App\Jobs\Commands\Eve\SendEveMail;


class SendMiningTaxesInvoicesOld implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

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
        $lookup = new LookupHelper;
        $config = config('esi');
        $mailDelay = 15;

        //Get the characters for each non-invoiced ledger entry
        $charIds = Ledger::where([
            'invoiced' => 'No',
                       ])->distinct('character_id')
                         ->pluck('character_id');

        if($charIds == null) {
            //Set the task status as done and Log the issue
            $task->SetStopStatus();
            Log::warning("No characters found to send invoices to in MiningTaxesInvoices Command.");
            return 0;
        }

        //Foreach character tally up the mining ledger.
        foreach($charIds as $charId) {
            //Declare some variables we need for each iteration of the loop
            $invoice = array();
            $ores = array();
            $totalPrice = 0.00;
            $body = null;

            //Get the row count to reduce overhead processing if we can.
            $rowCount = Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->count();
            
            //Get the actual rows
            $rows = Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->get()->toArray();

            //Taly up the item composition from each row and multiply by the quantity
            if($rowCount > 0) {
                foreach($rows as $row) {
                    if(!isset($ores[$row['type_id']])) {
                        $ores[$row['type_id']] = 0;
                    }
                    $ores[$row['type_id']] = $ores[$row['type_id']] + $row['quantity'];
    
                    //Add up the total price from the ledger rows for the report later
                    $totalPrice = $totalPrice + $row['amount'];
                }
    
                //Reduce the total price by the take percentage
                $invoiceAmount = $totalPrice * $config['mining_tax'];
                $invoiceAmount = round($invoiceAmount, 2);
                
                //Get the character name from the character id
                $charName = $lookup->CharacterIdToName($charId);
    
                //Generate a unique invoice id
                $invoiceId = uniqid();
                //Set the due date of the invoice
                $dateDue = Carbon::now()->addDays(7);
                $invoiceDate = Carbon::now();
    
                //Format the mining tax into a human readable number
                $numberMiningTax = number_format(($config['mining_tax'] * 100.00), 2, ".", ",");
    
                //Create the mail body
                $body .= "Dear Miner,<br><br>";
                $body .= "Mining Taxes are due for the following ores mined from alliance moons: <br>";
                foreach($ores as $ore => $quantity) {
                    $oreName = $lookup->ItemIdToName($ore);
                    $body .= $oreName . ": " . number_format($quantity, 0, ".", ",") . "<br>";
                }
                $body .= "Total Value of Ore Mined: " . number_format($totalPrice, 2, ".", ",") . " ISK.";
                $body .= "<br><br>";
                $body .= "Please remit " . number_format($invoiceAmount, 2, ".", ",") . " ISK to Spatial Forces by " . $dateDue . "<br>";
                $body .= "Set the reason for transfer as MMT: " . $invoiceId . "<br>";
                $body .= "The mining taxes are currently set to " . $numberMiningTax . "%.<br>";
                $body .= "<br><br>";
                $body .= "You can also send a contract with the following ores in the contract with the reason set as MMT: " . $invoiceId . "<br>";
                foreach($ores as $ore => $quantity) {
                    $oreName = $lookup->ItemIdToName($ore);
                    $body .= $oreName . ": " . number_format(round($quantity * $config['mining_tax']), 0, ".", ",") . "<br>";
                }
                $body .= "<br>";
                $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";
    
                //Check if the mail body is greater than 2000 characters.  If greater than 2,000 characters, then 
                if(strlen($body) > 2000) {
                    $body = "Dear Miner,<br><br>";
                    $body .= "Total Value of Ore Mined: " . number_format($totalPrice, 2, ".", ",") . " ISK.";
                    $body .= "<br><br>";
                    $body .= "Please remit " . number_format($invoiceAmount, 2, ".", ",") . " ISK to Spatial Forces by " . $dateDue . "<br>";
                    $body .= "Set the reason for transfer as MMT: " . $invoiceId . "<br>";
                    $body .= "The mining taxes are currently set to " . $numberMiningTax . "%.<br>";
                    $body .= "<br>";
                    $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";
                }
    
                //Mail the invoice to the character if the character is in
                //Warped Intentions or Legacy
                $subject = 'Warped Intentions Mining Taxes';
                $sender = $config['primary'];
                $recipientType = 'character';
                $recipient = $charId;
    
                //Send the Eve Mail Job to the queue to be dispatched
                SendEveMail::dispatch($body, $recipient, $recipientType, $subject, $sender)->onQueue('mail')->delay(Carbon::now()->addSeconds($mailDelay));
    
                //Save the invoice model
                $invoice = new Invoice;
                $invoice->character_id = $charId;
                $invoice->character_name = $charName;
                $invoice->invoice_id = $invoiceId;
                $invoice->invoice_amount = $invoiceAmount;
                $invoice->date_issued = $invoiceDate;
                $invoice->date_due = $dateDue;
                $invoice->status = 'Pending';
                $invoice->mail_body = $body;
                $invoice->save();
    
                //Update the ledger entries
                Ledger::where([
                    'character_id' => $charId,
                    'invoiced' => 'No',
                ])->update([
                    'invoiced' => 'Yes',
                    'invoice_id' => $invoiceId,
                ]);
    
                //update the delay
                $mailDelay = $mailDelay + 20;
            }
        }
    }
}
