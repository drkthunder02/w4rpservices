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
    private $fcId;
    private $endTime;

    /**
     * Constructor
     * 
     * @param fcId
     */
    public function __construct($charId = null, $fleetId = null) {
        $this->fcId = $charId;
        $this->fleet = $fleetId;
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

    public function UpdateFleet($fleet, $isFreeMove, $motd) {
        //Get the fcid from the datatable
        $fc = DB::table('Fleets')->where('fleet', $fleetId)->get();

        //Check if the fc has the right scope
        if(!$this->HaveEsiScope($fc->character_id, 'esi-fleets.write_fleet.v1')) {
            return 1;
        }
        //Get the FC's refresh token from the table
        $token = DB::table('EsiTokens')->where('character_id', $fc->character_id)->first();
        //Create the esi authentication container
        $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
            'client_id' => env('ESI_CLIENT_ID'),
            'secret' => env('ESI_SECRET_KEY'),
            'refresh_token' => $token->refresh_token,
        ]);
        //Create the esi class
        $esi = new Eseye($authentication);
        $error = $esi->invoke('put', '/fleets/{fleet_id}/', [
            'fleet_id' => $fleet,
            'new_settings' => [
                'is_free_move' => $isFreeMove,
                'motd' => $motd,
            ],
        ]);

        return $error;
    }

    public function AddPilot($fc, $charId) {
         //Check if the fc has the right scope
        if(!$this->HaveEsiScope($fc, 'esi-fleets.write_fleet.v1')) {
            return 1;
        }
        
        //Get the ESI token for the FC to add the new pilot
        $token = DB::table('EsiTokens')->where('character_id', $fc->character_id)->first();
        //Create the ESI Call Container
        $authentication = new EsiAuthentication([
            'client_id' => env('ESI_CLIENT_ID'),
            'secret' => env('ESI_SECRET_KEY'),
            'refresh_token' => $token->refresh_token,
        ]);
        //Crate the ESI Class
        $esi = new Eseye($authentication);
        //Perform the call to ESI
        $error = $esi->invoke('post', '/fleets/{fleet_id}/members/', [
            'fleet_id' => $this->fleet,
            'body' => [
                'character_id' => $charId,
                'role' => 'squad_member',
            ],
        ]);

        return $error;
    }

    public function RenderFleetDisplay() {
        //
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