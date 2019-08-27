<?php

namespace App\Jobs;

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
use App\Models\Jobs\JobProcessCorpJournal;
use App\Models\Jobs\JobStatus;

class ProcessCorpJournalJob implements ShouldQueue
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
    private $corpId;
    private $page;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessCorpJournal $pcj)
    {
        $this->division = $pcj->division;
        $this->charId = $pcj->charId;
        $this->corpId = $pcj->corpId;
        $this->page = $pcj->page;

        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Delcare the class variable we need
        $finance = new FinanceHelper();

        $finance->GetCorpWalletJournalPage($this->division, $this->charId, $this->corpId, $this->page);

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
        Log::critical($exception);
    }
}
