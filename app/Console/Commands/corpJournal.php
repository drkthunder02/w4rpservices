<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;
use App\Library\Esi\Esi;

use App\Models\Corporation\CorpStructure;

use Carbon\Carbon;

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
        foreach($corps as $corp) {
            $charId = CorpStructure::where(['corporation_id' => $corp->corporation_id])->first();
            $finance->GetWalletJournal(1, $charId->character_id);
        }

        $finance->GetWalletJournal(3, 94415555);

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
