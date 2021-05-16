<?php

namespace App\Jobs\Commands\Structures;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Application Library
use App\Library\Esi\Esi;
use Seat\Eseye\Exception\RequestFailedException;
use App\Library\Structures\StructureHelper;

//Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;

class FetchAllianceStructures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $config = config('esi');
        $corpId = 98287666;

        $esiHelper = new Esi;
        $structureScope = $esiHelper->HaveEsiScope($config['primary'], 'esi-universe.read_structures.v1');
        $corpStructureScope = $esiHelper->HaveEsiScope($config['primary'], 'esi-corporations.read_structures.v1');

        //Check scopes
        if($structureScope == false || $corpStructureScope == false) {
            if($structureScope == false) {
                Log::critical("Scope check for esi-universe.read_structures.v1 has failed.");
            }
            if($corpStructureScope == false) {
                Log::critical("Scope check for esi-corporations.read_structures.v1 has failed.");
            }

            return -1;
        }

        //Get the refresh token from the database
        $token = $esiHelper->GetRefreshToken($config['primary']);
        //Create the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Set the current page
        $currentPage = 1;
        //Set our default pages
        $totalPages = 1;

        do {
            //Attempt to get the entire page worth of structures
            $structures = $esi->page($currentPage)
                              ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                  'corporation_id' => $corpId,
                              ]);

            //If on the first page, then update the total number of pages
            if($currentPage == 1) {
                $totalPages = $structures->pages;
            }

            //For each asset retrieved, let's process it.
            foreach($structures as $s) {
                ProcessAllianceStructures::dispatch($s)->onQueue('default');
            }

            //Increment the current page
            $currentPage++;
        } while($currentPage <= $totalPages);

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
    
    public function tags() {
        return ['FetchAllianceStructures', 'AllianceStructures', 'Structures'];
    }
}
