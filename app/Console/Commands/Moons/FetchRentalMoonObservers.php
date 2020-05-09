<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//App Models
use App\Models\Moon\RentalMoon;
use App\Models\Moon\RentalMoonObserver;

class FetchRentalMoonObservers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:FetchRentalMoonObservers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the rental moon observers';

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
        $task = new CommandHelper('RentalMoonObservers');
        //Add the entry into the jobs table saying the job has started
        $task->SetStartStatus();

        //Declare some variables
        $lookup = new LookupHelper;
        $esi = new Esi;

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
            RentalMoonObserver::where(['observer_id' => $observer->observer_id])->count();
            //If the observer is not found, then add it to the database, otherwise we just need to update the last updated portion
            if($count == 0) {
                $obs = new RentalMoonObserver;
                $obs->observer_id = $observer->observer_id;
                $obs->observer_type = $observer->observer_type;
                $obs->last_updated = $esi->DecodeDate($observer->last_updated);
                $obs->save();
            } else {
                RentalMoonObserver::where([
                    'observer_id' => $observer->observer_id,
                ])->update([
                    'last_updated' => $esi->DecodeDate($observer->last_updated),
                ]);
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
