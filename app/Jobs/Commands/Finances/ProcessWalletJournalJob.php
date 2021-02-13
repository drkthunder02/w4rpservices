<?php

namespace App\Jobs\Commands\Finances;

//Internal Libraries
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library
use App\Library\Finances\Helper\FinanceHelper;
use App\Jobs\Library\JobHelper;

//App Models
use App\Models\Jobs\JobProcessWalletJournal;
use App\Models\Jobs\JobStatus;

class ProcessWalletJournalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    public $tries = 3;

    private $division;
    private $charId;
    private $page;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($division, $charId) {
        $this->division = $division;
        $this->charId = $charId;

        $this->connection = 'redis';
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

        $finance->GetWalletJournal($this->division, $this->charId);

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
        Log::critical($exception);
    }
}
