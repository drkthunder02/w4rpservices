<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;

//Application Library
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\MiningTaxes\CalculateMiningTaxesJob;

class MiningTaxesInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mining Taxes Invoice Command';

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
        $task = new CommandHelper('MiningTaxesInvoices');
        //Set the task as started
        $task->SetStartStatus();

        CalculateMiningTaxesJob::dispatch()->onQueue('miningtaxes');

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
