<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library
use App\Library\Structures\StructureHelper;
use App\Jobs\Library\JobHelper;

//App Models
use App\Models\Jobs\JobProcessAssets;
use App\Models\Jobs\JobStatus;
use App\Models\Stock\Asset;

class ProcessAssetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 300;

    /**
     * Number of job retries
     */
    public $tries = 3;

    /**
     * Job Variables
     */
    private $charId;
    private $corpId;
    private $page;
    private $esi;

    protected $location_array = [
        'CorpDeliveres',
        'CorpSAG1',
        'CorpSAG3',
        'CorpSAG4',
        'CorpSAG5',
        'CorpSAG6',
        'CorpSAG7',
        'StructureFuel',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessAssets $jpa)
    {
        $this->charId = $jpa->charId;
        $this->corpId = $jpa->corpId;
        $this->page = $jpa->page;
        //Setup the esi authentication container
        $config = config('esi');
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => $this->charId])->get(['refresh_token']);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        $this->esi = new Eseye($authentication);

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * The job's task is to get all fo the information for all of the assets in
     * a structure and store them in the database.  This task can take a few seconds
     * therefore we want the Horizon job queue to take care of the request rather
     * than the cronjob.
     *
     * @return void
     */
    public function handle()
    {
        //Get the pages of the asset list
        $assets = $this->GePageOfAssets();

        foreach($assets as $asset) {
            $found = Asset::where([
                'item_id' => $asset['item_id'],
            ])->get();

            //Update the asset if we found it, otherwise add the asset to the database
            if(!$found) {
                if(in_array($asset['location_flag'], $this->location_array)) {
                    $this->StoreNewAsset($asset);
                }
            } else {
                $this->UpdateAsset($asset);
            }
        }
    }

    private function UpdateAsset($asset) {
        if(isset($asset['is_blueprint_copy'])) {
            Asset::where([
                'item_id' => $asset['item_id'],
            ])->update([
                'is_blueprint_copy' => $asset['is_blueprint_copy'],
            ]);
        }

        Asset::where([
            'item_id' => $asset['item_id'],
        ])->update([
            'is_singleton' => $asset['is_singleton'],
            'item_id' => $asset['item_id'],
            'location_flag' => $asset['location_flag'],
            'location_id' => $asset['location_id'],
            'location_type' => $asset['location_type'],
            'quantity' => $asset['quantity'],
            'type_id' => $asset['type_id'],
        ]);
    }

    private function StoreNewAsset($asset) {
        $new = new Asset;
        if(isset($asset['is_blueprint_copy'])) {
            $new->is_blueprint_copy = $asset['is_blueprint_copy'];
        }        
        $new->is_singleton = $asset['is_singleton'];
        $new->item_id = $asset['item_id'];
        $new->location_flag = $asset['location_flag'];
        $new->location_id = $asset['location_id'];
        $new->location_type = $asset['location_type'];
        $new->quantity = $asset['quantity'];
        $new->type_id = $asset['type_id'];
        $new->save();
    }

    private function GetPageOfAssets() {
        try {
            $assets = $this->esi->page($this->page)
                                ->invoke('get', '/corporations/{corporation_id}/assets/', [
                                    'corporation_id' => $this->corpId,
                                ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get page of Assets from ESI.");
            $assets = null;
        }

        return $assets;
    }


}
