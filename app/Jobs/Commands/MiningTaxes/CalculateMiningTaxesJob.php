<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Internal Library
use App\Library\Helpers\LookupHelper;
use App\Library\Moons\MoonCalc;

//Models
use App\Models\Moon\ItemComposition;
use App\Models\Moon\MineralPrice;
use App\Models\MiningTax\Ledger;
use App\Models\MiningTax\Invoice;

class CalculateMiningTaxesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //Private variables
    private $mHelper;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Declare variables for use in the handler
        $this->mHelper = new MoonCalc;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get the characters for each non-invoiced ledger entry
        $chars = Ledger::distinct('character_id')->pluck('character_id');

        //Foreach character tally up the mining ledger totals to create an invoice
        foreach($chars as $char) {
            //Declare some variables we need for each loop
            $invoice = array();
            $ores = array();
            $totalPrice = 0.00;
            //Get the rows from the database for each character and the requirement of not been
            //invoiced yet
            $rows = Ledger::where([
                'character_id' => $char->character_id,
                'invoiced' => 'No',
            ])->get();

            //Taly up the item composition from each row and multiply by the quantity
            foreach($rows as $row) {
                $ores[$row->type_id] = $ores[$row->type_id] + $row->quantity;
            }

            //Add up the total price from the ledger rows
            foreach($rows as $row) {
                $totalPrice = $totalPrice + $row->price;
            }

            //Reduce the total price by the take percentage
            $totalPrice = $totalPrice * 0.20;

            //Create the invoice job
            CreateMiningTaxesInvoiceJob::dispatch($ores, $totalPrice, $char->character_id);
        }
    }
}
