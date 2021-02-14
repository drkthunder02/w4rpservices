<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;

//Application Library
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesLedgersJob;

class MiningTaxesLedgers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Ledgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start getting the mining ledgers.';

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
        //Create the command helper container
        $task = new CommandHelper('MiningTaxesLedger');
        //Set the task as started
        $task->SetStartStatus();

        FetchMiningTaxesLedgersJob::dispatch()->onQueue('miningtaxes');

        //Return 0
        return 0;
    }
}
