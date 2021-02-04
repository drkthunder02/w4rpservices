<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Internal Library
use App\Library\Lookups\LookupHelper;
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
            ]);

            //Taly up the item composition from each row and multiply by the quantity
            foreach($rows as $row) {
                $ores[$row->type_id] = $ores[$row->type_id] + $row->quantity;
            }

            //From the item composition for each of the totaled ores, let's get the components and find the price
            foreach($ores as $itemId => $quantity) {
                //Get the price from the helper function for each unit of ore
                $price = $this->mHelper->CalculateOrePrice($itemId);

                //Add up the total and keep a running total
                $totalPrice += $price * $quantity;
            }

            //Create the invoice job
            CreateMiningTaxesInvoiceJob::dispatch($ores, $totalPrice, $char->character_id);
        }
    }
}
