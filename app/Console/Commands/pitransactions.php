<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;

class PiTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:PiTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the transactions from the market alt for the alliance';

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
        //Create the command helper container
        $task = new CommandHelper('PiTransactions');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup the Finances container
        $finance = new FinanceHelper();

        $finance->GetWalletTransaction(3, 94415555);

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
