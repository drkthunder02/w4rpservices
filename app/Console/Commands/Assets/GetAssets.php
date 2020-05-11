<?php

namespace App\Console\Commands\Assets;

use Illuminate\Console\Command;
use DB;
use Log;

//Job
use App\Jobs\ProcessAssetsJob;

//Library
use App\Library\Esi\Esi;
use Commands\Library\CommandHelper;
use App\Library\Assets\AssetHelper;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\Jobs\JobProcessAsset;

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
    protected $description = 'Gets all of the assets of the holding corporation.';

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
        $assets = null;
        $pages = 0;

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
            Log::critical("Scope check for esi-assets.read_corporation_assets.v1 failed.");
            return null;
        }
        
        //Get the refresh token from the database
        $token = $esiHelper->GetRefreshToken($charId);
        //Create the authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        try {
            $assets = $esi->page(1)
                          ->invoke('get', '/corporations/{corporation_id}/assets/', [
                              'corporation_id' => $corpId,
                          ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get asset list.");
            return null;
        }

        $pages = $assets->pages;
        
        for($i = 1; $i <= $pages; $i++) {
            ProcessAssetsJob::dispatch($charId, $corpId, $i)->onQueue('assets');
        }
    }
}
