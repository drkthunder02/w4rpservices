<?php

namespace App\Jobs\Commands\MiningTaxes;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//Application Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;
use App\Library\Helpers\StructureHelper;

//Models
use App\Models\MiningTax\Observer;

class FetchMiningTaxesObservers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 1800;

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
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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

        $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
            'corporation_id' => $char->corporation_id,
        ]);

        $resp = json_decode($response->raw, false);

        //Run through the mining observers, and add them to the database
        foreach($resp as $observer) {
            if($observer->observer_id > 1030000000000) {
                $found = Observer::where([
                    'observer_id' => $observer->observer_id,
                ])->count();
    
                if($found > 0) {
                    Observer::where([
                        'observer_id' => $observer->observer_id,
                    ])->update([
                        'observer_id' => $observer->observer_id,
                        'observer_type' => $observer->observer_type,
                        'last_updated' => $observer->last_updated,
                    ]);
                } else {
                    $newObs = new Observer;
                    $newObs->observer_id = $observer->observer_id;
                    $newObs->observer_type = $observer->observer_type;
                    $newObs->last_updated = $observer->last_updated;
                    $newObs->save();
                }
            }            
        }

        /**
         * Cleanup stale data that hasn't been updated in at least 1 week.
         */
        $date = Carbon::now()->subDays(7);
        Observer::where('last_updated', '<', $date)->delete();

        //Set the end time for debugging and printint out to the screen
        $endTime = time();
        printf("Time to complete: " . ($endTime - $startTime) . "\n\r");

        //Return 0 saying everything is fine
        return 0;
    }

    /**
     * The job failed to process
     * @param Exception $exception
     * @return void
     */
    public function failed($exception) {
        if(!exception instanceof RequestFailedException) {
            //If not a failure due to ESI, then log it.  Otherwise,
            //deduce why the exception occurred.
            Log::critical($exception);
        }

        if ((is_object($exception->getEsiResponse()) && (stristr($exception->getEsiResponse()->error, 'Too many errors') || stristr($exception->getEsiResponse()->error, 'This software has exceeded the error limit for ESI'))) || 
           (is_string($exception->getEsiResponse()) && (stristr($exception->getEsiResponse(), 'Too many errors') || stristr($exception->getEsiResponse(), 'This software has exceeded the error limit for ESI')))) {
            
            //We have hit the error rate limiter, wait 120 seconds before releasing the job back into the queue.
            Log::info('FetchMiningTaxesObservers has hit the error rate limiter.  Releasing the job back into the wild in 2 minutes.');
            $this->release(120);
        }  else {
            $errorCode = $exception->getEsiResponse()->getErrorCode();

            switch($errorCode) {
                case 400:  //Bad Request
                    Log::critical("Bad request has occurred in FetchMiningTaxesObservers.  Job has been discarded");
                    break;
                case 401:  //Unauthorized Request
                    Log::critical("Unauthorized request has occurred in FetchMiningTaxesObservers at " . Carbon::now()->toDateTimeString() . ".\r\nCancelling the job.");
                    break;
                case 403:  //Forbidden
                    Log::critical("FetchMiningTaxesObservers has incurred a forbidden error.  Cancelling the job.");
                    break;
                case 420:  //Error Limited
                    Log::warning("Error rate limit occurred in FetchMiningTaxesObservers.  Restarting job in 120 seconds.");
                    $this->release(120);
                    break;
                case 500:  //Internal Server Error
                    Log::critical("Internal Server Error for ESI in FetchMiningTaxesObservers.  Attempting a restart in 120 seconds.");
                    $this->release(120);
                    break;
                case 503:  //Service Unavailable
                    Log::critical("Service Unavailabe for ESI in FetchMiningTaxesObservers.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 504:  //Gateway Timeout
                    Log::critical("Gateway timeout in FetchMiningTaxesObservers.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 201:   //Good response code
                    $this->delete();
                    break;
                //If no code is given, then log and break out of switch.
                default:
                    Log::warning("No response code received from esi call in FetchMiningTaxesObservers.\r\n");
                    $this->delete();
                    break;
            }
        }
    }

    /**
     * Set the tags for Horizon
     * 
     * @var array
     */
    public function tags() {
        return ['FetchMiningObservers'];
    }
}
