<?php

namespace App\Library;

use Auth;
use Session;
use DB;

use App\Models\Esi\EsiToken;
use App\Models\Fleet\Fleet;
use Carbon\Carbon;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class Fleets {

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
        $uris  = explode('https://esi.tech.ccp.is/v1/fleets/', $fleetUri);
        //Trim the right side of the fleet number
        $fleetUri = rtrim($uris[1], '/?datasource=tranquility');
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
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
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

    public function AddPilot($fc, $charId, $fleetId) {
                
        //Get the ESI token for the FC to add the new pilot
        $token = DB::table('EsiTokens')->where('character_id', $fc)->get();
        // Disable all caching by setting the NullCache as the
        // preferred cache handler. By default, Eseye will use the
        // FileCache.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;
        //Create the ESI Call Container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'     => $config['client_id'],
            'secret'        => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        $esi = new \Seat\Eseye\Eseye($authentication);

        try {
        //Setup the body of the esi message and perform the call
            $esi->setBody([
                'character_id' => $charId, 
                'role' => 'squad_member',
            ])->invoke('post', '/fleets/{fleet_id}/members/', [
                'fleet_id' => $fleetId,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
            return $e->getEsiResponse();
        }

        return null;
    }

    public function RenderFleetDisplay() {
        //
    }
}

?>