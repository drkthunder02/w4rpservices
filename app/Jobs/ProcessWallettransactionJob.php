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

//App Models
use App\Models\Jobs\JobProcessWalletTransaction;

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
    private $page;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessWalletTransaction $pwt)
    {
        $this->division = $pwt->division;
        $this->charId = $pwt->charId;
        $this->page = $pwt->page;

        $this->delay = 10;
        $this->connection = 'database';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the class variables
        $finance = new FinanceHelper();

        $finance->GetWalletTransaction($this->division, $this->charId);

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
        // Send user notification of the failure, etc.
        dd($exception);
    }
}
