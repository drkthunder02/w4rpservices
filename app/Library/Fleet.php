<?php

namespace App\Library;

use Session;
use DB;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class Fleet {
    /**
     * Get fleet information
     */
    public function GetFleetInfo($uri) {

    }

    /**
     * Update fleet information
     */
    public function UpdateFleet($fleet) {

    }

    /**
     * Create a standing fleet from a registered fleet.
     */
    public function CreateStandingFleet($fleet) {
        
    }

    /**
     * Join the standing fleet
     */
    public function JoinStandingFleet($fleet, $charId) {

    }

    /**
     * Leave the standing fleet
     */
    public function LeaveStandingFleet($fleet, $charId) {

    }

    /**
     * Create new wing in a fleet
     */
    public function CreateNewWing($fleet) {

    }

    /**
     * Create new squad in a fleet
     */
    public function CreateNewSquad($fleet) {

    }

    /**
     * Modify the MotD of a fleet
     */
    public function ModifyMOTD($fleet) {
        
    }

    /**
     * Get a fleet's squads
     */
    public function GetSquads($fleet) {

    }

    /**
     * Rename a fleet's squad
     */
    public function RenameSquad($fleet, $squad, $name) {

    }
    /**
     * Get fleet's wings
     */
    public function GetWings($fleet) {

    }

    /**
     * Rename a fleet wing
     */
    public function RenameWing($fleet, $wing, $name) {

    }
}

?>