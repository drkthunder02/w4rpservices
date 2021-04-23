<?php

namespace App\Console\Commands\Finances;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use App\Library\Helpers\FinanceHelper;
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\Finances\UpdateAllianceWalletJournal as UAWJ;

//Models
use App\Models\Finances\AllianceWalletJournal;

class UpdateAllianceWalletJournal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finances:UpdateJournals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update the holding corporation's finance journal.";

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
        //Declare variables
        $fHelper = new FinanceHelper;
        $config = config('esi');
        $task = new CommandHelper('UpdateAllianceWalletJournal');
        //Set the task as started
        $task->SetStartStatus();
        $startTime = time();

        UAWJ::dispatch();

        $endTime = time();

        printf("Updating the wallets took " . ($endTime - $startTime) . " seconds.\r\n");

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
