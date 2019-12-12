<?php

namespace App\Library\Lookups;

//Internal Libraries
use DB;
use Log;

//Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\Lookups\CharacterToCorporation;
use App\Models\Lookups\CorporationToAlliance;
use App\Models\Lookups\CharacterLookup;
use App\Models\Lookups\CorporationLookup;
use App\Models\Lookups\AllianceLookup;
use App\Models\Lookups\SolarSystem;
use App\Models\Lookups\ItemLookup;

class LookupHelper {

    //Variables
    private $esi;

    //Construct
    public function __construct() {
        //Declare a variable for use by the construct
        $esiHelper = new Esi;

        $this->esi = $esiHelper->SetupEsiAuthentication();
    }

    public function ItemIdToName($itemId) {
        //Check if the item is stored in our own database first
        $item = $this->LookupItem($itemId);

        //If the item is found, return it, otherwise, do some esi to find it.
        if($item != null) {
            return $item->name;
        } else {
            try {
                $response = $this->esi->invoke('get', '/universe/types/{type_id}/', [
                    'type_id' => $itemId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get item information from /universe/types/{type_id}/ in LookupHelper.');
                return null;
            }

            if(isset($response->description)) {
                $this->StoreItem($response);

                return $response->name;
            } else {
                return null;
            }
        }
    }

    private function LookupItem($itemId) {
        $item = ItemLookup::where([
            'type_id' => $itemId,
        ])->first();

        return $item;
    }

    private function StoreItem($item) {
        $newItem = new Item;
        if(isset($item->capacity)) {
            $newItem->capacity = $item->capacity;
        }
        $newItem->description = $item->description;
        if(isset($item->graphic_id)) {
            $newItem->graphic_id = $item->graphic_id;
        }
        $newItem->group_id = $item->group_id;
        if(isset($item->icon_id)) {
            $newItem->icon_id = $item->icon_id;
        }
        if(isset($item->market_group_id)) {
            $newItem->market_group_id = $item->market_group_id;
        }
        if(isset($item->mass)) {
            $newItem->mass = $item->mass;
        }
        $newItem->name = $item->name;
        if(isset($item->packaged_volume)) {
            $newItem->packaged_volume = $item->packaged_volume;
        }
        if(isset($item->portion_size)) {
            $newItem->portion_size = $item->portion_size;
        }
        $newItem->published = $item->published;
        if(isset($item->radius)) {
            $newItem->radius = $item->radius;
        }
        $newItem->type_id = $item->type_id;
        if(isset($item->volume)) {
            $newItem->volume = $item->volume;
        }
        $newItem->save();
    }


    public function SystemNameToId($system) {
        //Check if the solar system is stored in our own database first
        $solarSystem = $this->LookupSolarSystem($system);

        if($solarSystem != null) {
            return $solarSystem->solar_system_id;
        } else {
            try {
                $response = $this->esi->setBody(array(
                    $system,
                ))->invoke('post', '/universe/ids/');
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get system name from /universe/ids/ in lookup helper.');
                return null;
            }
    
            if(isset($response->systems)) {
                $this->StoreSolarSystem($response->systems[0]);

                return $response->systems[0]->id;
            } else {
                return null;
            }
        }
    }

    private function LookupSolarSystem($system) {
        $solar = SolarSystem::where([
            'name' => $system,
        ])->first();

        return $solar;
    }

    private function StoreSolarSystem($system) {
        $solar = new SolarSystem;
        $solar->name = $system->name;
        $solar->solar_system_id = $system->id;
        $solar->save();
    }

    public function GetCharacterInfo($charId) {
        //Check our own database first
        $char = $this->LookupCharacter($charId, null);

        //if the character was not found in the database, then get the information and store it in our database for later
        if($char == null) {
            try {
                $response = $this->esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $charId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get character information in GetCharacterInfo in Lookup');
                return null;
            }
            var_dump($response);
            dd();

            //Store the character in our database
            $this->SaveCharacter($response, $charId);

            //Return the character details to the calling function
            return $character;
        } else {
            //Return what was pulled from the database
            return $char;
        }
    }

    public function GetCorporationInfo($corpId) {
        //Check our own database first
        $corp = $this->LookupCorporation($corpId, null);

        //If the corporation was not found in the database, then get the information and store it in our database for later
        if($corp == null) {
            try {
                $corporation = $this->esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corpId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get corporation information in GetCorporationInfo in Lookup');
                return null;
            }
        } else {
            //Return what was pulled from the database
            return $corp;
        }
    }

    public function GetAllianceInfo($allianceId) {
        //Check our own database first
        $ally = $this->LookupAlliance($corpId, null);

        if($ally == null) {
            try {
                $alliance = $this->esi->invoke('get', '/alliances/{alliance_id}/', [
                    'alliance_id' => $allianceId,
                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get alliance information in GetAllianceInfo in Lookup');
                return null;
            }
        } else {
            return $ally;
        }
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
                return null;
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
                return null;
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
                return null;
            }
        }
    }

    private function LookupCharacter($id = null, $name = null) {
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

    private function LookupCorporation($id = null, $name = null) {
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
                $corporation = CorporationLookup::where(['name' => $name])->first();
            } else {
                $corporation = null;
            }
        }

        return $corporation;
    }

    private function LookupAlliance($id = null, $name = null) {
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
        //Declare the esi helper
        $esiHelper = new Esi;

        //If the id and name are null, just return
        if($id == null && $name == null) {
            return;
        }

        //If the id isn't null, then get the character information from the esi via the character id
        if($id != null) {
            //See if the character already exists in the lookup table
            $count = CharacterLookup::where(['character_id' => $id])->count();
            if($count == 0) {
                try {
                    $response = $this->esi->invoke('get', '/characters/{character_id}/', [
                        'character_id' => $id,
                    ]);
                } catch(RequestFailedException $e) {
                    return;
                }

                $corpId = $this->SaveCharacter($response, $id);

                if($corpId != null) {
                    //Do a recursive call for the corporation Lookup
                    $this->StoreCorporationLookup($corpId, null);
                }                
            } else {
                return;
            }
        } else {
            return;
        }
        
        //If the name is not null attempt to add the character to the table
        if($name != null) {
            $count = CharacterLookup::where(['name' => $name])->count();
            if($count == 0) {
                try {
                    //Get the character id from the ESI API
                    $responseName = $this->esi->setBody(array(
                        $name,
                    ))->invoke('post', '/universe/ids/');
                } catch(RequestFailedException $e) {
                    return;
                }

                try {
                    $response = $this->esi->invoke('get', '/characters/{character_id}/', [
                        'character_id' => $responseName->characters[0]->id,
                    ]);
                } catch(RequestFailedException $e) {
                    return;
                }

                $corpId = $this->SaveCharacter($response, $responseName->characters[0]->id);
                if($corpId != null) {
                    //Do a recursive call for the corporation Lookup
                    $this->StoreCorporationLookup($corpId, null);
                }
                
            } else {
                return;
            }
        } else {
            return;
        }
    }

    private function SaveCharacter($response, $charId) {
        $esiHelper = new Esi;

        $char = new CharacterLookup;

        $char->character_id = $charId;
        if(isset($response->alliance_id)) {
            $char->alliance_id = $response->alliance_id;
        }
        if(isset($response->ancestry_id)) {
            $char->ancestry_id = $response->ancestry_id;
        }
        $char->birthday = $esiHelper->DecodeDate($response->birthday);
        $char->bloodline_id = $response->bloodline_id;
        $char->corporation_id = $response->corporation_id;
        if(isset($response->description)) {
            $char->description = $response->description;
        }
        if(isset($response->faction_id)) {
            $char->faction_id = $response->faction_id;
        }
        $char->gender = $response->gender;
        $char->name = $response->name;
        $char->race_id = $response->race_id;
        if(isset($response->security_status)) {
            $char->security_status = $response->security_status;
        }
        if(isset($response->title)) {
            $char->title = $response->title;
        }
        $char->save();

        return $response->corporation_id;
    }

    public function UpdateCharacters() {
        $all = CharacterLookup::all();

        foreach($all as $entry) {
            //Attempt to get the data from ESI
            try {
                $response = $this->esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $entry->character_id,
                ]);
            } catch(RequestFailedException $e) {

            }

            //Update the data
            if(isset($response->alliance_id)) {
                if($response->alliance_id != $entry->alliance_id) {
                    CharacterLookup::where([
                        'character_id' => $entry->character_id,
                    ])->update([
                        'alliance_id' => $response->alliance_id,
                    ]);
                }
            }
            if(isset($response->description)) {
                if($response->description != $entry->description) {
                    CharacterLookup::where([
                        'character_id' => $entry->character_id,
                    ])->update([
                        'description' => $response->description,
                    ]);
                }
            }
            if(isset($response->security_status)) {
                if($response->security_status != $entry->security_status) {
                    CharacterLookup::where([
                        'character_id' => $entry->character_id,
                    ])->update([
                        'security_status' => $response->security_status,
                    ]);
                }
            }
            if(isset($response->title)) {
                if($response->title != $entry->title) {
                    CharacterLookup::where([
                        'character_id' => $entry->character_id,
                    ])->update([
                        'title' => $response->title,
                    ]);
                }
            }
            if(isset($response->corporation_id)) {
                if($response->corporation_id != $entry->corporation_id) {
                    CharacterLookup::where([
                        'character_id' => $entry->character_id,
                    ])->update([
                        'corporation_id' => $response->corporation_id,
                    ]);
                }
            }
        }
    }

    private function StoreCorporationLookup($id = null, $name = null) {
        //Declare the esi helper
        $esiHelper = new Esi;

        //If the id is null and the name is null, then return
        if($id == null && $name == null) {
            return;
        }

        if($id != null) {
            $count = CorporationLookup::where(['corporation_id' => $id])->count();
            if($count == 0) {
                try {
                    $response = $this->esi->invoke('get', '/corporations/{corporation_id}/', [
                        'corporation_id' => $id,
                    ]);
                } catch(RequestFailedException $e) {
                    return;
                }

                $allianceId = $this->SaveCorporation($response, $id);

                if($allianceId != null) {
                    $this->StoreAllianceLookup($allianceId);
                }
            } else {
                return;
            }
        } else {
            return;
        }

        if($name != null) {
            $count = CorporationLookup::where(['name' => $name])->count();
            if($count == 0) {
                try {
                    //Get the corporation id from the ESI API
                    $responseName = $this->esi->setBody(array(
                        $name,
                    ))->invoke('post', '/universe/ids/');
                } catch(RequestFailedException $e) {
                    return;
                }

                try {
                    $response = $this->esi->invoke('get', '/corporations/{corporation_id}/', [
                        'corporation_id' => $responseName->corporations[0]->id,
                    ]);
                } catch(ReqeustFailedException $e) {
                    return;
                }

                $allianceId = $this->SaveCorporation($response, $responseName->corporations[0]->id);
                if($allianceId != null) {
                    //Do a recursive call for the alliance lookup
                    $this->StoreAllianceLookup($allianceId, null);
                }
            } else {
                return;
            }
        } else {
            return;
        }
    }

    private function SaveCorporation($response, $corpId) {
        $esiHelper = new Esi;

        $corp = new CorporationLookup;
        $corp->corporation_id = $corpId;
        if(isset($response->alliance_id)) {
            $corp->alliance_id = $response->alliance_id;
        }
        $corp->ceo_id = $response->ceo_id;
        $corp->creator_id = $response->creator_id;
        if(isset($response->date_founded)) {
            $corp->date_founded = $esiHelper->DecodeDate($response->date_founded);
        }
        if(isset($response->description)) {
            $corp->description = $response->description;
        }
        if(isset($response->faction_id)) {
            $corp->faction_id = $response->faction_id;
        }
        if(isset($response->home_station_id)) {
            $corp->home_station_id = $response->home_station_id;
        }
        $corp->member_count = $response->member_count;
        $corp->name = $response->name;
        if(isset($response->shares)) {
            $corp->shares = $response->shares;
        }
        $corp->tax_rate = $response->tax_rate;
        $corp->ticker = $response->ticker;
        if(isset($response->url)) {
            $corp->url = $response->url;
        }
        if(isset($response->war_eligible)) {
            $corp->war_eligible = $response->war_eligible;
        }
        $corp->save();

        if(isset($response->alliance_id)) {
            return $response->alliance_id;
        } else {
            return null;
        }

    }

    public function UpdateCorporations() {
        $all = CorporationLookup::all();

        foreach($all as $entry) {
            try {
                $response = $this->esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $entry->corporation_id,
                ]);
            } catch(RequestFailedException $e) {

            }

            if(isset($response->alliance_id)) {
                if($response->alliance_id != $entry->alliance_id) {
                    CorporationLookup::where([
                        'corporation_id' => $entry->corporation_id,
                    ])->update([
                        'alliance_id' => $response->alliance_id,
                    ]);
                }

                if(isset($response->description)) {
                    if($response->description != $entry->description) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'description' => $response->description,
                        ]);
                    }
                }

                if(isset($response->faction_id)) {
                    if($response->faction_id != $entry->faction_id) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'faction_id' => $response->faction_id,
                        ]);
                    }
                }

                if(isset($response->home_station_id)) {
                    if($response->home_station_id != $entry->home_station_id) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'home_station_id' => $response->home_station_id,
                        ]);
                    }
                }

                if(isset($response->member_count)) {
                    if($response->member_count != $entry->member_count) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'member_count' => $response->member_count,
                        ]);
                    }
                }

                if(isset($response->tax_rate)) {
                    if($response->tax_rate != $entry->tax_rate) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'tax_rate' => $response->tax_rate,
                        ]);
                    }
                }

                if(isset($response->url)) {
                    if($response->url != $entry->url) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'url' => $response->url,
                        ]);
                    }
                }

                if(isset($response->war_eligible)) {
                    if($response->war_eligible != $entry->war_eligible) {
                        CorporationLookup::where([
                            'corporation_id' => $entry->corporation_id,
                        ])->update([
                            'war_eligible' => $response->war_eligible,
                        ]);
                    }
                }
            }
        }
    }

    private function StoreAllianceLookup($id = null, $name = null) {
        //Declare the esi helper
        $esiHelper = new Esi;

        //Check if the passed variables are null
        if($id == null && $name == null) {
            return;
        }

        //If the id isn't null then attempt to populate the table
        if($id != null) {
            //See if the alliance already exists in the table
            $count = AllianceLookup::where(['alliance_id' => $id])->count();
            if($count == 0) {
                try {
                    $response = $this->esi->invoke('get', '/alliances/{alliance_id}/', [
                        'alliance_id' => $id,
                    ]);
                } catch(RequestFailedException $e) {
                    return;
                }

                $this->SaveAlliance($response, $id);
            }
        }

        //If the name isn't null then attempt to populate the table
        if($name != null) {
            $count = AllianceLookup::where(['name' => $name])->count();
            if($count == 0) {
                try {
                    $responseName = $this->esi->setBody(array(
                        $name,
                    ))->invoke('post', '/universe/ids/');
                } catch(RequestFailedException $e) {
                    return;
                }

                try {
                    $response = $this->esi->invoke('get', '/alliances/{alliance_id}/', [
                        'alliance_id' => $responseName->alliances[0]->id,
                    ]);
                } catch(RequestFailedException $e) {
                    return;
                }

                $this->SaveAlliance($response, $responseName->alliances[0]->id);
            }
        }
    }

    private function SaveAlliance($response, $allianceId) {
        $esiHelper = new Esi;

        $alliance = new AllianceLookup;
        $alliance->alliance_id = $allianceId;
        $alliance->creator_corporation_id = $response->creator_corporation_id;
        $alliance->creator_id = $response->creator_id;
        $alliance->date_founded =  $esiHelper->DecodeDate($response->date_founded);
        if(isset($response->executor_corporation_id)) {
            $alliance->executor_corporation_id = $response->executor_corporation_id;
        }
        if(isset($response->faction_id)) {
            $alliance->faction_id = $response->faction_id;
        }
        $alliance->name = $response->name;
        $alliance->ticker = $response->ticker;
        $alliance->save();
    }

    public function UpdateAlliances() {
        $all = AllianceLookup::all();
        
        foreach($all as $entry) {
            try {
                $response = $this->esi->invoke('get', '/alliances/{alliance_id}/', [
                    'alliance_id' => $entry->alliance_id,
                ]);
            } catch(RequestFailedException $e) {

            }

            if(isset($response->executor_corporation_id)) {
                if($response->executor_corporation_id != $entry->executor_corporation_id) {
                    AllianceLookup::where([
                        'alliance_id' => $entry->alliance_id,
                    ])->update([
                        'executor_corporation_id' => $response->executor_corporation_id,
                    ]);
                }
            }

            if(isset($response->faction_id)) {
                if($response->faction_id != $entry->faction_id) {
                    AllianceLookup::where([
                        'alliance_id' => $entry->alliance_id,
                    ])->update([
                        'faction_id' => $response->faction_id,
                    ]);
                }
            }
        }
    }
}

?>