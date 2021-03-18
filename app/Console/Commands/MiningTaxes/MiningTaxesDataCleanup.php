<?php

namespace App\Console\Commands\MiningTaxes;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Commands\Library\CommandHelper;

//Models
use app\Models\MiningTax\Ledger;

class MiningTaxesDataCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTaxes:CleanupData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old ledger entries for mining taxes.';

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
        $task = new CommandHelper('MiningTaxesDataCleanup');
        //Set the task as started
        $task->SetStartStatus();

        //Clean up old data
        Ledger::where(['updated_at', '<', Carbon::now()->subDays(90)])->delete();

        //Set the task as finished
        $task->SetStopStatus();

        return 0;
    }
}
