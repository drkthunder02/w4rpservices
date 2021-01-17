<?php

namespace App\Jobs\Commands\MiningTaxes;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchMiningTaxesObserversJob implements ShouldQueue
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
     * Job Variables
     */
    private $charId;
    private $corpId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($charId, $corpId)
    {
        $this->charId = $charId;
        $this->corpId = $corpId;

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * The job's duty is to get all of the corporation's moon mining observers,
     * then store them in the database.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $sHelper = new StructureHelper($this->charId, $this->corpId);

        /**
         * Remove the current observers from the database.
         */

        /**
         * Create the esi call to get the current observers
         */

        /**
         * Add the current observers in the database
         */

        /**
         * Cleanup
         */
    }
}
