<?php

namespace App\Jobs\Commands\MiningTaxes;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//Models
use App\Models\MiningTax\Observer;

//Jobs
use App\Jobs\Commands\MiningTaxes\Ledgers\FetchMiningTaxesLedgers;

class PreFetchMiningTaxesLedgers implements ShouldQueue
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
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        //Get the site configuration which holds some data we need
        $config = config('esi');
        //Get the observers from the database
        $observers = Observer::all();
        
        //For each of the observers, send a job to fetch the mining ledger
        foreach($observers as $obs) {
            //Dispatch the mining taxes ledger jobs
            FetchMiningTaxesLedgers::dispatch($config['primary'], $config['corporation'], $obs->observer_id);
        }
    }

    /**
     * Set the tags for Horzion
     * 
     * @var array
     */
    public function tags() {
        return ['PreFetchMiningTaxesLedgers', 'MiningTaxes', 'MiningTaxesLedgers'];
    }
}
