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

class SovBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:SovBills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the holding corps sov bills from wallet 6.';

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
        $task = new CommandHelper('SovBills');

        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup the Finances container
        $finance = new FinanceHelper();

        //Get the esi configuration
        $config = config('esi');

        //Get the total pages for the journal for the sov bills from the holding corporation
        $pages = $finance->GetJournalPageCount(6, $config['primary']);

        //Dispatch a job for each page to process
        //for($i = 1; $i <= $pages; $i++) {
        //    $job = new JobProcessWalletJournal;
        //    $job->division = 6;
        //    $job->charId = $config['primary'];
        //    $job->page = $i;
        //    ProcessWalletJournalJob::dispatch($job)->onQueue('journal');
        //}

        //Try to figure it out from the command itself.
        for($i = 1; $i <= $pages; $i++) {
            printf("Getting page: " . $i . "\n");
            $finance->GetWalletJournalPage(6, $config['primary'], $i);
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
