<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Moons;

//Internal Library
use Session;
use DB;
use Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

//Models
use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\Moon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;

class MoonCalcAvg {

    public function GetOreComposition($ore) {
        $composition = ItemComposition::where([
            'Name' => $ore,
        ])->first();

        return $composition;
    }

}

?>