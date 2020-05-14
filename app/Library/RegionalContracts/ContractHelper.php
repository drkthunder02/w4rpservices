<?php

namespace App\Library\RegionalContracts;

//Internal Library
use Log;
use Carbon\Carbon;

//Library
use App\Library\Esi\Esi;

class RegionalContractHelper {
    
    /**
     * Region variable
     * 
     * @var int
     */
    private $region;

    /**
     * ESI Variable
     * 
     * @var esi
     */
    private $esi;
    
    /**
     * Construct
     */
    public function __construct($region, $esi) {
        $this->region = $region;
        $this->esi = $esi;
    }

    /**
     * Get the contracts within a region
     * 
     * @var private
     */
    public function GetContracts() {
        
        //Get the public contracts from the ESI
        $responses = $this->esi->invoke('get', '/contracts/public/{region_id}/', [
            'region_id' => $this->region,
        ]);

        //Send the contracts back to the calling function
        return $responses;
    }

    /**
     * Price the regional contract
     * 
     * @var buyout
     * @var contract_id
     * @var date_expired
     * @var date_issued
     * @var days_to_complete
     * @var end_location_id
     * @var issuer_id
     * @var price
     * @var title
     * @var type [unknown, item_exchange, auction, courier, loan]
     * @var volume
     */
    public function PriceContract($contract) {

    }

    /**
     * Function to get the items in a contract from ESI
     * 
     * @var id
     */
    public function GetContractItems($contractId) {
        $items = $this->esi->invoke('get', '/contracts/public/items/{contract_id}/', [
            'contract_id' => $contractId,
        ]);

        return $items;
    }
}

?>