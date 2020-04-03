<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;

//Jobs
use App\Jobs\ProcessWalletJournalJob;

//Models
use App\Models\Jobs\JobProcessWalletJournal;

class HoldingFinancesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:HoldingJournal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the holding corps finances.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Create the command helper container
        $task = new CommandHelper('HoldingFinances');

        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup the Finances container
        $finance = new FinanceHelper();

        //Get the esi configuration
        $config = config('esi');

        //Get the total pages for the journal for the holding corporation
        $pages = $finance->GetJournalPageCount(1, $config['primary']);

        //Dispatch a single job for each page to process
        for($i = 1; $i <= $pages; $i++) {
            $job = new JobProcessWalletJournal;
            $job->division = 1;
            $job->charId = $config['primary'];
            $job->page = $i;
            ProcessWalletJournalJob::dispatch($job)->onQueue('journal');
        }

        //Get the total pages for the journal for the sov bills from the holding corporation
        $pages = $finance->GetJournalPageCount(6, $config['primary']);

        //Dispatch a job for each page to process
        for($i = 1; $i <= $pages; $i++) {
            $job = new JobProcessWalletJournal;
            $job->division = 6;
            $job->charId = $config['primary'];
            $job->page = $i;
            ProcessWalletJournalJob::dispatch($job)->onQueue('journal');
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
