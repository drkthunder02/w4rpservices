<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


//App Library
use App\Library\Structures\StructureHelper;

//App Models
use App\Models\Jobs\JobProcessStructure;
use App\Models\Jobs\JobStatus;

class ProcessStructureJob implements ShouldQueue
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
    private $structure;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessStructure $jps)
    {
        $this->charId = $jps->charId;
        $this->corpId = $jps->corpId;
        $this->structure = $jps->structure;

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * The job's task is to get all of the information for a particular structure
     * and store it in the database.  This task can take a few seconds because of the ESI
     * calls required to store the information.  We leave this type of job up to the queue
     * in order to take the load off of the cron job.
     *
     * @return void
     */
    public function handle()
    {
        $sHelper = new StructureHelper($this->charId, $this->corpId);

        $sHelper->ProcessStructure($this->structure);

        //After the job is completed, delete the job
        $this->delete();
    }
}
