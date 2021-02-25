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

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

class MiningTaxesInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Invoice';

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
        $body = null;
        $task = new CommandHelper('MiningTaxesInvoices');
        //Set the task as started
        $task->SetStartStatus();

        //Get the characters for each non-invoiced ledger entry
        $charIds = Ledger::distinct('character_id')->pluck('character_id');

        //Foreach character tally up the mining ledger.
        foreach($charIds as $charId) {
            //Declare some variables we need for each iteration of the loop
            $invoice = array();
            $ores = array();
            $totalPrice = 0.00;

            //Get the rows from the database for each character and the requirement of not been invoiced yet
            $rows = Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->get()->toArray();        

            var_dump($rows);
            die();

            //Taly up the item composition from each row and multiply by the quantity
            foreach($rows as $row) {
                if(!isset($ores[$row->type_id])) {
                    $ores[$row->type_id] = 0;
                }
                $ores[$row->type_id] = $ores[$row->type_id] + $row->quantity;

                //Add up the total price from the ledger rows for the report later
                $totalPrice = $totalPrice + $row->amount;
                dd($row->amount);
            }

            //Reduce the total price by the take percentage
            $invoiceAmount = $totalPrice * 0.10;

            //Get the character name from the character id
            $charName = $lookup->CharacterIdToName($charId);

            //Generate a unique invoice id
            $invoiceId = uniqid();
            dd($invoiceId);
            //Save the invoice model
            $invoice = new Invoice;
            $invoice->character_id = $charId;
            $invoice->character_name = $charName;
            $invoice->invoice_id = $invoiceId;
            $invoice->invoice_amount = $invoiceAmount;
            $invoice->date_issued = Carbon::now();
            $invoice->date_due = Carbon::now()->addDays(7);
            $invoice->status = 'Pending';
            $invoice->save();

            //Update the ledger entries
            Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->update([
                'invoiced' => 'Yes',
                'invoice_id' => $invoiceId,
            ]);

            //Create the mail body
            $body .= "Dear Miner,<br><br>";
            $body .= "Mining Taxes are due for the following ores mined from alliance moons: <br>";
            foreach($ores as $ore => $quantity) {
                $oreName = $lookup->ItemIdToName($ore);
                $body .= $oreName . ": " . number_format($quantity, 0, ".", ",") . "<br>";
            }
            $body .= "Please remit " . number_format($totalPrice, 2, ".", ",") . " ISK to Spatial Forces by " . $invoice->date_due . "<br>";
            $body .= "Set the reason for transfer as MMT: " . $invoice->invoice_id . "<br>";
            $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";

            //Mail the invoice to the character if the character is in
            //Warped Intentions or Legacy
            $subject = 'Warped Intentions Mining Taxes';
            $sender = $config['primary'];
            $recipientType = 'character';
            $recipient = $config['primary'];

            //Send the Eve Mail Job to the queue to be dispatched
            ProcessSendEveMailJob::dispatch($body, $recipient, $recipientType, $subject, $sender)->onQueue('mail');
        }

        //CalculateMiningTaxesJob::dispatch()->onQueue('miningtaxes');

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
