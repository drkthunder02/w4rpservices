<?php

namespace App\Library\Fleets;

//Internal Libraries
use Log;

//Seat Stuff
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

//Libraries
use App\library\Esi\Esi;

//Models
use App\Models\Fleets\AllianceFleet;
use App\Models\Fleets\AllianceFleetMember;

class FleetHelper {
    //Variables
    private $esi;

    //Constructi
    public function __construct($charId) {
        //Declare a variable for use by the constructor
        $esiHelper = new Esi;

        //Check for the ESI scope
        $check = $esiHelper->HaveEsiScope($charId, 'esi-fleets.read_fleet.v1');
        if($check) {
            //Setup the ESI authentication container
            $this->esi = $esiHelper->SetupEsiAuthentication();
        } else {
            $this->esi = null;
        }
    }

    //Get fleet information
    public function GetFleetInfo($fleetId) {

    }

    //Get fleet members
    public function GetFleetMembers($fleetId) {

    }

    //Get fleet wings
    public function GetFleetWings($fleetId) {

    }

    //Update fleet time
    public function UpdateFleetTime($fleetId) {

    }

    //Update fleet character names
    public function UpdateFleetCharacters($fleetId) {
        
    }
}

?>