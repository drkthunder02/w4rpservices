<?php

namespace App\Console\Commands\Moons;

//Internal Library
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;

//Jobs
use App\Jobs\Commands\Moons\FetchMoonLedgerJob;
use App\Jobs\Commands\Moons\FetchMoonObserversJob;

//Library
use Commands\Library\CommandHelper;

//Models
use App\Models\Esi\EsiScope;

class MoonsUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:MoonUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all of the moons registered for observers and ledgers.';

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
        //Declare variables
        $delay = 0;
        $characters = array();

        //Create the new command helper container
        $task = new CommandHelper('MoonsUpdateCommand');
        //Set the task start status
        $task->SetStartStatus();

        //Get all of the characters who have registered structures for moon ledgers
        $miningChars = EsiScope::where([
            'scope' => 'esi-industry.read_corporation_mining.v1',
        ])->get();

        foreach($miningChars as $mChars) {
            $universe = EsiScope::where([
                'character_id' => $mChars->character_id,
                'scope' => 'esi-universe.read_structures.v1',
            ])->first();

            if($universe != null) {
                array_push($characters, $universe->character_id);
            }
        }

        //Cycle through each of the character Ids which have the correct scopes,
        //and dispatch jobs accordingly.
        foreach($characters as $charId) {
            //Fetch all of the corp observers with the job dispatch
            FetchMoonObserverJob::dispatch($charId);
            //Fetch all of the corp ledgers with the job dispatch
            FetchMoonLedgerJob::dispatch($charId);
        }

        

        //Set task done status
        $task->SetStopStatus();
    }
}
