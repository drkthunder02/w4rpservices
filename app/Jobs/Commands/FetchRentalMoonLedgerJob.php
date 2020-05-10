<?php

namespace App\Jobs\Commands;

//Internal Libraries
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

//App Models
use App\Models\RentalMoonLedger;
use App\Models\RentalMoonObserver;

class FetchRentalMoonLedgerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

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
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $response = null;
        $structureInfo = null;
        $entries = array();

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

                    $entries[] = [
                        'corporation_id' => $corpId,
                        'corporation_name' => $corpName,
                        'character_id' => $ledger->character_id,
                        'character_name' => $charInfo->name,
                        'observer_id' => $observer->observer_id,
                        'observer_name' => $observerName,
                        'type_id' => $ledger->type_id,
                        'ore' => $ore,
                        'quantity' => $ledger->quantity,
                        'recorded_corporation_id' => $ledger->recorded_corporation_id,
                        'recorded_corporation_name' => $recordedCorpName,
                        'last_updated' => $ledger->last_updated,
                        'created_at' => $ledger->last_updated . ' 23:59:59',
                        'updated_at' => $ledger->last_updated . ' 23:59:59',
                    ];
                }

                //Insert or ignore each of the records saved into the array through the foreach loop
                RentalMoonLedger::insertOrIgnore($entries);

                Log::info(
                    'FetchRentalMoonLedgerJob inserted up to ' .count($entires) . ' new ledger entries.'
                );

            }
        } 
    }
}
