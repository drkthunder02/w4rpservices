<?php

namespace App\Jobs\Commands\MiningTaxes;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        $mailDelay = 15;

        //Get the users from the database
        $mains = User::all();

        /**
         * For each of the users, let's determine if there are any ledgers,
         * then determine if there are any alts and ledgers associated with the alts.
         */
        foreach($mains as $main) {
            //Declare some variables for each run through the for loop
            $ledgers = new Collection;

            //Count the ledgers for the main
            $mainLedgerCount = Ledger::where([
                'character_id' => $main->character_id,
                'invoiced' => 'No',
            ])->where('last_updated', '>', Carbon::now()->subDays(7))->count();

            //If there are ledgers for the main, then let's grab them
            if($mainLedgerCount > 0) {
                $mainLedgers = Ledger::where([
                    'character_id' => $main->character_id,
                    'invoiced' => 'No',
                ])->where('last_updated', '>', Carbon::now()->subDays(7))->get();

                //Cycle through the entries, and add them to the ledger to send with the invoice
                foreach($mainLedgers as $row) {
                    $ledgers->push([
                        'character_id' => $row->character_id,
                        'character_name' => $row->character_name,
                        'type_id' => $row->type_id,
                        'ore_name' => $row->ore_name,
                        'quantity' => $row->quantity,
                        'amount' => (float)$row->amount,
                    ]);
                }
            }

            //Get the alt count for the main character
            $altCount = $main->altCount();
            //If more than 0 alts, grab all the alts.
            if($altCount > 0) {
                $alts = UserAlt::where([
                    'main_id' => $main->character_id,
                ])->get();

                //Cycle through the alts, and get the ledgers, and push onto the stack
                foreach($alts as $alt) {
                    $altLedgerCount = Ledger::where([
                        'character_id' => $alt->character_id,
                        'invoiced' => 'No',
                    ])->where('last_updated', '>', Carbon::now()->subDays(7))->count();

                    if($altLedgerCount > 0) {
                        $altLedgers = Ledger::where([
                            'character_id' => $alt->character_id,
                            'invoiced' => 'No',
                        ])->where('last_updated', '>', Carbon::now()->subDays(7))->get();

                        foreach($altLedgers as $row) {
                            $ledgers->push([
                                'character_id' => $row->character_id,
                                'character_name' => $row->character_name,
                                'type_id' => $row->type_id,
                                'ore_name' => $row->ore_name,
                                'quantity' => $row->quantity,
                                'amount' => (float)$row->amount,
                            ]);
                        }
                    }
                }
            }

            /**
             * Send the collected information over to the function to send the actual mail
             */
            if($ledgers->count() > 0) {
                $this->CreateInvoice($main->character_id, $ledgers, $mailDelay);
            }
        }
    }

    /**
     * Create the invoice to the mail out
     * 
     * @var charId
     * @var ledgers
     * @var mailDelay
     */
    private function CreateInvoice($charId, Collection $ledgers, int &$mailDelay) {
        $ores = array();
        $characters = array();
        $characterIds = array();
        $totalPrice = 0.00;
        $body = null;
        $lookup = new LookupHelper;
        $config = config('esi');

        //Create an invoice id 
        $invoiceId = "M" . uniqid();

        //Get the sum of all the ledgers
        $invoiceAmount = round(($ledgers->sum('amount') * $config['mining_tax']), 2);

        //Get the character name from the lookup table
        $charName = $lookup->CharacterIdToName($charId);

        //Create the date due and the invoice date
        $dateDue = Carbon::now()->addDays(7);
        $invoiceDate = Carbon::now();

        //Set the mining tax from the config file
        $numberMiningTax = number_format(($config['mining_tax'] * 100.00), 2, ".", ",");

        //Create the list of ores to put in the mail
        $temp = $ledgers->toArray();
        foreach($temp as $t) {
            //If the key isn't set, set it to the default of 0
            if(!isset($ores[$t['type_id']])) {
                $ores[$t['type_id']] = 0;
            }
            //Add the quantity into the ores array
            $ores[$t['type_id']] += $t['quantity'];

            if(!isset($characters[$t['character_name']])) {
                $characters[$t['character_name']] = $t['character_name'];
            }

            if(!isset($characterIds[$t['character_id']])) {
                $characterIds[$t['character_id']] = $t['character_id'];
            }
        }

        /**
         * Create the mail body to send to the main character
         */
        $body .= "Dear " . $charName . ",<br><br>";
        $body .= "Mining Taxes are due for the following ores mined from alliance moons: <br>";
        foreach($ores as $ore => $quantity) {
            $oreName = $lookup->ItemIdToName($ore);
            $body .= $oreName . ": " . number_format($quantity, 0, ".", ",") . "<br>";
        }
        $body .= "Total Value of Ore Mined: " . number_format($totalPrice, 2, ".", ",") . " ISK.";
        $body .= "<br><br>";
        $body .= "Please remit " . number_format($invoiceAmount, 2, ".", ",") . " ISK to Spatial Forces by " . $dateDue . "<br>";
        $body .= "Set the reason for transfer as " . $invoiceId . "<br>";
        $body .= "The mining taxes are currently set to " . $numberMiningTax . "%.<br>";
        $body .= "<br><br>";
        $body .= "You can also send a contract with the following ores in the contract with the reason set as: " . $invoiceId . "<br>";
        foreach($ores as $ore => $quantity) {
            $oreName = $lookup->ItemIdToName($ore);
            $body .= $oreName . ": " . number_format(round($quantity * $config['mining_tax']), 0, ".", ",") . "<br>";
        }
        $body .= "<br>";
        $body .= "Characters Processed: <br>";
        foreach($characters as $character) {
            $body .= $character . "<br>";
        }
        $body .= "<br>";
        $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";

        //Check if the mail body is greater than 2000 characters.  If greater than 2,000 characters, then 
        if(strlen($body) > 2000) {
            $body = "Dear " . $charName . "<br><br>";
            $body .= "Total Value of Ore Mined: " . number_format($totalPrice, 2, ".", ",") . " ISK.";
            $body .= "<br><br>";
            $body .= "Please remit " . number_format($invoiceAmount, 2, ".", ",") . " ISK to Spatial Forces by " . $dateDue . "<br>";
            $body .= "Set the reason for transfer as: " . $invoiceId . "<br>";
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
        SendEveMail::dispatch($body, $recipient, $recipientType, $subject, $sender)->delay(Carbon::now()->addSeconds($mailDelay));

        /**
         * Create a new invoice model, and save it to the database
         */
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

        /**
         * Mark the invoices as paid
         */
        foreach($characterIds as $char) {
            Ledger::where([
                'character_id' => $char,
                'invoiced' => 'No',
            ])->update([
                'invoice' => $invoiceId,
                'invoiced' => 'Yes',
            ]);
        }

        /**
         * Increment the mail delay for the next cycle
         */
        $mailDelay += 20;
    }

    /**
     * Set the tags for Horizon
     * 
     * @var array
     */
    public function tags() {
        return ['MiningTaxes', 'SendMiningTaxesInvoics', 'Invoices'];
    }
}
