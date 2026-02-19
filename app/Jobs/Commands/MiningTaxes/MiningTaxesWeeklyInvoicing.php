<?php

namespace App\Jobs\Commands\MiningTaxes;

use App\Jobs\Commands\MiningTaxes\Invoices\ProcessAllianceMiningOperations;
use App\Jobs\Commands\MiningTaxes\Invoices\SendMiningTaxesInvoices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class MiningTaxesWeeklyInvoicing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Number of job retries
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Set job parameters
        $this->connection = 'redis';
        $this->onQueue('miningtaxes');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Bus::chain([
            new ProcessAllianceMiningOperations,
            new SendMiningTaxesInvoices,
        ])->dispatch();
    }
}
