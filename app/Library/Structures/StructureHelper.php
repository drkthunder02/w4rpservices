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

    private $structureInfo;

    public function __construct($structure) {
        $esi = new Esi();
        $structureScope = $esi->HaveEsiScope($charId, 'esi-universe.read_structurs.v1');
        
        if($structureScope == false) {
            $this->scopeCheck = false;
        } else {
            $this->scopeCheck = true;
        }

        $this->structureInfo = $structure;
    }
}

?>