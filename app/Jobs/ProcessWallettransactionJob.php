<?php

namespace App\Jobs;

//Internal Libraries
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

//App Library
use App\Library\Finances\Helper\FinanceHelper;
use App\Jobs\Library;

//App Models
use App\Models\Jobs\JobProcessWalletTransaction;
use App\Models\Jobs\JobError;
use App\Models\Jobs\JobStatus;

class ProcessWalletTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 300;

    public $tries = 3;

    private $division;
    private $charId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessWalletTransaction $pwt)
    {
        $this->division = $pwt->division;
        $this->charId = $pwt->charId;
        
        $this->connection = 'database';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $status = new JobHelper('Process Wallet Transaction');
        $status->SetStartStatus();

        //Declare the class variables
        $finance = new FinanceHelper();

        $finance->GetWalletTransaction($this->division, $this->charId);

        $status->SetStopStatus();

        //After the job is completed, delete the job
        $this->delete();
    }

    /**
     * The job failed to process
     * 
     * @param Exception $exception
     * @return void
     */
    public function failed($exception) {
        dd($exception);
    }
}
