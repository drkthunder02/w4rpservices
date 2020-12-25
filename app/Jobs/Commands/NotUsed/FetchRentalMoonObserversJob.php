<?php

namespace App\Jobs\Commands\NotUsed;

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
use App\Models\Moon\RentalMoonObserver;

class FetchRentalMoonObserversJob implements ShouldQueue
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
        //Declare some variables
        $lookup = new LookupHelper;
        $esi = new Esi;
        $obss = array();

        //Get the configuration for the main site
        $config = config('esi');

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1') || !$esiHelper->HaveEsiScope($config['primary'], 'esi-universe.read_structures.v1')) {
            Log::critical('The primary character does not have the necessary scopes for FetchRentalMoonObservers.');
            return;
        }

        //Get the refresh token for spatial forces
        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo($config['primary']);

        //Get the mining observers for spatial forces from esi
        try {
            $responses = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
                'corporation_id' => $character->corporation_id,
            ]);
        } catch(RequestFailedException $e) {
            Log::critical('RentalMoonObservers failed to get the moon observers for Spatial Forces.');
        }

        //Run through the mining observers, and add them to the database as needed
        foreach($responses as $observer) {
            //Populate the array with the data, so we can do an insert or ignore after the foreach loop is completed.
            $obss[] = [
                'observer_id' => $observer->observer_id,
                'observer_type' => $observer->observer_type,
                'last_updated' => $esi->DecodeDate($observer->last_updated)
            ];
        }

        RentalMoonObserver::insertOrIgnore($obss);
    }
}
