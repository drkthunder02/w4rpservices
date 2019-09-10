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
use App\Library\Lookups\NewLookupHelper;

//App Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;


class MiningLedgerHelper {

    private $charId;
    private $corpId;

    /**
     * Constructor function
     * 
     * @var $charId
     * @var $corpId
     */
    public function __construct($charId, $corpId) {
        $this->charId = $charId;
        $this->corpId = $corpID;
    }

    /**
     * Get the corporation's mining structures.
     * These structures consist of Athanors and Tataras
     * 
     * @return array
     */
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

    /**
     * Get the mining struture information
     * 
     * @return array
     */
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

    /**
     * Get the mining ledger for a particular structure
     * 
     * @var observerId
     * @return array
     */
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

    /**
     * Process the mining ledger into something more readable for humans
     * 
     * @var array
     * @return array
     */
    public function ProcessMiningLedger($ledger, $date) {
        //Declare some variables
        $items = array();
        $notSorted = array();
        $final = array();
        $lookup = new NewLookupHelper;


        //In the first iteration of the array get rid of the extra items we don't want
        foreach($ledger as $ledg) {
            if($ledg->last_updated == $date) {
                array_push($items, $ledg);
            }
        }

        //Sort through the array and replace character id with name and item id with name
        foreach($items as $item) {
            $charName = $lookup->CharacterIdToName($item->character_id);
            $typeName = $this->GetItemName($item->type_id);
            $corpName = $lookup->CorporationIdToName($item->recorded_corporation_id);

            if(isset($final[$charName])) {
                $final[$charName] = [
                    'ore' => $typeName,
                    'quantity' => $item->quantity,
                    'date' => $item->last_updated,
                ];
            } else {
                $temp = [
                    'ore' => $typeName,
                    'quantity' => $item->quantity,
                    'date' => $item->last_updated,
                ];

                array_push($final[$charName], $temp);
            }
        }

        return $final;
    }

    /**
     * Get the type id and return the name of the ore
     * 
     * @var typeId
     * @return string
     */
    private function GetItemName($typeId) {
        //Setup the esi helper variable
        $esiHelper = new Esi;

        //Setup the authentication container for ESI
        $esi = $esiHelper->SetupEsiAuthentication();

        try {
            $item = $esi->invoke('get', '/universe/types/{type_id}/', [
                'type_id' => $typeId,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }

        return $item->name;
    }

    /**
     * Get the solar system name
     * 
     * @var systemId
     * @return string
     */
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