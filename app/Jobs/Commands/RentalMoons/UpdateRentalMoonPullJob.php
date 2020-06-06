<?php

namespace App\Jobs\Commands\RentalMoons;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Carbon\Carbon;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\MoonRentals\AllianceRentalMoon;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

class UpdateRentalMoonPullJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set the queue connection
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Setup the configuration variable for ESI
        $config = config('esi');
        //Setup the esi helper variable
        $esiHelper = new Esi;
        
        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            //Send a mail to the holding toon to update the esi scopes
            return null;
        }

        //Is this valid?
        AllianceRentalMoon::update([
            'next_moon_pull' => null,
        ]);

        //Reset all table entries to clear out the next moon pull entry
        $allMoons = AllianceRentalMoon::all();
        //Cycle through all of the moons, and make the next moon pull null
        foreach($allMoons as $moon) {
            AllianceRentalMoon::where([
                'system' => $moon->system,
                'planet' => $moon->planet,
                'moon' => $moon->moon,
            ])->update([
                'next_moon_pull' => null,
            ]);
        }

        //Get the refresh token
        $token = $esiHelper->GetRefreshToken($config['primary']);
        //Setup the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        try {
            $responses = $esi->invoke('get', '/corporation/{corporation_id}/mining/extractions/', [
                'corporation_id' => 98287666,
            ]);
        } catch(RequestExceptionFailed $e) {
            return null;
        }

        foreach($response as $response) {
            //Get whether the structure is being used by the alliance or not.
            $usage = AllianceRentalMoon::where([
                'structure_id' => $response->structure_id,
                'rental_type' => 'Alliance',
            ])->count();

            if($usage > 0) {
                AllianceRentalMoon::where([
                    'structure_id' => $response->structure_id,
                ])->update([
                    'next_moon_pull' => $response->chunk_arrival_time,
                ]);
            }
        }
    }
}
