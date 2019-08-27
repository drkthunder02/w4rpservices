<?php

namespace App\Jobs;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

//App Library
use App\Library\Logistics\ContractsHelper;

//App Models
use App\Models\Jobs\JobProcessContracts;
use App\Models\Job\JobStatus;

class ProcessEveContractsJob implements ShouldQueue
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessContracts $jpc)
    {
        $this->charId = $jpc->charId;
        $this->corpId = $jpc->corpId;
        $this->page = $jpc->page;

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the contracts helper
        $cHelper = new EveContractsHelper($this->charId, $this->corpId, $this->page);

        $contracts = $cHelper->GetContractsByPage();

        foreach($contracts as $contract) {
            $cHelper->ProcessContract($contract);
        }

        //After the job is completed, delete the job
        $this->delete();
    }
}
