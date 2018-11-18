<?php

use DB;

use App\Models\EsiScope;

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

    public function FindCharacterId($name) {
        $config = config('esi');
        //Create the esi authentication container
        $authentication = new EsiAuthentication([
            'client_id' => $config['esi']['client_id'],
            'secret' => $config['esi']['secret'],
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

}

?>