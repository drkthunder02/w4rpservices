<?php

namespace App\Jobs;

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
use App\Models\CorpMoonLedger;
use App\Models\CorpMoonObserver;


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
        $structure = new StructureHelper;
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

        //Get the character data from the lookup table
        $character = $lookup->GetCharacterInfo($this->charId);

        //Get the corporation data from the lookup table
        $corporation = $lookup->GetCorporationInfo($character->corporation_id);

        //Get the moon observers from the database
        $observers = CorpMoonObserver::where([
            'corporation_id' => $character->corporation_id,
        ])->get();

        foreach($observers as $observer) {
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

                    
                }
            }
        }
    }
}
