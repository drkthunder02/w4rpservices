<?php

namespace App\Jobs\Commands\PublicContracts;

//Internal Libraries
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

//Jobs
use App\Jobs\Commands\PublicContracts\GetPublicContractItemsJob;

//Models
use App\Models\PublicContracts\PublicContract;
use App\Models\PublicContracts\PUblicContractItem;

class GetPublicContractItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Private Variables
     */
    private $esi;
    private $contractId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($esi, $contract)
    {
        $this->esi = $esi;
        $this->contractId = $contract;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
