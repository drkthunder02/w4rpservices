<?php

namespace App\Console\Commands\Moons;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Structures\StructureHelper;
use App\Library\Lookups\LookupHelper;

//App Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Lookups\ItemLookup;
use App\Models\RentalMoonLedger;
use App\Models\RentalMoonObserver;

class FetchRentalMoonLedgerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:FetchRentalMoonLedgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches rental moon ledgers from EVE API.';

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
     * @return mixed
     */
    public function handle()
    {
        //Create the command helper container
        $task = new CommandHelper('RentalMoonLedger');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Declare variables
        $structures = array();
        $miningLedgers = array();
        $tempMiningLedger = array();
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $response = null;
        $structureInfo = null;

        //Get the configuration for the main site
        $config = config('esi');

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1') || !$esiHelper->HaveEsiScope($config['primary'], 'esi-universe.read_structures.v1')) {
            Log::critical('The primary character does not have the necessary scopes for FetchRentalMoonLedgerCommand.');
            return;
        }

        //Get the refresh token if scope checks have passed
        $refreshToken = $esiHelper->GetRefreshtoken($config['primary']);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo($config['primary']);

        //Get all of the rental moon observers from the database
        $observers = RentalMoonObserver::all();

        //Dump the mining ledger table for rental moons
        RentalMoonLedger::truncate();

        //Foreach observer get the ledger
        foreach($observers as $observer) {
            //Get the observer name.
            
            try {
                $ledgers = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                    'corporation_id' => $character->corporation_id,
                    'observer_id' => $observer->observer_id,
                ]);
            } catch(RequestFailedException $e) {
                //If an exception has occurred, then log it
                Log::critical('FetchRentalMoonLedger command failed to get the mining ledger for observer id: ' . $observer->observer_id);
            }
            
            if($ledgers != null) {
                foreach($ledgers as $ledger) {
                    //Get the ore name from the lookup table
                    $ore = $lookup->ItemIdToName($ledger->type_id);

                    //Get the character name from the lookup helper using the characterId
                    $charInfo = $lookup->GetCharacterInfo($ledger->character_id);
                    //Get the corporation information
                    $corpInfo = $lookup->GetCorporationInfo($charInfo->corporation_id);

                    $newLedger = new RentalMoonLedger;
                    $newLedger->corporation_id = $corpId;
                    $newLedger->corporation_name = $corpName;
                    $newLedger->character_id = $ledger->character_id;
                    $newLedger->character_name = $charInfo->name;
                    $newLedger->observer_id = $observer->observer_id;
                    $newLedger->observer_name = $observerName;
                    $newLedger->type_id = $ledger->type_id;
                    $newLedger->ore = $ore        ;
                    $newLedger->quantity = $ledger->quantity;
                    $newLedger->recorded_corporation_id = $ledger->recorded_corporation_id;
                    $newLedger->recorded_corporation_name = $recordedCorpName;
                    $newLedger->last_updated = $ledger->last_updated;
                    $newLedger->save();
                }
            }
        }                

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
