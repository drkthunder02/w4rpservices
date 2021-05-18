<?php

namespace App\Jobs\Commands\Assets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

//Models
use App\Models\Structure\Asset;

class ProcessAllianceAssets implements ShouldQueue
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

    //Private variable
    private $asset;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($a)
    {
        //Set the connection for the job
        $this->connection = 'redis';

        $this->asset = $a;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * If the asset is not in the database, then let's save it to the database,
         * otherwise, we just update the old asset
         */
        if(Asset::where(['item_id' => $this->asset->item_id])->count() == 0) {
            $as = new Asset;
            if(isset($this->asset->is_blueprint_copy)) {
                $as->is_blueprint_copy = $this->asset->is_blueprint_copy;
            }
            $as->is_singleton = $this->asset->is_singleton;
            $as->item_id = $this->asset->item_id;
            $as->location_flag = $this->asset->location_flag;
            $as->quantity = $this->asset->quantity;
            $as->type_id = $this->asset->type_id;
            $as->save();
        } else {
            //Update the previously found asset
            Asset::where([
                'item_id' => $this->asset->item_id,
            ])->update([
                'is_blueprint_copy' => $this->asset->is_blueprint_copy,
                'location_flag' => $this->asset->location_flag,
                'location_type' => $this->asset->location_type,
                'quantity' => $this->asset->quantity,
                'type_id' => $this->asset->type_id,
            ]);

            if(isset($this->asset->is_singleton)) {
                Asset::where([
                    'item_id' => $this->asset->item_id,
                ])->update([
                    'is_singleton' => $this->asset->is_singleton,
                ]);
            }
        }
    }
}
