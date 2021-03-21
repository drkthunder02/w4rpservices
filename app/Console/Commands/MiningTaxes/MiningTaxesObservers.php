<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;
use Commands\Library\CommandHelper;
use App\Library\Helpers\StructureHelper;

//App Models
use App\Models\MiningTax\Observer;

//Jobs
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesObserversJob;

class MiningTaxesObservers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get mining tax observers.';

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
        $task = new CommandHelper('MiningTaxesObservers');
        //Set the task as started
        $task->SetStartStatus();
        $startTime = time();

        //Declare variables
        $config = config('esi');
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            Log::critical('Esi scopes were not found for FetchMiningTaxesObserversJob.');
            print("Esi scopes not found.");
            return;
        }

        $char = $lookup->GetCharacterInfo($config['primary']);

        //Get the refresh token for the character
        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        //Get the esi variable
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        try {
            $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
                'corporation_id' => $char->corporation_id,
            ]);
        } catch(RequestFailedException $e) {
            Log::critical("Failed to get moon observers in FetchMiningTaxesObservers");
            Log::critical($e);
            dd($e);
        }
        
        $resp = json_decode($response->raw, false);

        //Run through the mining observers, and add them to the database
        foreach($resp as $observer) {
            //Declare the structure helper
            $sHelper = new StructureHelper($config['primary'], $config['corporation']);

            //Get the structure name from the universe endpoint to store in the database
            $observerName = $sHelper->GetStructureName($observer->observer_id);
            
            $found = Observer::where([
                'observer_id' => $observer->observer_id,
            ])->count();

            if($found > 0) {
                Observer::where([
                    'observer_id' => $observer->observer_id,
                ])->update([
                    'observer_id' => $observer->observer_id,
                    'observer_type' => $observer->observer_type,
                    'observer_name' => (string)$observerName,
                    'last_updated' => $observer->last_updated,
                ]);
            } else {
                $newObs = new Observer;
                $newObs->observer_id = $observer->observer_id;
                $newObs->observer_type = $observer->observer_type;
                $newObs->observer_name = (string)$observerName;
                $newObs->last_updated = $observer->last_updated;
                $newObs->save();
            }
        }

        /**
         * Cleanup stale data that hasn't been updated in at least 1 week.
         */
        $date = Carbon::now()->subDays(7);
        Observer::where('last_updated', '<', $date)->delete();

        //Set the task as completed
        $task->SetStopStatus();

        //Set the end time for debugging and printint out to the screen
        $endTime = time();
        printf("Time to complete: " . ($endTime - $startTime) . "\n\r");

        //Return 0 saying everything is fine
        return 0;
    }
}
