<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Commands\Library\CommandHelper;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;
use App\Library\Moons\MoonCalc;

//Models
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Ledger;
use App\Models\Moon\MineralPrice;
use App\Models\Moon\ItemComposition;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

//Jobs
//use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesLedgersJob;

class MiningTaxesLedgers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Ledgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start getting the mining ledgers.';

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
        //Create the command helper container
        $task = new CommandHelper('MiningTaxesLedger');
        //Set the task as started
        $task->SetStartStatus();

        //Get the site configuration which holds some data we need
        $config = config('esi');
        //Get the observers from the database
        $observers = Observer::all();
        //Job Variables to be moved later
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $mHelper = new MoonCalc;
        $esiHelper = new Esi;
        /*
        //For each of the observers, send a job to fetch the mining ledger
        foreach($observers as $obs) {
            //Dispatch the mining taxes ledger jobs
            FetchMiningTaxesLedgersJob::dispatch($config['primary'], $config['corporation'], $obs->observer_id)->onQueue('miningtaxes');
        }
        */

        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        foreach($observers as $obs) {
            try {
                $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                    'corporation_id' => $config['corporation'],
                    'observer_id' => $obs->observer_id,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get the mining ledger in FetchMiningTaxesLedgersCommand for observer id: ' . $this->observerId);
                return null;
            }

            $ledgers = json_decode($ledgers);

            foreach($ledgers as $ledger) {
                //Get some basic information we need to work with
                $charName = $lookup->CharacterIdToName($ledger->character_id);
                //Get the type name from the ledger ore stuff
                $typeName = $lookup->ItemIdToName($ledger->type_id);
                //Decode the date and store it.
                //$updated = $esiHelper->DecodeDate($ledger->last_updated);

                $price = $mHelper->CalculateOrePrice($ledger->type_id);
                $amount = $price * $ledger->quantity;

                //Insert or update the entry in the database
                $item = Ledger::updateOrCreate([
                        'character_id' => $ledger->character_id,
                        'character_name' => $charName,
                        'observer_id' => $this->observerId,
                        'last_updated' => $ledger->last_updated,
                        'type_id' => $ledger->type_id,
                        'ore_name' => $typeName,
                        'quantity' => $ledger->quantity,
                        'price' => $amount,
                    ], [
                        'character_id' => $ledger->character_id,
                        'character_name' => $charName,
                        'observer_id' => $this->observerId,
                        'last_updated' => $ledger->last_updated,
                        'type_id' => $ledger->type_id,
                        'ore_name' => $typeName,
                        'quantity' => $ledger->quantity,
                        'price' => $amount,
                    ]);
            }
        }

        //Clean up old data
        Ledger::where(['updated_at', '<', Carbon::now()->subDays(120)])->delete();

        //Return 0
        return 0;
    }
}
