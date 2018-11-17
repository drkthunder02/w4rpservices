<?php

use App\Models\EsiScope;

use DB;

class Esi {

    /**
     * Check if a scope is in the database for a particular character
     * 
     * @param charId
     * @param scope
     * 
     * @return true,false
     */
    public function CheckScope($charId, $scope) {
        $checks = DB::table('EsiScopes')->where('character_id', $charId)->get();
        foreach($checks as $check) {
            if($check === $scope){
                return true;
            }
        }

        return false;
    }

}

?>