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
use App\Library\Lookups\LookupHellper;

//Models
use App\Models\PublicContracts\PublicContract;

class GetPublicContractsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job Variables
     */
    private $esi;
    private $region;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($esi, $regionId)
    {
        $this->esi = $esi;
        $this->region = $regionId;
    }

    /**
     * Execute the job.
     * The job gets all of the contracts in a region
     *
     * @return void
     */
    public function handle()
    {
        $responses = $this->esi->invoke('get', '/contracts/public/{region_id}/', [
            'region_id' => $this->region,
        ]);

        foreach($response as $resp) {
            $count = PublicContract::where([
                'contract_id' => $resp->contract_id,
            ])->count();
            if($count == 0) {
                $pub = new PublicContract;
                $pub->region_id = $this->region;
                if(isset($resp->buyout)) {
                    $pub->buyout = $resp->buyout;
                }
                if(isset($resp->collateral)) {
                    $pub->collateral = $resp->collateral;
                }
                $pub->contract_id = $resp->contract_id;
                $pub->date_expired = $resp->date_expired;
                $pub->date_issed = $resp->date_issed;
                if(isset($resp->days_to_complete)) {
                    $pub->days_to_complete = $resp->days_to_complete;
                }
                if(isset($resp->end_location_id)) {
                    $pub->end_location_id = $resp->end_location_id;
                }
                if(isset($resp->for_corporation)) {
                    $pub->for_corporation = $resp->for_corporation;
                }
                $pub->issuer_corporation_id = $resp->issuer_corporation_id;
                $pub->issuer_id = $resp->issuer_id;
                if(isset($resp->price)) {
                    $pub->price = $resp->price;
                }
                if(isset($resp->reward)) {
                    $pub->reward = $resp->reward;
                }
                if(isset($resp->start_location_id)) {
                    $pub->start_location_id = $resp->start_location_id;
                }
                if(isset($resp->title)) {
                    $pub->title = $resp->title;
                }
                $pub->type = $resp->type;
                if(isset($resp->volume)) {
                    $pub->volume = $resp->volume;
                }
                //Save the new contract
                $pub->save();

                //Dispatch a job to collect the contract items
                GetPublicContractItemsJob::dispatch($this->esi, $resp->contract_id);
            }
        }
    }
}
