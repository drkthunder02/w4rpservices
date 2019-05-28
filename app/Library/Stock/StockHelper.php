<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Structures\Helper;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Log;

//Job
use App\Jobs\ProcessStocksJob;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

//Models
use App\Models\Stock\StructureStock;

class StructureStockHelper {

    private $scopeCheck;

    public function __construct() {
        $esi = new Esi();

        $assetScope = $esi->HaveEsiScope($charId, 'esi-assets.read_corporation_assets.v1');
        
        if($assetScope == false) {
            $scopeCheck = false;
        } else {
            $scopeCheck = true;
        }
    }

    public function GetAssetList() {
        if($this->scopeCheck == false) {
            Log::critical("Structure Stock Helper didn't have the correct scopes available.");
            return null;
        }

        //Setup the esi authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'=> $config['client_id'],
            'secret' => $config['secret'],
        ]);
        
        $esi = new Eseye($authentication);
        try {
            $newAssets = $esi->invoke('get', '/corporations/{corporation_id}/assets/', [
                'corporation_id' => 98287666,
            ]);
        } catch(RequestFailedException $e) {
            Log::critical($e->getEsiExceptionResponse());
            return null;
        }

        //How to deal with stale data in this table?

        foreach($newAssets as $asset) {
            //See if the asset is in the asset table already.
            $found = Asset::where(['item_id' => $asset['item_id']]);
            if(!$found) {
                $newItem = new Asset;
                if(isset($asset['is_blueprint_copy'])) {
                    $newItem->is_blueprint_coopy = $asset['is_blueprint_copy'];
                }
                $newItem->is_singleton = $asset['is_singleton'];
                $newItem->item_id = $asset['item_id'];
                $newItem->location_flag = $asset['location_flag'];
                $newItem->location_id = $asset['location_id'];
                $newItem->location_type = $asset['location_type'];
                $newItem->quantity = $asset['quantity'];
                $newItem->type_id = $asset['type_id'];
            }
        }
    }
}

?>