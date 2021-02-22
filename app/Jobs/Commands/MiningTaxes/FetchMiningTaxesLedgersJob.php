<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestionFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;
use App\Library\Moons\MoonCalc;

//App Models
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Ledger;
use App\Models\Moon\MineralPrice;
use App\Models\Moon\ItemComposition;

class FetchMiningTaxesLedgersJob implements ShouldQueue
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
    private $charId;
    private $corpId;
    private $observerId;
    private $esi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId, $corpId, $observerId)
    {
        //Set the connection for the job
        $this->connection = 'redis';

        //Import the variables from the calling function
        $this->charId = $charId;
        $this->corpId = $corpId;
        $this->observerId = $observerId;

        //Setup the ESI stuff
        $esiHelper = new Esi;

        //Setup the private esi variables
        if(!$esiHelper->haveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1')) {
            Log::critical('Character: ' . $this->charId . ' did not have the correct esi scope in FetchMiningTaxesLedgersJob.');
            return null;
        }
        $refreshToken = $esiHelper->GetRefreshToken($this->charId);
        $this->esi = $esiHelper->SetupEsiAuthentication($refreshToken);
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
        $mHelper = new MoonCalc;
        $esiHelper = new Esi;

        //Get the ledger from ESI
        try {
            $ledgers = $this->esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                'corporation_id' => $this->corpId,
                'observer_id' => $this->observerId,
            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Failed to get the mining ledger in FetchMiningTaxesLedgersJob for observer id: ' . $this->observerId);
            return null;
        }

        //Sort through the array, and create the variables needed for database entries
        foreach($ledgers as $ledger) {
            //Get some basic information we need to work with
            $charName = $lookup->CharacterIdToName($ledger->character_id);
            //Get the type name from the ledger ore stuff
            $typeName = $lookup->ItemIdToName($ledger->type_id);
            //Decode the date and store it.
            $updated = $esiHelper->DecodeDate($ledger->last_updated);

            $price = $mHelper->CalculateOrePrice($ledger->type_id);
            $amount = $price * $ledger->quantity;

            //Insert or update the entry in the database
            $item = Ledger::updateOrCreate([
                'character_id' => $ledger->character_id,
                'character_name' => $charName,
                'observer_id' => $this->observerId,
                'last_updated' => $updated,
                'type_id' => $ledger->type_id,
                'ore_name' => $typeName,
                'quantity' => $ledger->quantity,
                'price' => $amount,
            ], [
                'character_id' => $ledger->character_id,
                'character_name' => $charName,
                'observer_id' => $this->observerId,
                'last_updated' => $updated,
                'type_id' => $ledger->type_id,
                'ore_name' => $typeName,
                'quantity' => $ledger->quantity,
                'price' => $amount,
            ]);
        }

        //Clean up old data
        Ledger::where(['updated_at', '<', Carbon::now()->subDays(120)])->delete();
    }
}
