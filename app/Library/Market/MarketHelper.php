<?php

namespace App\Library\Market;

//Internal Library
use Log;
use Carbon\Carbon;

//Library
use App\Library\Esi\Esi;

class MarketHelper {

    /**
     * Private variables
     * 
     * @var esi
     */
    private $esi;

    /**
     * Class Construct
     */
    public function __construct($esi = null) {
        $this->esi = $esi;
    }

    /**
     * Get the regional market orders
     * 
     * @var region
     */
    public function GetRegionalMarketOrders($region) {

    }

    /**
     * Price an item out and return the different price schemes
     * 
     * @var itemId
     */
    public function PriceItem($itemId) {

    }

    /**
     * Get the market group infromation
     * 
     * @var group
     */
    public function GetMarketGroup($group) {

    }

    /**
     * Get the public contract items
     * 
     * @var contractId
     */
    public function GetPublicContractItem($contractId) {

    }

    /**
     * Get market price for an item
     * 
     * @var itemId
     */
    public function GetMarketPrice($itemId) {

    }
}

?>