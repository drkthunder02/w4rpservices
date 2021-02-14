<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesObserversJob;

class MiningTaxesObservers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get mining tax observers.';

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
        $task = new CommandHelper('MiningTaxesObservers');
        //Set the task as started
        $task->SetStartStatus();

        FetchMiningTaxesObserversJob::dispatch()->onQueue('miningtaxes');

        $task->SetStopStatus();

        //Return 0 saying everything is fine
        return 0;
    }
}
