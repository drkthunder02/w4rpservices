<?php

/**
 * W4RP Services
 * GNU Public License
 * 
 */

namespace App\Library\Moons;

//Internal Library
use Log;
use DB;

//App Library
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//App Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;


class MiningLedgerHelper {

    private $charId;
    private $corpId;

    public function __construct($charId, $corpId) {
        $this->charId = $charId;
        $this->corpId = $corpID;
    }

    public function GetCorpMiningStructures() {
        //Declare variables
        $esiHelper = new Esi;

        //Check if the character has the correct ESI Scope.  If the character doesn't, then return false, but
        //also send a notice eve mail to the user.  The HaveEsiScope sends a mail for us.
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1')) {
            
            return null;
        }

        //Get the refresh token from the database and setup the esi authenticaiton container
        $esi = $esiHelper->SetupEsiAuthentication($esiHelper->GetRefreshToken($this->charId));

        //Get a list of the mining observers, which are structures
        try {
            $observers = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
                'corporation_id' => $this->corpId,
            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Could not find any mining observers for corporation: ' . $this->corpId);
            return null;
        }

        return $observers;
    }

    public function GetMiningStructureInfo($observerId) {
        //Declare variables
        $esiHelper = new Esi;

        //Check for ESI scopes
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-universe.read_structures.v1')) {
            return null;
        }

        //Get the refresh token and setup the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($esiHelper->GetRefreshToken($this->charId));

        //Try to get the structure information
        try {
            $info = $esi->invoke('get', '/universe/structures/{struture_id}/', [
                'structure_id' => $observerId,
            ]);
        } catch(RequestFailedExcept $e) {
            return null;
        }

        $system = $this->GetSolarSystemName($info->solar_system_id);

        return [
            'name' => $info->name,
            'system' => $system,
        ];
    }

    public function GetMiningLedger($observerId) {
        //Declare variables
        $esiHelper = new Esi;
        
        //Check for ESI Scopes
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-industry.read_corporation_mining.v1')) {
            return null;
        }

        //Get the refresh token and setup the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($esiHelper->GetRefreshToken($charId));

        //Get the mining ledger
        try {
            $ledger = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                'corporation_id' => $corpId,
                'observer_id' => $observerId,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }

        return $ledger;
    }

    private function GetSolarSystemName($systemId) {
        //Setup the esi helper variable
        $esiHelper = new Esi;

        //Setup the authentication container for ESI
        $esi = $esiHelper->SetupEsiAuthentication();

        //Attempt to get the solar system name from ESI
        try {
            $solar = $esi->invoke('get', '/universe/systems/{system_id}/', [
                'system_id' => $systemId,
            ]);
        } catch(RequestFailedException $e) {
            $solar = null;
        }

        if($solar != null) {
            return $solar->name;
        } else {
            return null;
        }
    }

}
?>