<?php

namespace App\Jobs\Commands\PublicContracts;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Log;

//Library

//Models
use App\Models\PublicContracts\PublicContract;
use App\Models\PublicContracts\PublicContractItem;


/**
 * Job to purge some old data from the public contracts
 */
class PurgePublicContracts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Nothing to do in this part of the job
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get today's date
        $today = Carbon::now();

        //If the date for a contract has expired, then purge it from the system
        $contracts = PublicContract::all();
        
        //Check each contract to see if it has expired
        foreach($contracts as $contract) {
            //If the contract has expired, then delete the contract and all of it's items
            if($today->greaterThan($contract->date_expired)) {
                //Delete the contract
                PublicContract::where([
                    'id' => $contract->id,
                ])->delete();

                //Delete the items from the contract from the other table
                PublicContract::where([
                    'contract_id' => $contract->id,
                ])->delete();
            }
        }
    }
}
