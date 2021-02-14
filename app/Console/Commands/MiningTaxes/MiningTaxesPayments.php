<?php

namespace App\Console\Commands\MiningTaxes;

use Illuminate\Console\Command;
use Log;

//Application Library
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPaymentsJob;

class MiningTaxesPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process mining tax payments';

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
        $task = new CommandHelper('MiningTaxesPayments');
        //Set the task as started
        $task->SetStartStatus();

        //Dispatch the job
        ProcessMiningTaxesPaymentsJob::dispatch()->onQueue('miningtaxes');

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
