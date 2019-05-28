<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Stock\Helper;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

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
        $structureScope = $esi->HaveEsiScope($charId, 'esi-universe.read_structurs.v1');
        
        if($assetScope == false || $structureScope == false) {
            $scopeCheck = false;
        } else {
            $scopeCheck = true;
        }
    }

}

?>