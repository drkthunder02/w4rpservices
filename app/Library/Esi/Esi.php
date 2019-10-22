<?php

namespace App\Library\Esi;

//Internal Libraries
use DB;
use Carbon\Carbon;

//Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Jobs\JobSendEveMail;
use App\Models\Mail\EveMail;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Seat Stuff
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

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
        //Get the esi config
        $config = config('esi');

        //Check for an esi scope
        $check = EsiScope::where(['character_id' => $charId, 'scope' => $scope])->count();
        if($check == 0) {
            //Compose a mail to send to the user if the scope is not found
            $mail  = new EveMail;
            $mail->sender = $config['primary'];
            $mail->subject = 'W4RP Services - Incorrect ESI Scope';
            $mail->body = "Please register on https://services.w4rp.space with the scope: " . $scope;
            $mail->recipient = (int)$charId;
            $mail->recipient_type = 'character';

            ProcessSendEveMailJob::dispatch($mail)->onQueue('mail')->delay(Carbon::now()->addSeconds(5));
            return false;
        }

        return true;
    }

    public function GetCharacterData($charId) {
        $esi = new Eseye();
        try {
            $character = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }
        
        return $character;
    }

    public function GetCharacterName($charId) {
        $esi = new Eseye();
        try {
            $character = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }
        
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

        try {
            $character = $esi->setBody(array(
                $name,
            ))->invoke('post', '/universe/ids/');
        } catch(RequestFailedException $e) {
            return null;
        }

        if(isset($character->characters[0]->id)) {
            return $character->characters[0]->id;
        } else {
            return null;
        }
        /*
        try {
            $character = $esi->setQueryString([
                'categories' => 'character',
                'language' => 'en-us',
                'search' => $name,
                'strict' => 'true',
            ])->invoke('get', '/search/');
        } catch(RequestFailedException $e) {
            return null;
        }
        

        $character = json_decode($character, true);

        return $character['character'];
        */
    }

    public function FindCorporationId($charId) {
        $esi = new Eseye();
        try {
            $character = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }
        
        return $character->corporation_id;
    }

    public function FindCorporationName($charId) {
        $esi = new Eseye();
        try {
            $character = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
    
            $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                'corporation_id' => $character->corporation_id,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }
        

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

    public function GetRefreshToken($charId) {
        //Get the refresh token from the database
        $tokenCount = EsiToken::where([
            'character_id' => $charId,
        ])->count();

        //If the token is not found, then don't return it.
        if($tokenCount == 0) {
            return null;
        }

        $token = EsiToken::where([
            'character_id' => $charId,
        ])->first();

        return $token->refresh_token;
    }

    public function SetupEsiAuthentication($token = null) {
        //Get the platform configuration
        $config = config('esi');

        //Declare some variables
        $authentication = null;

        if($token === null) {
            $authentication = new EsiAuthentication([
                'client_id' => $config['client_id'],
                'secret' => $config['secret'],
            ]);
        } else {
            $authentication = new EsiAuthentication([
                'client_id' => $config['client_id'],
                'secret' => $config['secret'],
                'refresh_token' => $token,
            ]);
        }

        //Setup the esi variable
        $esi = new Eseye($authentication);

        //Return the created variable
        return $esi;
    }
}

?>