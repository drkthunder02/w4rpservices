<?php

namespace App\Jobs\Commands\Moons;

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
use App\Library\Structures\StructureHelper;

//App Models
use App\Models\Moon\CorpMoonLedger;
use App\Models\Moon\CorpMoonObserver;


class FetchMoonLedgerJob implements ShouldQueue
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
     * Private Variables
     */
    private $charId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId)
    {
        $this->charId = $charId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare Variables
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $response = null;
        $structureInfo = null;

        //Get the configuration for the main site
        $config = config('esi');

        //Check for esi scope for the character
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1') || !$esiHelper->HaveEsiScope($this->charId, 'esi-universe.read_structures.v1')) {
            Log::critical('The primary character does not have the necessary scopes for FetchRentalMoonLedgerCommand.');
            return;
        }

        //Get the refresh token if the scope checks have passed
        $refreshToken = $esiHelper->GetRefreshToken($this->charId);

        //Setup the esi information
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Get the character data from the lookup table
        $character = $lookup->GetCharacterInfo($this->charId);

        //Get the corporation data from the lookup table
        $corporation = $lookup->GetCorporationInfo($character->corporation_id);

        //Setup the structure helper
        $structure = new StructureHelper($this->charId, $character->corporation_id, $esi);

        //Get the moon observers from the database
        $observers = CorpMoonObserver::where([
            'corporation_id' => $character->corporation_id,
        ])->get();

        foreach($observers as $observer) {
            //Try to get the ledger data from the esi
            try {
                $ledgers = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                    'corporation_id' => $character->corporation_id,
                    'observer_id' => $observer->observer_id,
                ]);
            } catch(RequestFailedException $e) {
                //Log the exception
                Log::critical('FetchMoonLedger job failed to get the mining ledgers.');
            }

            if($ledgers != null) {
                foreach($ledgers as $ledger) {
                    //Get the ore name from the lookup table
                    $ore = $lookup->ItemIdToName($ledger->type_id);

                    //Get the character name from the lookup helper
                    $charInfo = $lookup->GetCharacterInfo($ledger->character_id);
                    //Get the corporation info from the lookup helper
                    $corpInfo = $lookup->GetCorporationInfo($charInfo->corporation_id);
                    //Get the recorded corporation information
                    $recordedCorpInfo = $lookup->GetCorporationInfo($ledger->recorded_corporation_id);

                    $entries[] = [
                        'corporation_id' => $corpInfo->corporation_id,
                        'corporation_name' => $corpInfo->name,
                        'character_id' => $ledger->character_id,
                        'character_name' => $charInfo->name,
                        'observer_id' => $observer->observer_id,
                        'observer_name' => $observer->observer_name,
                        'type_id' => $ledger->type_id,
                        'ore' => $ore,
                        'quantity' => $ledger->quantity,
                        'recorded_corporation_id' => $ledger->recorded_corporation_id,
                        'recorded_corporation_name' => $recordedCorpInfo->name,
                        'last_updated' => $ledger->last_updated,
                        'created_at' => $ledger->last_updated . ' 23:59:59',
                        'updated_at' => $ledger->last_updated . ' 23:59:59',
                    ];
                }

                //Insert or ignore each entry into the database
                CorpMoonLedger::insertOrIgnore($entries);

                Log::info('FetchMoonLedgerJob inserted up to ' . count($entries) . 'into the database.');
            }
        }
    }
}
