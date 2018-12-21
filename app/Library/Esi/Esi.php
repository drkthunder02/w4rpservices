<?php

namespace App\Library\Esi;

use DB;

use App\Models\Esi\EsiScope;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

/**
 * This class represents a few ESI helper functions for the program
 */
class Esi {

    /**
     * Check if a scope is in the database for a particular character
     * 
     * @param charId
     * @param scope
     * 
     * @return true,false
     */
    public function HaveEsiScope($charId, $scope) {
        //Check for an esi scope
        $checks = DB::table('EsiScopes')->where('character_id', $charId)->get();
        foreach($checks as $check) {
            if($check->scope === $scope) {
                return true;
            }
        }

        return false;
    }

    public function GetCharacterName($charId) {
        $esi = new Eseye();
        $character = $esi->invoke('get', '/characters/{character_id}/', [
            'character_id' => $charId,
        ]);

        return $character->name;
    }

    public function FindCharacterId($name) {
        $config = config('esi');
        //Create the esi authentication container
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
        ]);
        //Create the esi container
        $esi = new Eseye($authentication);
        $character = $esi->setQueryString([
            'categories' => 'character',
            'language' => 'en-us',
            'search' => $name,
            'strict' => 'true',
        ])->invoke('get', '/search/');

        $character = json_decode($character, true);

        return $character['character'];
    }

    public function FindCorporationId($charId) {
        $esi = new Eseye();
        $character = $esi->invoke('get', '/characters/{character_id}/', [
            'character_id' => $charId,
        ]);

        return $character->corporation_id;
    }

    public function FindCorporationName($charId) {
        $esi = new Eseye();
        $character = $esi->invoke('get', '/characters/{character_id}/', [
            'character_id' => $charId,
        ]);

        $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
            'corporation_id' => $character->corporation_id,
        ]);

        return $corporation->name;
    }

    public function DecodeDate($date) {
        //Find the end of the date
        $dateEnd = strpos($date, "T");
        //Split the string up into date and time
        $dateArr = str_split($date, $dateEnd);
        //Trim the T and Z from the end of the second item in the array
        $dateArr[1] = ltrim($dateArr[1], "T");
        $dateArr[1] = rtrim($dateArr[1], "Z");
        //Combine the date
        $realDate = $dateArr[0] . " " . $dateArr[1];

        //Return the combined date in the correct format
        return $realDate;
    }
}

?>