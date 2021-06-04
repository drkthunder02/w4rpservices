<?php

namespace App\Jobs\Commands\Assets;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Jobs
use App\Jobs\Commands\Assets\FetchAllianceAssets;

//Models
use App\Models\Structure\Asset;

class PurgeAllianceAssets implements ShouldQueue
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
        $this->onQueue('assets');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Asset::truncate();

        FetchAllianceAssets::dispatch()->delay(Carbon::now()->addSeconds(30));
    }
}
