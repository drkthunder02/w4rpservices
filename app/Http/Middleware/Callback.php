<?php

namespace App\Http\Middleware;

//Internal Library
use Closure;
use Illuminate\Support\Facades\Auth;
use Socialite;
use DB;

//Libraries
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

//Models
use App\Models\User\User;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

class Callback
{
    /**
     * Handle an incoming request for callback.  Set to handle the request after the 
     * login controller does what it needs to do.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $ssoUser)
    {
        $response = $next($request);

        if(isset($ssoUser->refreshToken)) {
            //See if an access token is present for the user
            $tokenCount = EsiToken::where(['character_id' => $ssoUser->id])->count();
            if($tokenCount > 0) {
                //Update the esi token
                $this->UpdateEsiToken($ssoUser);
            } else {
                //Save the esi token
                $this->SaveEsiToken($ssoUser);
            }

            //After creating or updating the token, update the table for the scopes.
            $this->SetScopes($ssoUser->user['Scopes'], $ssoUser->id);
        } else {
            $created = $this->createAlt($ssoUser);

        }

        return $response;
    }

    /**
     * Update the ESI Token
     */
    private function UpdateEsiToken($eve_user) {
        EsiToken::where('character_id', $eve_user->id)->update([
            'character_id' => $eve_user->getId(),
            'access_token' => $eve_user->token,
            'refresh_token' => $eve_user->refreshToken,
            'inserted_at' => time(),
            'expires_in' => $eve_user->expiresIn,
        ]);
    }

    /**
     * Create a new ESI Token in the database
     */
    private function SaveEsiToken($eve_user) {
        $token = new EsiToken;
        $token->character_id  = $eve_user->id;
        $token->access_token = $eve_user->token;
        $token->refresh_token = $eve_user->refreshToken;
        $token->inserted_at = time();
        $token->expires_in = $eve_user->expiresIn;
        $token->save();
    }

    private function SetScopes($scopes, $charId) {
        //Delete the current scopes, so we can add new scopes into the database
        EsiScope::where('character_id', $charId)->delete();
        $scopes = explode(' ', $scopes);
        foreach($scopes as $scope) {
            $data = new EsiScope;
            $data->character_id = $charId;
            $data->scope = $scope;
            $data->save();
        }
    }

    /**
     * Check if an alt exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function createAlt($user) {
        $altCount = UserAlt::where('character_id', $user->id)->count();
        if($altCount == 0) {
            $newAlt = new UserAlt;
            $newAlt->name = $user->getName();
            $newAlt->main_id = auth()->user()->getId();
            $newAlt->character_id = $user->id;
            $newAlt->avatar = $user->avatar;
            $newAlt->access_token = $user->token;
            $newAlt->owner_hash = $user->owner_hash;
            $newAlt->inserted_at = time();
            $newAlt->expires_in = $user->expiresIn;
            $newAlt->save();
            return 1;
        } else {
            return 0;
        }
    }
}


