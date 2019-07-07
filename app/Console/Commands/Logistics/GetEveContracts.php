<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;
use DB;
use Log;

//Job
use App\Jobs\ProcessContractsJob;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Commands\Library\CommandHelper;
use App\Library\Logistics\ContractsHelper;

//Models
use App\Models\Jobs\JobProcessContracts;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

class GetEveContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:GetContracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get contracts from a certain corporation';

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
        $task = new CommandHelper('GetContracts');

        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup the esi authentication container
        $config = config('esi');

        //Declare some variables
        $charId = 2115439862;
        $corpId = 98606886;

        //Esi Scope Check
        $esiHelper = new Esi();
        $contractScope = $esiHelper->HaveEsiScope($charId, 'esi-contracts.read_corporation_contracts.v1');

        if($contractScope == false) {
            Log::critical('Scope check for esi contracts failed.');
            return null;
        }

        // Disable all caching by setting the NullCache as the
        // preferred cache handler. By default, Eseye will use the
        // FileCache.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => $charId])->get(['refresh_token']);
        //Create the authentication container
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        $esi = new Eseye($authentication);

        try {
            $contracts = $esi->page(1)
                            ->invoke('get', '/corporations/{corporation_id}/contracts/', [
                                'corporation_id' => $corpId,
                            ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get the contracts list.");
            return null;
        }

        $pages = $contracts->pages;

        for($i = 1; $i <= $pages; $i++) {
            $job = new JobProcessEveContracts;
            $job->charId = $charId;
            $job->corpId = $corpId;
            $job->page = $i;
            ProcessEveContractsJob::dispatch($job)->onQueue('default');
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
