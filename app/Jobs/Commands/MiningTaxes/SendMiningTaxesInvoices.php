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

class SendMiningTaxesInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $mainAlts = array();
        $mailDelay = 15;
        $mainIds = new Collection;

        //Get all of the users in the database
        $users = User::all();

        //Get a list of the alts for each character, then process the ledgers and combine them to send one mail out
        //in this first part
        foreach($users as $char) {
            //Gather up all of the ledgers from the character and its alts.
            $ledgers = $this->LedgersWithAlts($char->character_id);

            if(sizeof($ledgers) > 0) {
                //Create an invoice from the ledger rows
                $this->CreateInvoice($char->character_id, $ledgers, $mailDelay);
            }
        }

        //Get the ledgers characters which haven't had an invoice created yet.
        $charIds = Ledger::where([
            'invoiced' => 'No',
        ])->distinct('character_id')
          ->pluck('character_id');

        if($charIds == null) {
            return 0;
        }

        $this->CreateOtherInvoices($charIds, $mailDelay);

        return 0;
    }

    private function CreateOtherInvoices($charIds, $mailDelay) {
        foreach($charIds as $charId) {
            $invoice = array();
            $ores = array();
            $totalPrice = 0.00;
            $body = null;
            $lookup = new LookupHelper;
            $config = config('esi');

            //Get the actual rows
            $rows = Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->get()->toArray();

            //Taly up the item composition from each row and multiply by the quantity
            if(sizeof($rows) > 0) {
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
                    'invoiced' => 'No',
                    'invoice_id' => $invoiceId,
                ]);
    
                //update the delay
                $mailDelay = $mailDelay + 20;
            }
        }

        return 0;
    }

    private function CreateInvoice($charId, $ledgers, &$mailDelay) {
        $invoice = array();
        $ores = array();
        $characters = array();
        $totalPrice = 0.00;
        $body = null;
        $lookup = new LookupHelper;
        $config = config('esi');

        if(sizeof($ledgers) > 0) {
            foreach($ledgers as $ledger) {
                if(!isset($ores[$ledger['type_id']])) {
                    $ores[$ledger['type_id']] = 0;
                }
                $ores[$ledger['type_id']] = $ores[$ledger['type_id']] + $ledger['quantity'];
                $totalPrice = $totalPrice + $ledger['amount'];

                if(!isset($characters[$ledger['character_name']])) {
                    $characters[$ledger['character_name']] = $ledger['character_name'];
                }
            }

            $invoiceAmount = round(($totalPrice * $config['mining_tax']), 2);

            $charName = $lookup->CharacterIdToName($charId);

            $invoiceId = uniqid();
            $datedue = Carbon::now()->addDays(7);
            $invoiceDate = Carbon::now();

            $numberMiningTax = number_format(($config['mining_tax'] * 100.00), 2, ".", ",");

            //Create the mail body
            $body .= "Dear " . $charName . ",<br><br>";
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
            $body .= "Characters Processed: <br>";
            foreach($characters as $character) {
                $body .= $character['character_name'] . "<br>";
            }
            $body .= "<br>";
            $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";

            //Check if the mail body is greater than 2000 characters.  If greater than 2,000 characters, then 
            if(strlen($body) > 2000) {
                $body = "Dear " . $charName . "<br><br>";
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

            foreach($ledgers as $ledger) {
                Ledger::where([
                    'character_id' => $ledger['character_id'],
                    'invoiced' => 'No',
                ])->update([
                    'invoiced' => 'Yes',
                    'invoice_id' => $invoiceId,
                ]);

                $mailDelay += 20;
            }
        } else {
            return null;
        }

        return 0;
    }

    private function LedgersWithAlts($charId) {
        $ledgers = array();
        
        $alts = UserAlt::where([
            'main_id' => $charId,
        ])->get();

        $altCount = UserAlt::where([
            'main_id' => $charId,
        ])->count();

        $rows = Ledger::where([
            'character_id' => $charId,
            'invoiced' => 'No',
        ])->get();

        $mainCount = Ledger::where([
            'character_id' => $charId,
            'invoiced' => 'No',
        ])->count();

        if($mainCount > 0) {
            foreach($rows as $row) {
                array_push($ledgers, [
                    'character_id' => $row->character_id,
                    'character_name' => $row->character_name,
                    'observer_id' => $row->observer_id,
                    'last_updated' => $row->last_updated,
                    'type_id' => $row->type_id,
                    'ore_name' => $row->ore_name,
                    'quantity' => $row->quantity,
                    'amount' => $row->amount,
                    'invoiced' => $row->invoiced,
                    'invoice_id' => $row->invoice_id,
                ]);
            }
        }

        if($altCount > 0) {
            foreach($alts as $alt) {
                $rows = Ledger::where([
                    'character_id' => $alt->character_id,
                    'invoiced' => 'No',
                ])->get();

                $rowCount = Ledger::where([
                    'character_id' => $alt->character_id,
                    'invoiced' => 'No',
                ])->count();

                if($rowCount > 0) {
                    foreach($rows as $row) {
                        array_push($ledgers, [
                            'character_id' => $row->character_id,
                            'character_name' => $row->character_name,
                            'observer_id' => $row->observer_id,
                            'last_updated' => $row->last_updated,
                            'type_id' => $row->type_id,
                            'ore_name' => $row->ore_name,
                            'quantity' => $row->quantity,
                            'amount' => $row->amount,
                            'invoiced' => $row->invoiced,
                            'invoice_id' => $row->invoice_id,
                        ]);
                    }
                }
            }
        }
        
        //Return the ledgers
        return $ledgers;
    }

    /**
     * Set the tags for Horzion
     * 
     * @var array
     */
    public function tags() {
        return ['MiningTaxes', 'SendMiningTaxesInvoices', 'Invoices'];
    }
}
