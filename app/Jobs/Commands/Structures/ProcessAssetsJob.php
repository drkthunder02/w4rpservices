<?php

namespace App\Jobs\Commands\Structures;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library
use App\Library\Structures\StructureHelper;
use App\Jobs\Library\JobHelper;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Assets\AssetHelper;

//App Models
use App\Models\Jobs\JobStatus;
use App\Models\Structure\Asset;

class ProcessAssetsJob implements ShouldQueue
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
     */
    public $tries = 3;

    /**
     * Job Variables
     */
    private $charId;
    private $corpId;
    private $page;
    private $esi;
    private $currentPage;
    private $totalPages;
    private $config;

    protected $location_array = [
        'StructureFuel',
        'FighterBay',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId, $corpId, $page)
    {
        //Set the connection for the job
        $this->connection = 'redis';

        $this->charId = $charId;
        $this->corpId = $corpId;
        $this->currentPage = 1;
        $this->totalPages = 1;

        $this->config = config('esi');
    }

    /**
     * Execute the job.
     * The job's task is to get all of the information for all of the assets in
     * a structure and store them in the database.  This task can take a few seconds
     * therefore we want the Horizon job queue to take care of the request rather
     * than the cronjob.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $esiHelper = new Esi;
        $aHelper = new AssetHelper($this->charId, $this->corpId);

        //ESI Scope Check
        $assetScope = $esiHelper->HaveEsiScope($this->config['primary'], 'esi-assets.read_corporation_assets.v1');

        if($assetScope == false) {
            Log::critical("Scope check for esi-assets.read_corporations_assets.v1 has failed in ProcessAssetsJob");
            return null;
        }

        //Truncate the Asset data from the table

        //Get the refresh token from the database
        $token = $esiHelper->GetRefreshToken($this->charId);
        //Create the authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        try {
            $assets = $esi->page($this->currentPage)
                          ->invoke('get', '/corporations/{corporation_id}/assets/', [
                                'corporation_id' => $this->corpId,
                          ]);
        } catch(RequestFailedException $e) {
            Log::critical("Failed to get asset list in ProcessAssetsJob");
            return null;
        }

        //Set the total number of pages
        $this->totalPages = $assets->pages;

        //Do this while the total pages is not completed
        do {
            if($currentPage > 1) {
                try {
                    $assets = $esi->page($this->currentPage)
                              ->invoke('get', '/corporations/{corporation_id}/assets/', [
                                  'corporation_id' => $this->corpId,
                              ]);
                } catch(RequestFailedException $e) {
                    Log::critical("Failed to get asset list on page " . $this->currentPage . " in ProcessAssetsJob");
                    return null;
                }
            }

            //Cycle through the assets, and attempt to store them.
            foreach($assets as $asset) {
                //if the asset is in one of the locations we want, then store
                //or update the asset
                if(in_array($asset->location_flag, $this->location_array)) {
                    //Attempt to store the asset
                    $aHelper->StoreNewAsset($asset);
                }
            }

            //Increment the current page before doing the loop again
            $this->currentPage++;
        } while($this->currentPage <= $this->totalPages);

        //Once all the data is stored and updated, purge stale data
        $aHelper->PurgeStaleData();
    }
}
