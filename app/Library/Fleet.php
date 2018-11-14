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
        $uris  = explode('https://esi.tech.cpp.is/v1/fleets/', $fleetUri);
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

    public function AddPilot($fc, $charId, $fleetId) {
         //Check if the fc has the right scope
        if(!$this->HaveEsiScope($fc, 'esi-fleets.write_fleet.v1')) {
            return 'Incorrect Scopes.';
        }
        
        //Get the ESI token for the FC to add the new pilot
        $token = DB::table('EsiTokens')->where('character_id', $fc)->first();
        // Disable all caching by setting the NullCache as the
        // preferred cache handler. By default, Eseye will use the
        // FileCache.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;
        $configuration->logfile_location = '/var/www/w4rpservices/storage/eseye';
        //Create the ESI Call Container
        $authentication = new EsiAuthentication([
            'client_id' => env('ESI_CLIENT_ID'),
            'secret' => env('ESI_SECRET_KEY'),
            'refresh_token' => $token->refresh_token,
        ]);
        //Crate the ESI Class
        $esi = new Eseye($authentication);
        //Setup the body of the esi message
        $esi->setBody(['character_id' => $charId, 'role' => 'squad_member']);
        //Perform the call to ESI
        try {
            $esi->invoke('post', '/fleets/{fleet_id}/members/', [
                'fleet_id' => $fleetId,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
             // The HTTP Response code and message can be retreived
            // from the exception...
            print $e->getCode() . PHP_EOL;
            print $e->getMessage() . PHP_EOL;

            // .. or from the EsiResponse available from the Exception
            print $e->getEsiResponse()->getErrorCode() . PHP_EOL;
            print $e->getEsiResponse()->error() . PHP_EOL;

            // You can also access the *actual* response we got from
            // ESI as a normal array.
            print_r($e->getEsiResponse());
            dd($e->getEsiResponse());
        }

        return 'Invite Sent';
    }

    public function RenderFleetDisplay() {
        //
    }

    private function HaveEsiScope($charId, $scope) {
        //Check for an esi scope
        $checks = DB::table('EsiScopes')->where('character_id', $charId)->get();
        foreach($checks as $check) {
            if($check->scope === $scope) {
                return true;
            }
        }

        return false;
    }
}

?>