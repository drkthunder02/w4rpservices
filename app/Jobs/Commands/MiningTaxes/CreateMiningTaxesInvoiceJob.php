<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Log;

//Library
use App\Library\Helpers\LookupHelper;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

//Models
use App\Models\MiningTaxes\Invoice;
use App\Models\MiningTaxes\Ledger;

class CreateMiningTaxesInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //Private Variables
    private $ores;
    private $totalPrices;
    private $charId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ores = [], $totalPrice, $charId)
    {
        $this->ores = $ores;
        $this->totalPrice = $totalPrice;
        $this->charId = $charId;

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
        $body = '';

        //Get the character name from the character id
        $charName = $lookup->CharacterIdToName($this->charId);

        //Generate an invoice id
        $invoiceId = uniqid();

        //Get the mining ledgers, and totalize the price

        $invoice = new Invoice;
        $invoice->character_id = $this->charId;
        $invoice->character_name = $charName;
        $invoice->invoice_id = $invoiceId;
        $invoice->invoice_amount = $this->totalPrice;
        $invoice->date_issued = Carbon::now();
        $invoice->date_due = Carbon::now()->addDays(7);
        $invoice->status = 'Pending';
        $invoice->save();

        //Update the entries in the mining tax ledgers table to show the ore has been invoiced
        Ledger::where([
            'character_id' => $this->charId,
            'invoiced' => 'No',
        ])->update([
            'invoiced' => 'Yes',
            'invoice_id' => $invoiceId,
        ]);

        $body .= "Dear Miner,<br><br>";
        $body .= "Mining Taxes are due for the following ores mined from alliance moons: <br>";
        foreach($this->ores as $ore => $quantity) {
            $oreName = $lookup->ItemIdToName($ore);
            $body .= $oreName . ": " . number_format($quantity, 0, ".", ",") . "<br>";
        }
        $body .= "Please remit " . number_format($this->totalPrice, 2, ".", ",") . " ISK to Spatial Forces by " . $invoice->date_due . "<br>";
        $body .= "Set the reason for transfer as MMT: " . $invoice->invoice_id . "<br>";
        $body .= "<br>Sincerely,<br>Warped Intentions Leadership<br>";

        //Mail the invoice to the character if the character is in
        //Warped Intentions or Legacy
        $subject = 'Warped Intentions Mining Taxes';
        $sender = $config['primary'];
        $recipienttype = 'character';
        $recipient = $this->charId;

        ProcessSendEveMailJob::dispatch($body, $recipient, $recipientType, $subject, $sender)->onQueue('mail')->delay(Carbon::now()->addSeconds(30));
    }
}
