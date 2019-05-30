<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

//Job
use App\Jobs\ProcessStructureJob;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Commands\Library\CommandHelper;

//Models
use App\Models\Jobs\JobProcessStructure;

class GetStructuresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:GetStructures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the list of structures ';

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
        $task = new CommandHelper('GetStructures');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Declare some variables
        $charId = 93738849;
        $corpId = 98287666;

        //ESI Scope Check
        $esiHelper = new Esi();
        $structureScope = $esiHelper->HaveEsiScope($charId, 'esi-universe.read_structures.v1');
        $corpStructureScope = $esiHelper->HaveEsiScope($charId, 'esi-corporations.read_structures.v1');

        if($structureScope == false || $corpStructureScope == false) {
            if($structureScope == false) {
                Log::critical("Scope check for esi-universe.read_structures.v1 has failed.");
            }
            if($corpStructureScope == false) {
                Log::critical("Scope check for esi-corporations.read_structures.v1 has failed.");
            }
            return null;
        }

        //Setup the esi authentication container
        $config = config('esi');
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => 93738489])->get(['refresh_token']);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        $esi = new Eseye($authentication);

        //Set the current page
        $currentPage = 1;
        //Set our default total pages, and we will refresh this later
        $totalPages = 1;

        try {
            $structures = $esi->page($currentPage)
                              ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                'corporation_id' => $corpId,
                                ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get structure list.");
            return null;
        }

        for($i  = 1; $i <= $structures->pages; $i++) {
            $job = new JobProcessStructure;
            $job->charId = $charId;
            $job->corpId = $corpId;
            $job->page = $i;
            $job->esi = $esi;
            ProcessStructureJob::dispatch($job)->onQueue('default');
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
