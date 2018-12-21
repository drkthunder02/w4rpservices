<?php

namespace App\Library\Fleets;

use Auth;
use Session;
use DB;

use App\Models\Esi\EsiToken;
use App\Models\Fleet\Fleet;
use App\Models\Fleet\FleetActivity;
use Carbon\Carbon;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class FleetHelper {

    private $fleetUri;
    private $fcId;

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
        $this->fleetUri = $fleetUri;
    }

    public function GetFleetUri() {
        return $this->fleetUri;
    }

    /**
     * Update fleet motd and free move.
     * 
     * @param fleetId
     */
    public function UpdateFleet($fleetId) {
        return null;
    }

    /**
     * Helper function to add a pilot to the fleet using his character id
     * 
     * @param fc
     * @param charId
     * @param fleetId
     */
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

    /**
     * Store Fleet Activity Tracking information into the database when caleld
     * 
     * @param fleetId
     */
    public function StoreFleetActivity($fleetId, $fcId) {
        //Get the ESI token for the FC to get the fleet composition
        $token = DB::table('EsiTokens')->where('character_id', $fcId)->get();
        //Disable all caching by setting the NullCache as the preferred cache handler.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;
        //Create the ESI Call Container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        $esi = new Eseye($authentication);

        try {
            $fleetComp = $esi->invoke('get', '/fleets/{fleet_id}/members/', [
                'fleet_id' => $fleetId,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
            return $e->getEsiResponse();
        }
        dd($fleetComp);
        foreach($fleetComp as $comp) {

        }

        return null;
    }
}

?>