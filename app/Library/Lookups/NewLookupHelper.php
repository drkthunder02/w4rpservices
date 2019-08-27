<?php

namespace App\Library\Lookups;

//Internal Libraries
use DB;
use Log;

//Library
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException; 

//Models
use App\Models\Lookups\CharacterToCorporation;
use App\Models\Lookups\CorporationToAlliance;

class NewLookupHelper {

    //Variables
    private $esi;

    //Construct
    public function __construct() {
        $this->esi = new Eseye();
    }



}

?>