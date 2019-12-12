<?php

namespace App\Library\Assets;

//Internal Library
use Log;
use DB;
use Carbon\Carbon;

//App Library
use App\Jobs\Library\JobHelper;
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;

use App\Models\Jobs\JobProcessAsset;
use App\Models\Jobs\JobStatus;
use App\Models\Stock\Asset;
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
    public function GetAssetsByPage() {
        //Declare the variable for the esi helper
        $esiHelper = new Esi;

        //Setup the esi authentication container
        $config = config('esi');

        //Check for the scope needed
        $hasScope = $esiHelper->HaveEsiScope($this->charId, 'esi-assets.read_corporation_assets.v1');
        if($hasScope == false) {
            Log::critical('ESI Scope check has failed for esi-assets.read_corporation_assets.v1 for character id: ' . $this->charId);
            return null;
        }
        
        //Get the refresh token from the database
        $token = $esiHelper->GetRefreshToken($this->charId);
        //Setup the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);
        
        try {
            $assets = $esi->page($this->page)
                          ->invoke('get', '/corporations/{corporation_id}/assets', [
                              'corporation_id' => $this->corpId,
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
            if(isset($asset->is_blueprint_copy)) {
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
     * Get the liquid ozone asset
     */
    public function GetAssetByType($type, $structureId) {
        //See if the row is in the database table
        $count = Asset::where([
            'location_id' => $structureId,
            'type_id' => $type,
            'location_flag' => 'StructureFuel',
        ])->count();
        //Get the row if it is in the table
        $asset = Asset::where([
            'location_id' => $structureId,
            'type_id' => $type,
            'location_flag' => 'StructureFuel',
        ])->first();

        if($count == 0) {
            return 0;
        } else {
            return $asset['quantity'];
        }
    }

    /**
     * Update an existing asset based off the esi pull
     */
    private function UpdateAsset($asset) {

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

?>