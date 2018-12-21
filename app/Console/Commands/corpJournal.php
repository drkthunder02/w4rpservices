<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use Commands\Library\CommandHelper;
use App\Library\Finances\FinanceHelper;
use App\Library\Esi\Esi;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Corporation\Structure;
use App\Models\ScheduledTask\ScheduleJob;

use Carbon\Carbon;

class CorpJournal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:corpjournal';

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
        $corps = DB::table('CorpStructures')->select('corporation_id')->groupBy('corporation_id')->get();
        foreach($corps as $corp) {
            $this->line('Retrieving Corporation Journals for ' . $corp->corporation_id);
            $charId = DB::table('CorpStructures')->select('character_id')->where(['corporation_id' => $corp->corporation_id])->first();
            $lastPage = $this->GetJournal($charId->character_id);
            $this->line('Last Page recorded was ' . $lastPage);
            $this->line('Received Corporation Journals for ' . $corp->corporation_id);
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }

    private function GetJournal($charId) {
        $finances = new FinanceHelper();
        //Get the master wallet journal for the corporation for the character
        $lastPage = $finances->GetWalletJournal(1, $charId);

        return $lastPage;
    }
}
