<?php

namespace App\Jobs\Commands\Finances;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//Application Library
use App\Library\Helpers\FinanceHelper;
use Commands\Library\CommandHelper;

//Models
use App\Models\Finances\AllianceWalletJournal;

class UpdateAllianceWalletJournal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 1800;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $fHelper = new FinanceHelper;
        $config = config('esi');

        $pages = $fHelper->GetAllianceWalletJournalPages(1, $config['primary']);

        for($i = 1; $i < $pages; $i++) {
            UpdateAllianceWalletJournalPage::dispatch(1, $config['primary'], $page)->onQueue('finances');
        }
    }
}
