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
use App\Models\Jobs\JobProcessWalletJournal as JobModel;

class ProcessWalletJournalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 600;

    /**
     * Delay time for job
     * 
     * @var int
     */
    public $delay = 15;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobModel $pwj) {
        $this->pwj = $pwj;
    }

    /**
     * Execute the job.
     * Utilized by using ProcessWalletJournalJob::dispatch()
     * The model is passed into the dispatch function, then added to the queue
     * for processing.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the class variable we need
        $finance = new FinanceHelper();

        $finance->GetWalletJournalPage($pwj->division, $pwj->charId, $pwj->page);

        //After the job is completed, delete the job
        $this->pwj->delete();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed($exception)
    {
        // Send user notification of failure, etc...
        dd($exception);
    }
}
