<?php

namespace App\Library\Esi;

//Internal Libraries
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Log;

//Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

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
            $subject = 'W4RP Services - Incorrect ESI Scope';
            $body = "Please register on https://services.w4rp.space with the scope: " . $scope;

            ProcessSendEveMailJob::dispatch($body, (int)$charId, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds(5));
            return false;
        }

        return true;
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

        return $token;
    }

    public function SetupEsiAuthentication($token = null) {
        //Get the platform configuration
        $config = config('esi');
        $currentTime = Carbon::now();

        //Declare some variables
        $authentication = null;
        $esi = null;

        if($token == null) {
            $esi = new Eseye();
        } else {
            $expires = $token->inserted_at + $token->expires_in;
            $token_expiration = Carbon::createFromTimestamp($expires)->toDateTimeString();
            
            //If the access token has expired, we need to do a request for a new access token
            if($currentTime > $token_expiration) {
                //Get the scopes to pass to the guzzle client
                $scopesArr = EsiScope::where([
                    'character_id' => $token->character_id,
                ])->get(['scope']);

                dd($scopesArr);
                //Create string separated by %20 = space from the array of scopes
                $scopes = implode(" ", $scopesArr);
                $scopes = str_replace(" ", "%20", $scopes);
                
                //Setup the new guzzle client
                $client = new Client();
                $response = $client->request('POST', 'https://login.eveonline.com/v2/oauth/token', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Host' => 'login.eveonline.com',
                        'Authorization' => "Basic " . base64_encode($config['client_id'] . ":" . $config['secret']),
                    ],
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $token->refresh_token,
                        'scope' => $scopes,
                    ]
                ]);

                dd($response);
            }

            $authentication = new EsiAuthentication([
                'client_id' => $config['client_id'],
                'secret' => $config['secret'],
                'refresh_token' => $token->refresh_token,
                'access_token' => $token->access_token,
                'token_expires' => $token_expiration,
            ]);

            //Setup the esi variable
            $esi = new Eseye($authentication);
        }

        //Return the created variable
        return $esi;
    }
}

?>