<?php

namespace App\Jobs\Commands\Structures;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Jobs
use App\Jobs\Commands\Structures\FetchAllianceStructures;

//Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;

class PurgeAllianceStructures implements ShouldQueue
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
        //Set the connection for the job
        $this->connection = 'redis';
        $this->onQueue('structures');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Structure::truncate();
        Service::truncate();

        FetchAllianceStructures::dispatch();
    }

    /**
     * Set the tags for the job
     * 
     * @var array
     */
    public function tags() {
        return ['PurgeAllianceStructures', 'AllianceStructures', 'Structures'];
    }
}
