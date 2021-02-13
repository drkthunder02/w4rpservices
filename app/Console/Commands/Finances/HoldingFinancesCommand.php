<?php

namespace App\Console\Commands\Finances;

use Illuminate\Console\Command;

use App\Library\Finances\Helper\FinanceHelper;

//Jobs
use App\Jobs\Commands\Finances\ProcessWalletJournalJob;

class HoldingFinancesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:HoldingJournal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the holding corps finances.';

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
        //Setup the Finances container
        $finance = new FinanceHelper();

        //Get the esi configuration
        $config = config('esi');

        for($i = 1; $i <= 6; $i++) {
            ProcessWalletJournalJob::dispatch($i, $config['primary'])->onQueue('journal');
        }
    }
}
