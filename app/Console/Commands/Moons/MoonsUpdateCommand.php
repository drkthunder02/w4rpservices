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
use App\Models\Moon\CorpMoonObserver;
use App\Models\Moon\CorpMoonLedger;

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
        //
    }
}
