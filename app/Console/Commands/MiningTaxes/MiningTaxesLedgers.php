<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Commands\Library\CommandHelper;

//Models
use App\Models\MiningTax\Observer;

//Jobs
//use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesLedgersJob;

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

        //Get the site configuration which holds some data we need
        $config = config('esi');
        //Get the observers from the database
        $observers = Observer::all();
        
        //For each of the observers, send a job to fetch the mining ledger
        foreach($observers as $obs) {
            //Dispatch the mining taxes ledger jobs
            FetchMiningTaxesLedgersJob::dispatch($config['primary'], $config['corporation'], $obs->observer_id)->onQueue('miningtaxes');
        }

        //Set the task as finished
        $task->SetStopStatus();
    
        //Return 0
        return 0;
    }
}
