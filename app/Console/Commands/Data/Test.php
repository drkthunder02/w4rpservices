<?php

namespace App\Console\Commands\Data;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Models
use App\Models\ScheduledTask\ScheduleJob;

//Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test ESI stuff.';

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
     * @return int
     */
    public function handle()
    {
        //Declare some variables
        $esiHelper = new Esi;
        $config = config('esi');

        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Get the ESI Token
        $token = $esiHelper->GetRefreshToken($config['primary']);
        $esi = $esiHelper->SetupEsiAuthentication($token);

        try {
            $stuff = $esi->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                'corporation_id' => $config['corporation'],
                'division' => 1,
            ]);
        } catch(RequestFailedException $e) {
            dd($e);
        }

        dd(json_decode($stuff->raw, true));
    }
}
