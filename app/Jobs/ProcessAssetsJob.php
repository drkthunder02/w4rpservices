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

//App Models
use App\Models\Jobs\JobProcessAssets;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessAssets $jpa)
    {
        $this->charId = $jpa->charId;
        $this->corpId = $jpa->corpId;
        $this->page = $jpa->page;
        $this->esi = $jpa->esi;

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
        //Get the pages of the asset list
        $assets = $this->GePageOfAssets();

        foreach($assets as $asset) {
            
        }
    }

    private function GetPageOfAssets() {
        try {
            $assets = $this->esi->page($this->page)
                                ->invoke('get', '/corporations/{corporation_id}/assets/', [
                                    'corporation_id' => $this->corpId,
                                ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get page of Assets from ESI.");
            $assets = null;
        }

        return $assets;
    }


}
