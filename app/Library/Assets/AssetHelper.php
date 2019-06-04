<?php

namespace App\Library\Assets;

//Internal Library
use Log;
use DB;

//App Library
use App\Jobs\Library\JobHelper;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

use App\Models\Jobs\JobProcessAsset;
use App\Models\Jobs\JobStatus;
use App\Models\Models\Stock;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

class AssetHelper {

    private $charId;
    private $corpId;
    private $page;

    public function __construct($char, $corp, $pg = null) {
        $this->charId = $char;
        $this->corpId = $corp;
        $this->page = $pg;
    }

    /**
     * Get Assets By Page in order to store in the database
     */
    public function GetAssetsByPage($page) {
        // Disable all caching by setting the NullCache as the
        // preferred cache handler. By default, Eseye will use the
        // FileCache.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Setup the esi authentication container
        $config = config('esi');
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => $this->charId])->get(['refresh_token']);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        //Setup the ESI variable
        $esi = new Eseye($authentication);

        try {
            $assets = $esi->page($this->page)
                          ->invoke('get', '/corporations/{corporation_id}/assets', [
                              'corporation_id' => $this->corId,
                          ]);
        } catch(RequestFailedException $e) {
            Log::critical("Failed to get page of assets from ESI.");
            $assets = null;
        }

        return $assets;
    }

    /**
     * Store a new asset record in the database
     */
    public function StoreNewAsset($asset) {
        //See if we find any assets which already exist
        $found = Asset::where([
            'item_id' => $asset->item_id,
        ])->count();

        //If nothing is found 
        if($found == 0) {
            $item = new Asset;
            if(isset($asset['is_blueprint_copy'])) {
                $item->is_blueprint_copy = $asset->is_blueprint_copy;
            }
            $item->is_singleton = $asset->is_singleton;
            $item->item_id = $asset->item_id;
            $item->location_flag = $asset->location_flag;
            $item->location_id = $asset->location_id;
            $item->location_type = $asset->location_type;
            $item->quantity = $asset->quantity;
            $item->type_id = $asset->type_id;
            $item->save();
        }  else {
            $this->UpdateAsset($asset);
        }      
    }

    /**
     * Purge old data, so we don't run into data issues
     */
    public function PurgeStaleData() {
        $date = Carbon::now()->subDay(1);

        Asset::where('updated_at', '<', $date)->delete();
    }

    /**
     * Update an existing asset based off the esi pull
     */
    private function UpdateAsset($asset) {
        $item = Asset::where([
            'item_id' => $asset->item_id,
        ])->count();

        if($count != 0) {
            Asset::where([
                'item_id' => $asset->item_id,
            ])->update([
                'is_singleton' => $asset->is_singleton,
                'location_flag' => $asset->location_flag,
                'location_id' => $asset->location_id,
                'location_type' => $asset->location_type,
                'quantity' => $asset->quantity,
                'type_id' => $asset->type_id,
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

?>