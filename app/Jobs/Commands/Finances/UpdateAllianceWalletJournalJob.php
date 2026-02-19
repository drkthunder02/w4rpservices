<?php

namespace App\Jobs\Commands\Finances;

use App\Library\Helpers\FinanceHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// Application Library
use Log;

// Models

class UpdateAllianceWalletJournalJob implements ShouldQueue
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
        $this->onQueue('finances');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Declare variables
        $fHelper = new FinanceHelper;
        $config = config('esi');

        $pages = $fHelper->GetAllianceWalletJournalPages(1, $config['primary']);

        // If the number of pages received is zero there is an error in the job.
        if ($pages == 0) {
            Log::critical('Failed to get the number of pages in the job.');
            $this->delete();
        }

        for ($i = 1; $i <= $pages; $i++) {
            UpdateAllianceWalletJournalPage::dispatch(1, $config['primary'], $i)->onQueue('journal');
        }
    }

    /**
     * Set the tags for Horzion
     *
     * @var array
     */
    public function tags()
    {
        return ['UpdateAllianceWalletJournal', 'Finances'];
    }
}
