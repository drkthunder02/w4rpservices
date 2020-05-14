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
        //Get the items from esi
        $responses = $this->esi->invoke('get', '/contracts/public/items/{contract_id}/', [
            'contract_id' => $this->contract,
        ]);

        foreach($response as $resp) {
            //See if the item exists
            $count = PublicContractItems::where([
                'record_id' => $resp->record_id,
            ])->count();
            
            //If the item doesn't exist in the database, save it to the database
            if($count == 0) {
                $contractItem = new PublicContractItems;
                if(isset($resp->is_blueprint_copy)) {
                    $contractItem->is_blueprint_copy = $resp->is_blueprint_copy;
                }
                $contractItem->is_included = $resp->is_included;
                if(isset($resp->item_id)) {
                    $contractItem->item_id = $resp->item_id;
                }
                if(isset($resp->material_efficiency)) {
                    $contractItem->material_efficiency = $resp->material_efficiency;
                }
                $contractItem->quantity = $resp->quantity;
                $contractItem->recorded_id = $resp->recorded_id;
                if(isset($resp->runs)) {
                    $contractItem->runs = $resp->runs;
                }
                if(isset($resp->time_efficiency)) {
                    $contractItem->time_efficiency = $resp->time_efficiency;
                }
                $contractItem->type_id = $resp->type_id;
                $contractItem->save();
            }
        }
    }
}
