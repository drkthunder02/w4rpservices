<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use Commands\Library\CommandHelper;

use App\Library\Finances;
use App\Library\Esi;

use App\Models\EsiScope;
use App\Models\EsiToken;
use App\Models\Structure;
use App\Models\ScheduleJob;

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
        $finance = new Finances();
        //Setup an array to store corporations which have been logged so we don't keep calling the same ones.  We need
        //this step in order to save time during the cronjob.
        $finishedCorps = array();
        $corpCompleted = false;
        //Get the corps with structures logged in the database
        $structures = DB::table('CorpStructures')->get();
        //For each structure get the corp journals from the corporation owning the structure
        //After getting the corp journal for the corporation, let's not do the corporation again
        foreach($structures as $structure) {
            foreach($finishedCorps as $finished) {
                if($finished == $structure->corporation_id) {
                    $corpCompleted = true;
                    break;
                } else {
                    //If the corp wasn't completed yet ensure the variable is false
                    //If the corp was completed, the variable will be true and this else
                    //will be skipped
                    $corpCompleted = false;
                }
            }
            //If we didn't find the corporation was already done, then complete it.
            if($corpCompleted === false) {
                 //$this->line('Getting corp journal');
                $this->GetJournal($structure->character_id);
                $finishedCorps[sizeof($finishedCorps)] = $structure->corporation_id;
                //After the corporation has been done set the variable back to false
                $corpCompleted = false;
            }
           
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }

    private function GetJournal($charId) {
        $finances = new Finances();
        //Get the master wallet journal for the corporation for the character
        $finances->GetWalletJournal(1, $charId);
    }
}
