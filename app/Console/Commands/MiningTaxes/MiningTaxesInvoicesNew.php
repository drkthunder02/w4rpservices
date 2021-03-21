<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Commands\Library\CommandHelper;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Ledger;
use App\Models\User\UserAlt;
use App\Models\User\User;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

class MiningTaxesInvoicesNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:InvoiceNew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mining Taxes Invoice Command';

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
     * @return int
     */
    public function handle()
    {
        //Declare variables
        $lookup = new LookupHelper;
        $config = config('esi');
        $task = new CommandHelper('MiningTaxesInvoices');
        $mainsAlts = array();
        $mailDelay = 15;
        //Set the task as started
        $task->SetStartStatus();

        //Get the characters for each non-invoiced ledger entry
        $charIds = Ledger::where([
            'invoiced' => 'No',
                       ])->distinct('character_id')
                         ->pluck('character_id');
        
        //If character ids are null, then there are no ledgers to process.
        if($charIds->count() == 0) {
            //Set the task status as done and Log the issue
            $task->SetStopStatus();
            Log::warning("No characters found to send invoices to in MiningTaxesInvoices Command.");
            return 0;
        }

        /**
         * From the list of character ids, create an array of mains and alts to group together.
         */
        foreach($charIds as $charId) {
            $invoice = array();
            $ores = array();
            $totalPrice = 0.00;
            $body = null;
            $mainId = null;

            //Determine if this character is an alt of someone else
            $foundAlt = UserAlt::where([
                'character_id' => $charId,
            ])->get();
            
            //If we found an alt, then take the main's id and use it for the next set of calculations
            if($foundAlt->count() > 0) {
                $mainId = $foundAlt->main_id;
            } else {
                //If we didn't find an alt, then we assume this is the main character's id
                $mainId = $charId;
            }

            //Build a character Id list for the characters before processing the ledgers.
            $alts = UserAlt::where([
                'main_id' => $mainId,
            ])->get();

            //Get the ledgers for the main character
            $rows = Ledger::where([
                'character_id' => $mainId,
            ])->get();

            foreach($alts as $alt) {
                $altLedger = Ledger::where([
                    'character_id' => $alt->character_id,
                ])->get();

                $rows->push($altLedger);
            }

            dd($rows);

            if($rows->count() > 0) {
                //Create the ore set for later for item composition and other functions
                foreach($rows as $row) {
                    if(!isset($ores[$row['type_id']])) {
                        $ores[$row['type_id']] = 0;
                    }
                    //Add up the ores from each row
                    $ores[$row['type_id']] = $ores[$row['type_id']] + $row['quantity'];

                    //Add up the total price from the ledger rows for the totalized cost
                    $totalPrice = $totalPrice + $row['amount'];
                }

                //Reduce the total price by the take percentage
                $invoiceAmount = round(($totalPrice * $config['mining_tax']), 2);

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
                if(sizeof($body) > 2000) {
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

                /**
                 * Send the eve mail out to the character.
                 * Disabled currently for testing
                 * ProcessSendEveMailJob::dispatch($body, $recipient, $recipientType, $subject, $sender)->onQueue('mail')->delay(Carbon::now()->addSeconds($mailDelay));
                 */

                /**
                 * Save the invoice using eloquent models.
                 * Currently disabled for testing.
                 * $invoice = new Invoice;
                 * $invoice->character_id = $charId;
                 * $invoice->character_name = $charName;
                 * $invoice->invoice_id = $invoiceId;
                 * $invoice->invoice_amount = $invoiceAmount;
                 * $invoice->date_issued = $invoiceDate;
                 * $invoice->date_due = $dateDue;
                 * $invoice->status = 'Pending';
                 * $invoice->mail_body = $body;
                 * $invoice->save();
                 */

                /**
                 * Update the ledger entries.
                 * This is disabled for testing currently.
                 * //Update the ledger entries
                 * Ledger::where([
                 *   'character_id' => $charId,
                 *   'invoiced' => 'No',
                 * ])->update([
                 *   'invoiced' => 'Yes',
                 *   'invoice_id' => $invoiceId,
                 * ]);
                 */

                //Update the delay
                $mailDelay = $mailDelay + 20;
            }
        }

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
