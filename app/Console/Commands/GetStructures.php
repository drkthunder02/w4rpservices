<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

//Job


//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class GetStructures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GetStructures';

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

        //ESI Scope Check
        $esiHelper = new Esi();
        $structureScope = $esiHelper->HaveEsiScope($charId, 'esi-universe.read_structures.v1');
        $corpStructureScope = $esiHelper->HaveEsiScope($charId, 'esi-corporations.read_structures.v1');

        if($structureScope == false || $corpStructureScope == false) {
            Log::critical("Scope check for esi failed.");
            return null;
        }

        //Setup the esi authentication container
        $config = config('esi');
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => $charId])->get(['refresh_token']);
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

        do {
            try {
                $structures = $esi->page($currentPage)
                                  ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                    'corporation_id' => 98287666,
                                    ]);
            } catch (RequestFailedException $e) {
                Log::critical("Failed to get structure list.");
                return null;
            }

            //Set the total pages we need to cycle through
            if($totalPages == 1) {
                $totalPages = $structures->pages;
            }

            //Dispatch a job to get all of the structure information from ESI
            foreach($structures as $structure) {
                $job = new JobProcessStructure;
                $job->charId = 93738489;
                $job->corpId = 98287666;
                $job->structure = $structure;
                JobProcessStructure::dispatch($job)->onQueue('default');
            }
            
        } while ($currentPage < $totalPages);

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
