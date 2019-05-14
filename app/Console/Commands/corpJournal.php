<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

//Libraries
use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;

//Jobs
use App\Jobs\ProcessWalletJournalJob;

//Models
use App\Models\Corporation\CorpStructure;
use App\Models\Jobs\JobProcessWalletJournal;

class CorpJournalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:CorpJournal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs the corporation journals and deposit in db.';

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
        $task = new CommandHelper('CorpJournal');

        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup the Finances Container
        $finance = new FinanceHelper();

        //Setup an array to store corporations which have been logged so we don't keep calling the same ones.  We need
        //this step in order to save time during the cronjob.
        $finishedCorps = array();
        $corpCompleted = false;

        //Get the corps with structures logged in the database
        $corps = CorpStructure::select('corporation_id')->groupBy('corporation_id')->get();

        //For all of the corporations, go through each structure and get wallet data
        foreach($corps as $corp) {
            //If the corporation isn't the holding corporation, then process the data.
            //We process holding corporation data elsewhere.
            if($corp->corporation_id != 98287666) {
                $structure = CorpStructure::where(['corporation_id' => $corp->corporation_id])->first();
                $pages = $finance->GetJournalPageCount(1, $structure->character_id);
                for($i = 1; $i <= $pages; $i++) {
                    $job = new JobProcessWalletJournal;
                    $job->division = 1;
                    $job->charId = $structure->character_id;
                    $job->page = $i;
                    ProcessWalletJournalJob::dispatch($job)->onQueue('default');
                }
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
