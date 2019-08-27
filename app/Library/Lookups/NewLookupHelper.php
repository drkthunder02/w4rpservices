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
use App\Models\Lookups\CharacterLookup;
use App\Models\Lookups\CorporationLookup;
use App\Models\Lookups\AllianceLookup;

class NewLookupHelper {

    //Variables
    private $esi;

    //Construct
    public function __construct() {
        $this->esi = new Eseye();
    }

    public function GetCharacterInfo($charId) {
        try {
            $character = $this->esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }

        return $character;
    }

    public function CharacterIdToName($charId) {
        //Check if the character is stored in our own database first
        $char = $this->LookupCharacter($charId, null);
        //If the char is null, then we did not find the character in our own database
        if($char != null) {
            return $char->name;
        } else {
            //Get the character id from esi
            try {
                $character = $this->esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $charId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get character name from /characters/{character_id}/ in lookup helper.');
                return null;
            }
            
            if(isset($character->name)) {
                //Store the character name for the lookup table
                $this->StoreCharacterLookup(null, $character->name);
                //Return the character name to the calling function
                return $character->name;
            } else {
                //If we don't find any information return null
                return null;
            }
        }
    }

    public function CharacterNameToId($charName) {
        //Check if the character is stored in our own database first
        $char = $this->LookupCharacter(null, $charName);
        if($char != null) {
            return $char->character_id;
        } else {
            try {
                $response = $this->esi->setBody(array(
                    $charName,
                ))->invoke('post', '/universe/ids/');
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get character name from /universe/ids/ in lookup helper.');
                return null;
            }

            if(isset($response->characters[0]->id)) {
                $this->StoreCharacterLookup($response->characters[0]->id, null);
    
                return $response->characters[0]->id;
            } else {
                return -1;
            }            
        }
    }

    public function CorporationIdToName($corpId) {
        //Check if the corporation is stored in our own database first
        $corp = $this->LookupCorporation($corpId, null);
        if($corp != null) {
            return $corp->name;
        } else {
            //Try to get the corporation details from ESI
            try {
                $corporation = $this->esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corpId,
                ]);
            } catch(RequestFailedException $e) {
                //Log the issue
                Log::warning('Failed to get corporation name from /corporations/{corporation_id}/ in lookup helper.');
                return null;
            }

            if(isset($corporation->name)) {
                //Store the corporation name for the lookup table
                $this->StoreCorporationLookup(null, $corporation->name);
                //Return the corporation name to the calling function
                return $corporation->name;
            } else {
                //If nothing is found and ESI didn't work, return null to the calling function
                return null;
            }
        }
    }

    public function CorporationNameToId($corpName) {
        //Check if the corporation is stored in our own database first
        $corp = $this->LookupCorporation(null, $corpName);
        if($corp != null) {
            return $corp->corporation_id;
        } else {
            //Try to get the corporation details from ESI
            try {
                $corporation = $this->esi->setBody(array(
                    $corpName,
                ))->invoke('post', '/universe/ids/');
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get the corporation id from /universe/ids/ in lookup helper.');
                return null;
            }

            if(isset($response->corporations[0]->id)) {
                $this->StoreCorporationLookup($response->corporations[0]->id, null);

                return $response->corporations[0]->id;
            } else {
                return -1;
            }
        }
    }

    public function AllianceIdToName($allianceId) {
        //Check if the alliance is stored in our own database first
        $alliance = $this->LookupAlliance($allianceId, null);
        if($alliance != null) {
            return $alliance->alliance_id;
        } else {
            //Try to get the alliance details from ESI
            try {
                $alliance = $this->esi->invoke('get', '/alliances/{alliance_id}/', [
                    'alliance_id' => $allianceId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get the alliance name from /alliances/{alliance_id}/ in lookup helper.');
                return null;
            }

            if(isset($alliance->name)) {
                $this->StoreAllianceLookup(null, $alliance->name);

                return $alliance->name;
            } else {
                return null;
            }
        }
    }

    public function AllianceNameToId($allianceName) {
        //Check if the alliance is stored in our own database first
        $alliance = $this->LookupAlliance(null, $allianceName);
        if($alliance != null) {
            return $alliance->name;
        } else {
            //Try to get the alliance details from ESI
            try {
                $response = $this->esi->setBody(array(
                    $allianceName,
                ))->invoke('post', '/universe/ids/');
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get the alliance id from /universe/ids/ in lookup helper.');
                return null;
            }

            //If the data is pulled from ESI store the data, and send the data back to the calling function
            if(isset($response->alliances[0]->id)) {
                $this->StoreAllianceLookup($response->alliances[0]->id, null);

                return $response->alliances[0]->id;
            } else {
                return -1;
            }
        }
    }

    public function LookupCharacter($id = null, $name = null) {
        //If both the id and name are null, then there is nothing to lookup
        if($id == null & $name == null) {
            return null;
        }

        $character = null;

        //If the id is null attempt to lookup the character
        if($id != null) {
            $count = CharacterLookup::where(['character_id' => $id])->count();
            if($count > 0) {
                $character = CharacterLookup::where(['character_id' => $id])->first();
            } else {
                $character = null;
            }
        } else if($name != null) {
            //If the name is not null then attemp to lookup the character
            $count = CharacterLookup::where(['name' => $name])->count();
            if($count > 0) {
                $character = CharacterLookup::where(['name' => $name])->first();
            } else {
                $character = null;
            }
        }

        //Return the character details to the calling function
        return $character;
    }

    public function LookupCorporation($id = null, $name = null) {
        if($id == null && $name == null) {
            return null;
        }

        $corporation = null;

        //If the id is not null attempt to lookup the character
        if($id != null) {
            $count = CorporationLookup::where(['corporation_id' => $id])->count();
            if($count > 0) {
                $corporation = CorporationLookup::where(['corporation_id' => $id])->first();
            } else {
                $corporation = null;
            }
        } else if($name != null) {
            $count = CorporationLookup::where(['name' => $name])->count();
            if($count > 0) {
                $corporation = CorporationLookup::where(['name' => $name])->count();
            } else {
                $corporation = null;
            }
        }

        return $corporation;
    }

    public function LookupAlliance($id = null, $name = null) {
        if($id == null && $name == null) {
            return null;
        }

        $alliance = null;

        if($id != null) {
            $count = AllianceLookup::where(['alliance_id' => $id])->count();
            if($count > 0) {
                $alliance = AllianceLookup::where(['alliance_id' => $id])->first();
            } else {
                $alliance = null;
            }
        } else if($name != null) {
            $count = AllianceLookup::where(['name' => $name])->count();
            if($count > 0) {
                $alliance = AllianceLookup::where(['name' => $name])->first();
            } else {
                $alliance = null;
            }
        }

        return $alliance;
    }

    private function StoreCharacterLookup($id = null, $name = null) {
        if($id == null && $name == null) {
            return null;
        }

        
    }

    private function UpdateCharacter($id = null, $name = null) {

    }

    private function StoreCorporationLookup($id = null, $name = null) {

    }

    private function UpdateCorporation($id = null, $name = null) {

    }

    private function StoreAllianceLookup($id = null, $name = null) {

    }

    private function UpdateAlliance($id = null, $name = null) {

    }
}

?>