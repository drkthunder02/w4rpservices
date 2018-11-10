<?php

namespace App\Library;

use Auth;
use Session;
use DB;

use App\Models\EsiToken;
use App\Library\Fleet;
use Carbon\Carbon;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class Fleet {

    private $fleet;
    private $endTime;

    private $fcId;

    /**
     * Constructor
     * 
     * @param fcId
     */
    public function __construct($charId) {
        $this->fcId = $charId;
    }

    /**
     * Set Fleet number
     * 
     * @param fleetUri
     */
    public function SetFleetUri($fleetUri) {
        //Trim the left side of the fleet number
        $fleetUri = ltrim($fleetUri, 'https://esi.tech.ccp.is/v1/fleets/');
        //Trim the right side of the fleet number
        $fleetUri = rtrim($fleetUri, '/?datasource=tranquility');
        $this->fleet = $fleetUri;

        return $this->fleet;
    }

    /**
     * Set the fleet's end time
     * 
     * @param endTime
     */
    public function SetFleetEndTime($endTime) {
        $this->endTime = $endTime;
    }

    public function UpdateFleet($isFreeMove, $motd) {
        //Check if the fc has the right scope
        if(!$this->HaveEsiScope($this->fcId, 'esi-fleets.write_fleet.v1')) {
            return false;
        }
        //Get the FC's refresh token from the table
        $token = DB::table('EsiTokens')->where('character_id', $this->fcId)->first();
        //Create the esi authentication container
        $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
            'client_id' => env('ESI_CLIENT_ID'),
            'secret' => env('ESI_SECRET_KEY'),
            'refresh_token' => $token->refresh_token,
        ]);
        //Create the esi class
        $esi = new Eseye($authentication);
        $error = $esi->invoke('put', '/fleets/{fleet_id}/', [
            'fleet_id' => $this->fleet,
            'new_settings' => [
                'is_free_move' => $isFreeMove,
                'motd' => $motd,
            ],
        ]);
    }

    public function RenderFleetDisplay() {
        if(!$this->HaveEsiScope($this->fcId, 'esi-fleets.read_fleet.v1')) {
            return false;
        }

        $display = array();

        //Get the FC's refresh token from the table
        $token = DB::table('EsiTokens')->where('character_id', $this->fcId)->first();
        //Create the esi authentication container
        $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
            'client_id' => env('ESI_CLIENT_ID'),
            'secret' => env('ESI_SECRET_KEY'),
            'refresh_token' => $token->refresh_token,
        ]);
        //Create the esi class
        $esi = new Eseye($authentication);
        //Get the wings for the fleet wing ids
        $wings = $esi->invoke('get', '/fleets/{fleet_id}/wings/', [
            'fleet_id' => $this->fleet,
        ]);
    }

    private function HaveEsiScope($charId, $scope) {
        //Check for an esi scope
        $checks = DB::table('EsiScopes')->where('character_id')->get();
        foreach($checks as $check) {
            if($check->scope === $scope) {
                return true;
            }
        }

        return false;
    }
}

?>