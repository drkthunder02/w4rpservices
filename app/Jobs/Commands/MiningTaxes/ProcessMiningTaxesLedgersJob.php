<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//App Library
use App\Library\Helpers\LookupHelper;
use App\Library\Moons\MoonCalc;

//Models
use App\Models\MiningTax\Ledger;
use App\Models\Moon\MineralPrice;
use App\Models\Moon\ItemComposition;

class ProcessMiningTaxesLedgersJob implements ShouldQueue
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
     * Job Variables
     */
    private $ledger;
    private $observerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ledger, $observerId)
    {
        //Set the connection for the job
        $this->connection = 'redis';

        //Import variables from the calling function
        $this->ledger = $ledger;
        $this->observerId = $observerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lookup = new LookupHelper;
        $mHelper = new MoonCalc;

        //Get some of the basic information we need to work with
        $charName = $lookup->CharacterIdToName($this->ledger->character_id);
        //Get the type name from the ledger ore
        $typeName = $lookup->ItemIdToName($this->ledger->type_id);
        //Get the price from the helper function
        $price = $mHelper->CalculateOrePrice($this->ledger->type_id);
        //Calculate the total price based on the amount
        $amount = $price * $this->ledger->quantity;

        $found = Ledger::where([

        ])->count();

        if($found > 0) {
            Ledger::where([
                'character_id' => $this->ledger->character_id,
                'character_name' => $charName,
                'observer_id' => $this->observerId,
                'type_id' => $this->ledger->type_id,
                'ore_name' => $typeName,
            ])->update([
                'character_id' => $this->ledger->character_id,
                'character_name' => $charName,
                'observer_id' => $this->observerId,
                'last_updated' => $this->ledger->last_updated,
                'type_id' => $this->ledger->type_id,
                'ore_name' => $typeName,
                'quantity' => $this->ledger->quantity,
                'amount' => $amount,
            ]);
        } else {
            $ledg = new Ledger;
            $ledg->character_id = $this->ledger->character_id;
            $ledg->character_name = $charName;
            $ledg->observer_id = $this->observerId;
            $ledg->last_updated = $ledger->last_updated;
            $ledg->type_id = $ledger->type_id;
            $ledg->ore_name = $typeName;
            $ledg->quantity = $ledger->quantity;
            $ledg->amount = $amount;
            $ledg->save();
        }
    }
}
