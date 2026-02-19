<?php

namespace App\Console\Commands\Finances;

// Internal Library
use App\Jobs\Commands\Finances\UpdateAllianceWalletJournalJob;
// Application Library

// Jobs
use Illuminate\Console\Command;

// Models

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
        UpdateAllianceWalletJournalJob::dispatch()->onQueue('finances');
    }
}
