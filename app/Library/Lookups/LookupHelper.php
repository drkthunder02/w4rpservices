<?php

namespace App\Library\Lookups;

use DB;

use App\Models\User\UserToCorporation;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class LookupHelper {

    //Add characters to the lookup table for quicker lookups without having
    //to hit the ESI all the time
    public function LookupCharacter($charId) {
        //Check for the character in the user_to_corporation table
        $found = UserToCorporation::where('character_id', $charId)->get(['corporation_id']);

        //If we don't find the character in the table, then we retrieve from ESI
        //and add the character to the table
    }

    //Update the character lookup table as often as necessary
    public function UpdateLookupCharacter() {

    }

    //Add corporations to the lookup table for quicker lookups without having to
    //hit the ESI API all the time
    public function LookupCorporation($corpId) {

    }

    //Update the corporation lookup table as often as necessary
    public function UpdateLookupCorporation() {

    }
}

?>