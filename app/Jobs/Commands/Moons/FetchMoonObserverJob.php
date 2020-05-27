<?php

namespace App\Jobs\Commands\Moons;

//Internal Library
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
use App\Models\Moon\CorpMoonObserver;

class FetchMoonObserverJob implements ShouldQueue
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
     * Private variables
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
        //Declare some variables
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Get the configuration from the main site
        $config = config('esi');

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1') || !$esiHelper->HaveEsiScope($this->charId, 'esi-universe.read_structures.v1')) {
            Log::warning('Esi scopes were not found for Fetch Moon Observers job.');
            return;
        }

        //Get the refresh token for the character
        $refreshToken = $esiHelper->GetRefreshToken($this->charId);
        //Get the esi variable
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //With the lookup helper, get the character information
        $character = $lookup->GetCharacterInfo($this->charId);

        //Get the mining observers for the corporation's from esi
        try {
            $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
                'corporation_id' => $character->corporation_id,
            ]);
        } catch(RequestFailedException $e) {
            Log::critical('FetchMoonObservers failed to get the moon observers for the corporation');
        }

        //Run through the mining observers, and add them to the database as needed
        foreach($response as $observer) {
            CorpMoonObserver::where(['observer_id' => $observer->observer_id])->count();
            //If the observer is not found, then add it to the database
            if($count == 0) {
                $obs = new CorpMoonObserver;
                $obs->observer_id = $observer->observer_id;
                $obs->observer_type = $observer->observer_type;
                $obs->last_updated = $esiHepler->DecodeDate($observer->last_updated);
                $obs->save();
            } else {
                CorpMoonObserver::where([
                    'observer_id' => $observer->observer_id,
                ])->update([
                    'last_updated' => $observer->last_updated,
                ]);
            }
        }
    }
}
