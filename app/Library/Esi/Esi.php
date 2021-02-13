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
        //Declare variables
        $currentTime = Carbon::now();
        $scopes = null;
        $i = 0;
        $config = config('esi');

        //If the program doesn't find an ESI Token, there is nothing to return
        if(EsiToken::where(['character_id' => $charId])->count() == 0) {
            return null;
        }

        //Get the ESI Token from the database
        $token = EsiToken::where([
            'character_id' => $charId,
        ])->first();

        //Check the expiration of the token to see if the token has expired and needs to be refreshed using the refresh token
        $expires = $token->inserted_at + $token->expires_in;
        $tokenExpiration = Carbon::createFromTimestamp($expires)->toDateTimeString();
        //If the access token has expired, we need to do a request for a new access token
        if($currentTime > $tokenExpiration) {
            //Get the current scopes of the token
            $scopesArr = EsiScope::where([
                'character_id' => $token->character_id,
            ])->get(['scope'])->toArray();

            //Cycle through the scopes, and create the string for scopes to send with the token
            foreach($scopesArr as $scp) {
                $scopes .= $scp['scope'];
                $i++;
                if($i < sizeof($scopesArr)) {
                    $scopes .= '%20';
                }
            }

            //Setup the guzzle client for the request to get a new token
            $client = new Client(['base_uri' => 'https://login.eveonline.com']);
            $response = $client->request('POST', '/v2/oauth/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Host' => 'login.eveonline.com',
                    'Authorization' => "Basic " . base64_encode($config['client_id'] . ":" . $config['secret']),
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $token->refresh_token,
                ],
            ]);
            //Decode the body of the response which has the token information
            $body = json_decode($response->getBody(), true);
            //Update the old token, then send the new token back to the calling function
            EsiToken::where([
                'character_id' => $charId,
            ])->update([
                'access_token' => $body['access_token'],
                'refresh_token' => $body['refresh_token'],
                'expires_in' => $body['expires_in'],
                'inserted_at' => time(),
            ]);

            $newToken = new EsiToken;
            $newToken->character_id = $charId;
            $newToken->access_token = $body['access_token'];
            $newToken->refresh_token = $body['refresh_token'];
            $newToken->inserted_at = time();
            $newToken->expires_in = $body['expires_in'];

            //Return the new token model
            return $newToken;
        } 
        
        //If we had a good token which has not expired yet, return the data
        return $token;
    }

    public function SetupEsiAuthentication($token = null) {
        //Declare some variables
        $authentication = null;
        $esi = null;
        $config = config('esi');

        if($token == null) {
            $esi = new Eseye();
        } else {
            $tokenExpires = $token->inserted_at + $token->expires_in;

            //Setup the esi authentication container
            $authentication = new EsiAuthentication([
                'client_id' => $config['client_id'],
                'secret' => $config['secret'],
                'refresh_token' => $token->refresh_token,
                'access_token' => $token->access_token,
                'token_expires' => $tokenExpires,
            ]);

            //Setup the esi variable
            $esi = new Eseye($authentication);
        }

        //Return the created variable
        return $esi;
    }
}

?>