<?php

namespace App\Jobs;

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
use App\Models\Jobs\JobProcessAsset;
use App\Models\Jobs\JobStatus;
use App\Models\Stock\Asset;

class ProcessAssetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 300;

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

    protected $location_array = [
        'CorpDeliveres',
        'CorpSAG1',
        'CorpSAG3',
        'CorpSAG4',
        'CorpSAG5',
        'CorpSAG6',
        'CorpSAG7',
        'StructureFuel',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessAsset $jpa)
    {
        $this->charId = $jpa->charId;
        $this->corpId = $jpa->corpId;
        $this->page = $jpa->page;

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * The job's task is to get all fo the information for all of the assets in
     * a structure and store them in the database.  This task can take a few seconds
     * therefore we want the Horizon job queue to take care of the request rather
     * than the cronjob.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the asset helper
        $aHelper = new AssetHelper($this->charId, $this->corpId);

        //Get a page of assets
        $assets = $aHelper->GetAssetsByPage($this->page);

        //Cycle through the assets, and attmept to store them.
        foreach($assets as $asset) {
            //Attempt to store the asset
            $aHelper->StoreNewAsset($asset);
        }

        //Purge Stale Data
        //$aHelper->PurgeStaleData();
    }
}
