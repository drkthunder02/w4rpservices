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
    public function __construct(JobModel $pwj) {
        dd($pwj);

        $this->division = $pwj->division;
        $this->charId = $pwj->charId;
        $this->page = $pwj->page;

        $this->delay = 15;
        $this->connection = 'database';
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

        $finance->GetWalletJournalPage($this->division, $this->charId, $this->page);

        //After the job is completed, delete the job
        $this->delete();
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
