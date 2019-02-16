<?php

namespace App\Library\Lookups;

use DB;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException; 

use App\Models\Lookups\CharacterToCorporation;
use App\Models\Lookups\CorporationToAlliance;

class LookupHelper {

    //Create a character id from a character name
    public function CharacterNameToId($character) {
        //Setup Eseye Configuration
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;
        //Setup class variables
        $esi = new Eseye();

        //Attempt to find the character name in the LookupCharacter table to see if we can match it to an id
        $charId = CharacterToCorporation::where(['character_name' => $character])->get(['character_id']);
        if($charId == null) {
            //Get the character id from the ESI API.
            $response = $esi->setQueryString([
                'categories' => 'character',
                'search' => $character,
                'strict' => 'true',
            ])->invoke('get', '/search/');

            $this->LookupCharacter($response->character[0]);

            return $response->character[0];
        } else {
            return $charId[0]->character_id;
        }
    }

    //Add characters to the lookup table for quicker lookups without having
    //to hit the ESI all the time
    public function LookupCharacter($charId) {
        //Check for the character in the user_to_corporation table
        $found = CharacterToCorporation::where('character_id', $charId)->get(['corporation_id']);
        
        //If we don't find the character in the table, then we retrieve from ESI
        //and add the character to the table
        if(!isset($found[0]->corporation_id)) {
            //Get the configuration for ESI from the environmental variables
            $config = config('esi');

            //Setup a new ESI container
            $esi = new Eseye();

            //Try to get the character information, then the corporation information
            try {
                $character = $esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $charId,
                ]);
                $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $character->corporation_id,
                ]);
            } catch(RequestFailedException $e){
                return null;
            }

            //Save all of the data to the database
            $char = new CharacterToCorporation;
            $char->character_id = $charId;
            $char->character_name = $character->name;
            $char->corporation_id = $character->corporation_id;
            $char->corporation_name = $corporation->name;
            $char->save();
            //Return the corporation_id which is what the calling function is looking for
            return $character->corporation_id;
        } else {
            //Return the corporation_id if it was found in the database as it is what the calling function is looking for
            return $found;
        }
    }

    //Update the character lookup table as often as necessary
    public function UpdateLookupCharacter() {
        //Create a new ESI container
        $esi = new Eseye();
        
        //Get all of the data from the database and start performing updates
        $dbChars = CharacterToCorporation::all();
        foreach($dbChars as $char) {
            //Attempt to get the data from the ESI API
            try{
                $charcter = $esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $char->character_id,
                ]);
            } catch(RequestFailedException $e) {
                return $e->getEsiResponse();
            }
            
            //Check the response versus the database
            if($char->corporation_id != $character->corporation_id) {
                try {
                    $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                        'corporation_id' => $character->corporation_id,
                    ]);
                } catch(RequestFailedException $e) {
                    return $e->getEsiResponse();
                }
                CharacterToCorporation::where(['character_id' => $char->character_id])
                                    ->update([
                                        'corporation_id' => $character->corporation_id,
                                        'corporation_name' => $corporation->name,
                                    ]);
            }
        }
    }

    //Add corporations to the lookup table for quicker lookups without having to
    //hit the ESI API all the time
    public function LookupCorporation($corpId) {
        //Check for the character in the user_to_corporation table
        $found = CorporationToAlliance::where('corporation_id', $charId)->get(['alliance_id']);

        //If we don't find the character in the table, then we retrieve from ESI
        //and add the character to the table
        if(!isset($found[0]->alliance_id)) {
            //Get the configuration for ESI from the environmental variables
            $config = config('esi');

            //Setup a new ESI container
            $esi = new Eseye();

            //Try to get the character information, then the corporation information
            try {
                $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corpId,
                ]);
                $alliance = $esi->invoke('get', '/alliances/{alliance_id}/', [
                    'alliance_id' => $corporation->alliance_id,
                ]);
            } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
                return $e->getEsiResponse();
            }

            //Save all of the data to the database
            $char = new UserToCorporation;
            $char->character_id = $corpId;
            $char->character_name = $corporation->name;
            $char->corporation_id = $corporation->corporation_id;
            $char->corporation_name = $corporation->name;
            $char->save();
            //Return the corporation_id which is what the calling function is looking for
            return $corporation->alliance_id;
        } else {
            //Return the corporation_id if it was found in the database as it is what the calling function is looking for
            return $found[0]->alliance_id;
        }
    }

    //Update the corporation lookup table as often as necessary
    public function UpdateLookupCorporation() {
        //Create a new ESI container
        $esi = new Eseye();
        
        //Get all of the data from the database and start performing updates
        $dbCorps = CorporationToAlliance::all();
        foreach($dbCorps as $corp) {
            //Attempt to get the data from the ESI API
            try{
                $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corp->corporation_id,
                ]);
            } catch(RequestFailedException $e) {
                return $e->getEsiResponse();
            }
            
            //Check the response versus the database
            if($corp->corporation_id != $corporation->corporation_id) {
                try {
                    $alliance = $esi->invoke('get', '/alliancess/{alliance_id}/', [
                        'alliance_id' => $corporation->alliance_id,
                    ]);
                } catch(RequestFailedException $e) {
                    return $e->getEsiResponse();
                }
                CorporationToAlliance::where(['corporation_id' => $char->character_id])
                                    ->update([
                                        'alliance_id' => $corporation->alliance_id,
                                        'alliance_name' => $alliance->name,
                                    ]);
            }
        }
    }
}

?>