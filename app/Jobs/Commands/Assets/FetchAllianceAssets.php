<?php

namespace App\Jobs\Commands\Assets;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Application Library
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\Structure\Asset;

class FetchAllianceAssets implements ShouldQueue
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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set the connection for the job
        $this->connection = 'redis';
        $this->onQueue('assets');
        
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

        //Get the refresh token from the database
        $token = $esiHelper->GetRefreshToken($config['primary']);
        //Create the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Check the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-assets.read_corporation_assets.v1')) {
            Log::critical("Scope check failed in FetchAllianceAssets for esi-assets.read_corporation_assets.v1");
        }

        //Set the current page
        $currentPage = 1;
        //Set our default pages
        $totalPages = 1;

        do {
            if($esiHelper->TokenExpired($token)) {
                $token = $esiHelper->GetRefreshToken($config['primary']);
                $esi = $esiHelper->SetupAuthenticationToken($token);
            }            

            //Attempt to get the assets
            $assets = $esi->page($currentPage)
                          ->invoke('get', '/corporations/{corporation_id}/assets/', [
                              'corporation_id' => $corpId,
                          ]);
            
            //If on the first page, then update the total number of pages
            if($currentPage == 1) {
                $totalPages = $assets->pages;
            }

            //For each asset retrieved, let's process it.
            foreach($assets as $a) {
                ProcessAllianceAssets::dispatch($a);
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
            Log::info('FetchAllianceAssets has hit the error rate limiter.  Releasing the job back into the wild in 2 minutes.');
            $this->release(120);
        }  else {
            $errorCode = $exception->getEsiResponse()->getErrorCode();

            switch($errorCode) {
                case 400:  //Bad Request
                    Log::critical("Bad request has occurred in FetchAllianceAssets.  Job has been discarded");
                    break;
                case 401:  //Unauthorized Request
                    Log::critical("Unauthorized request has occurred in FetchAllianceAssets at " . Carbon::now()->toDateTimeString() . ".\r\nCancelling the job.");
                    $this->delete();
                    break;
                case 403:  //Forbidden
                    Log::critical("FetchAllianceAssets has incurred a forbidden error.  Cancelling the job.");
                    $this->delete();
                    break;
                case 420:  //Error Limited
                    Log::warning("Error rate limit occurred in FetchAllianceAssets.  Restarting job in 120 seconds.");
                    $this->release(120);
                    break;
                case 500:  //Internal Server Error
                    Log::critical("Internal Server Error for ESI in FetchAllianceAssets.  Attempting a restart in 120 seconds.");
                    $this->release(120);
                    break;
                case 503:  //Service Unavailable
                    Log::critical("Service Unavailabe for ESI in FetchAllianceAssets.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 504:  //Gateway Timeout
                    Log::critical("Gateway timeout in FetchAllianceAssets.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 201:
                    //Good response code
                    $this->delete();
                    break;
                //If no code is given, then log and break out of switch.
                default:
                    Log::warning("No response code received from esi call in FetchAllianceAssets.\r\n");
                    $this->delete();
                    break;
            }
        }
    }

    /**
     * Tags for jobs
     * 
     * @var array
     */
    public function tags() {
        return ['FetchAllianceAssets', 'AllianceStructures', 'Assets'];
    }
}
