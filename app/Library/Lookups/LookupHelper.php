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

    public function CharacterName($charId) {
        $esi = new Eseye();

        try {
            $character = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestFailedException $e){
            return null;
        }

        return $character->name;
    }

    //Create a character id from a character name
    public function CharacterNameToId($character) {
        //Setup class variables
        $esi = new Eseye();

        //Attempt to find the character name in the LookupCharacter table to see if we can match it to an id
        $count = CharacterToCorporation::where(['character_name' => $character])->count();
        if($count === 0) {
            //Format the name
            $name = str_replace(' ', '%20', $character);

            try {
                //Get the character id from the ESI API.
                $response = $esi->setBody([
                    'categories' => 'character',
                    'search' => $name,
                    'strict' => 'true',
                ])->invoke('get', '/search/');
            } catch(RequestFailedException $e) {

            }

            dd($response->raw);

            if(isset($response->character)) {
                $this->LookupCharacter($response->character);

                return $response->character;
            }

        } else {
            $char = CharacterToCorporation::where(['character_name' => $character])->get(['character_id']);

            return $char[0]->character_id;
        }
    }

    //Add characters to the lookup table for quicker lookups without having
    //to hit the ESI all the time
    public function LookupCharacter($charId) {
        //Check for the character in the user_to_corporation table
        $count = CharacterToCorporation::where('character_id', $charId)->count();
        
        //If we don't find the character in the table, then we retrieve from ESI
        //and add the character to the table
        if($count == 0) {
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
            $found = CharacterToCorporation::where('character_id', $charId)->get(['corporation_id']);

            //Return the corporation_id if it was found in the database as it is what the calling function is looking for
            return $found[0]->corporation_id;
        }
    }

    public function LookupCorporationId($charId) {
        //Check for the character in the user_to_corporation table
        $count = CharacterToCorporation::where('character_id', $charId)->count();
        
        //If we don't find the character in the table, then we retrieve from ESI
        //and add the character to the table
        if($count == 0) {
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
            $found = CharacterToCorporation::where('character_id', $charId)->get(['corporation_id']);

            //Return the corporation_id if it was found in the database as it is what the calling function is looking for
            return $found[0]->corporation_id;
        }
    }

    /**
     * Function to retrieve a corporation name from the lookup tables
     * or add the details of the corporation if it's not found
     * 
     */
    public function LookupCorporationName($corpId) {
        //check for the character in the user_to_corporation table
        $count = CorporationToAlliance::where('corporation_id', $corpId)->count();

        //If we don't find the corporation in the table, then we need to retrieve it from ESI
        //and add the corporation to the table
        if($count == 0) {
            //Get the configuration for ESI from the environmental variables
            $config = config('esi');

            //Setup a new ESI container
            $esi = new Eseye();

            try {
                $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corpId,
                ]);
            } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
                return $e->getEsiResponse();
            }

            //Return the corporation name
            return $corporation->name;
        } else {
            $found = CorporationToAlliance::where('corporation_id', $corpId)->get(['corporation_name']);

            return $found[0]->corporation_name;
        }
    }

    //Add corporations to the lookup table for quicker lookups without having to
    //hit the ESI API all the time
    public function LookupCorporation($corpId) {
        //Check for the character in the user_to_corporation table
        $count = CorporationToAlliance::where('corporation_id', $corpId)->count();
       
        //If we don't find the character in the table, then we retrieve from ESI
        //and add the character to the table
        if($count == 0) {
            //Get the configuration for ESI from the environmental variables
            $config = config('esi');

            //Setup a new ESI container
            $esi = new Eseye();

            //Try to get the character information, then the corporation information
            try {
                $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corpId,
                ]);
                if(isset($corporation->alliance_id)) {
                    $alliance = $esi->invoke('get', '/alliances/{alliance_id}/', [
                        'alliance_id' => $corporation->alliance_id,
                    ]);
                } else {
                    return -1;
                }
                
            } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
                return -1;
            }
            
            //Save all of the data to the database
            $corp = new CorporationToAlliance;
            $corp->corporation_id = $corpId;
            $corp->corporation_name = $corporation->name;
            $corp->alliance_id = $corporation->alliance_id;
            $corp->alliance_name = $alliance->name;
            $corp->save();

            //Return the corporation_id which is what the calling function is looking for
            return $corporation->alliance_id;
        } else {
            $found = CorporationToAlliance::where('corporation_id', $corpId)->get(['alliance_id']);

            //Return the corporation_id if it was found in the database as it is what the calling function is looking for
            return $found[0]->alliance_id;
        }
    }

    public function LookupAllianceTicker($allianceId) {
        //Get the configuration for ESI from the environmental variable
        $config = config('esi');

        //Setup a new ESI container
        $esi = new Eseye();

        try {
            $alliance = $esi->invoke('get', '/alliances/{alliance_id}/', [
                'alliance_id' => $allianceId,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
            return $e->getEsiResponse();
        }

        return $alliance->ticker;
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