<?php

namespace App\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use DB;

class AllianceOrders {
    
    public function __construct() {

    }

    /**
     * @param $itemId
     * @param $regionId
     * @param $systemId
     * 
     * @return $price
     */
    public function PriceLookUp($itemId, $regionId = null, $systemId = null) {

        return $price;
    }

    
}

?>