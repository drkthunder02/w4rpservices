<?php

namespace App\Console\Commands\Data;

//Internal Library
use Illuminate\Console\Command;

//Jobs
use App\Jobs\Commands\Moons\PurgeMoonLedgerJob;

class PurgeCorpMoonLedgers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:PurgeCorpLedgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old corp ledgers data';

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
        PurgeMoonLedgerJob::dispatch();
    }
}
