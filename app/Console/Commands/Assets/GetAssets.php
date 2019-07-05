<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

//Job
use App\Jobs\ProcessAssetsJob;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Commands\Library\CommandHelper;

//Models
use App\Models\Jobs\JobProcessAssets;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;


class GetAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:GetAssets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets all of the assets of a corporation.';

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
        $task = new CommandHelper('GetAssets');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup the esi authentication container
        $config = config('esi');

        //Declare some variables
        $charId = $config['primary'];
        $corpId = 98287666;

        //ESI Scope Check
        $esiHelper = new Esi();
        $assetScope = $esiHelper->HaveEsiScope($config['primary'], 'esi-assets.read_corporation_assets.v1');

        if($assetScope == false) {
            Log::critical("Scope check for esi failed.");
            return null;
        }

        
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => $charId])->get(['refresh_token']);
        dd($token);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        $esi = new Eseye($authentication);

        try {
            $assets = $esi->invoke('get', '/corporations/{corporation_id}/assets/', [
                              'corporation_id' => $corpId,
                          ]);
        } catch (RequestFailedException $e) {
            //
        }

        for($i = 1; $i <= $assets->pages; $i++) {
            $job = new JobProcessAsset;
            $job->charId = $charId;
            $job->corpId = $corpId;
            $job->page = $i;
            ProcessAssetJob::dispatch($job)->onQueue('default');
        }
    }
}
