<?php

namespace App\Console\Commands\Wormholes;

//Internal Library
use Illuminate\Console\Command;
use Carbon\Carbon;

//Models
use App\Models\Wormholes\AllianceWormhole;

class PurgeWormholes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:PurgeWormholeData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge stale wormhole data automatically';

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
        //Time of now
        $currentTime = Carbon::now();

        AllianceWormhole::where('created_at', '<', $currentTime->subHours(48))->delete();
    }
}
