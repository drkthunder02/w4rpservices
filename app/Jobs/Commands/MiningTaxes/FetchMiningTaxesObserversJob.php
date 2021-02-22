<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;

//App Models
use App\Models\MiningTax\Observer;

class FetchMiningTaxesObserversJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Number of job retries
     * 
     * @var int
     */
    public $tries = 3;

    /**
     * Job Variables
     */
    private $charId;
    private $corpId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId = null, $corpId = null)
    {
        $this->charId = $charId;
        $this->corpId = $corpId;

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * The job's duty is to get all of the corporation's moon mining observers,
     * then store them in the database.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Get the configuration from the main site
        $config = config('esi');

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1') || !$esiHelper->HaveEsiScope($this->charId, 'esi-universe.read_structures.v1')) {
            Log::critical('Esi scopes were not found for FetchMiningTaxesObserversJob.');
            return;
        }

        //Get the refresh token for the character
        $refreshToken = $esiHelper->GetRefreshToken($this->charId);
        //Get the esi variable
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        try {
            $response = $esi->invoke('get', '/corporations/{corporation_id}/mining/observers', [
                'corporation_id' => $this->corpId,
            ]);
        } catch(RequestFailedException $e) {
            Log::critical("Failed to get moon observers in FetchMiningTaxesObservers");
            Log::critical($e);
        }

        $resp = json_decode($response, false);

        //Run through the mining observers, and add them to the database
        foreach($resp as $observer) {

            Observer::updateOrInsert([
                'observer_id' => $observer->observer_id,
            ], [
                'observer_id' => $observer->observer_id,
                'observer_type' => $observer->observer_type,
                'last_updated' => $observer->last_updated,
            ]);
        }

        /**
         * Cleanup stale data that hasn't been updated in at least 1 week.
         */
        $date = Carbon::now()->subDays(60);
        Observer::where('updated_at', '<', $date)->delete();
    }
}
