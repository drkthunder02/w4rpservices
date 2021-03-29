<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;
use App\Library\Moons\MoonCalc;

//Jobs
use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesLedgersJob;

//App Models
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Ledger;
use App\Models\Moon\MineralPrice;
use App\Models\Moon\ItemComposition;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

class FetchMiningTaxesLedgersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The queue connection that should handle the job
     */
    public $connection = 'queue';

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
    private $observerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId, $corpId, $observerId)
    {
        //Set the connection for the job
        //$this->connection = 'redis';

        //Import the variables from the calling function
        $this->charId = $charId;
        $this->corpId = $corpId;
        $this->observerId = $observerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $lookup = new LookupHelper;
        $mHelper = new MoonCalc;
        $esiHelper = new Esi;
        $pageFailed = false;
        $config = config('esi');

        //Check for the correct scope
        if(!$esiHelper->haveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1')) {
            Log::critical('Character: ' . $this->charId . ' did not have the correct esi scope in FetchMiningTaxesLedgersJob.');
            return null;
        }

        //Get the esi token in order to pull data from esi
        $refreshToken = $esiHelper->GetRefreshToken($this->charId);
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Set the current page
        $currentPage = 1;
        $totalPages = 1;

        //Setup a do-while loop to sort through the ledgers by pages
        do {
            /**
             * During the course of the operation, we want to ensure our token stays valid.
             * If the token, expires, then we want to refresh the token through the esi helper
             * library functionality.
             */
            if($esiHelper->TokenExpired($refreshToken)) {
                $refreshToken = $esiHelper->GetRefreshToken($charId);
                $esi = $esiHelper->SetupEsiAuthentication($refreshToken);
            }

            /**
             * Attempt to get the data from the esi api.  If it fails, we skip the page
             */
            try {
                $response = $esi->page($currentPage)
                                ->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                    'corporation_id' => $config['corporation'],
                    'observer_id' => $this->observerId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get the mining ledger in FetchMiningTaxesLedgersCommand for observer id: ' . $this->observerId);
                $pageFailed = true;
            }

            /**
             * If the current page is the first one and the page didn't fail, then update the total pages.
             * If the first page failed, just return as we aren't going to be able to get the total amount of data needed.
             */
            if($currentPage == 1 && $pageFailed == false) {
                $totalPages = $response->pages;
            } else if($currentPage == 1 && $pageFailed == true) {
                return null;
            }

            if($pageFailed == true) {
                //If the page failed, then reset the variable, and skip the current iteration
                //of creating the jobs.
                $pageFailed = false;
            } else {
                //Decode the json response from the ledgers
                $ledgers = json_decode($response->raw);

                //Dispatch jobs to process each of the mining ledger entries
                foreach($ledgers as $ledger) {
                    ProcessMiningTaxesLedgersJob::dispatch($ledger, $this->observerId)->onQueue('miningtaxes');
                }                
            }

            //Increment the current pages
            $currentPage++;

        } while($currentPage <= $totalPages);
    }
}
