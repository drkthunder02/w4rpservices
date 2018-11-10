<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;
use Auth;
use DB;

use App\User;
use App\Models\EsiScope;
use App\Models\EsiToken;
use App\Models\UserRole;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 
                                            'handleProviderCallback', 
                                            'redirectToProvider']);
    }

    /**
     * Logout function
     * 
     * @return void
     */
    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Redirect to the provider's website
     * 
     * @return Socialite
     */
    public function redirectToProvider() {
        return Socialite::driver('eveonline')->redirect();
    }

    /**
     * Get token from callback
     * Redirect to the dashboard if logging in successfully. 
     */
    public function handleProviderCallback() {
        $ssoUser = Socialite::driver('eveonline')->user();
        $user = $this->createOrGetUser($ssoUser);

        auth()->login($user, true);

        return redirect()->to('/dashboard')->with('success', 'Successfully Logged In or Updated ESI.');
    }

     /**
     * Check if a user exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function createOrGetUser($eve_user) {
        //Search for user in the database
        $authUser = User::where('character_id', $eve_user->id)->first();
        //If the user is found, do more checks to see what type of login we are doing
        if($authUser) {
            //if a refresh token is present, then we are doing a scope callback to update scopes for an access token
            if($eve_user->refreshToken !== null) {
                //Check if the owner hash has changed to call the user type if it needs to be updated
                if($this->OwnerHasChanged($authUser->owner_hash, $eve_user->owner_hash)) {
                    //Get the right role for the user
                    $role = $this->GetRole(null, $eve_user->id);
                    //Set the role for the user
                    $this->SetRole($role, $eve_user->id);

                    //Update the user information never the less.
                    DB::table('users')->where('character_id', $eve_user->id)->update([
                        'avatar' => $eve_user->avatar,
                        'owner_hash' => $eve_user->owner_hash,
                        'role' => $role,
                    ]);
                } else {
                    //Update the user information never the less.
                    DB::table('users')->where('character_id', $eve_user->id)->update([
                        'avatar' => $eve_user->avatar,
                    ]);
                }
                
                //See if we have an access token for the user.
                //If we have a token update the token, if not create an entry into the database
                $token = EsiToken::where('character_id', $eve_user->id)->first();
                if($token) {
                    //Update the ESI Token
                    DB::table('EsiTokens')->where('character_id', $eve_user->id)->update([
                        'character_id' => $eve_user->getId(),
                        'access_token' => $eve_user->token,
                        'refresh_token' => $eve_user->refreshToken,
                        'expires_in' => $eve_user->expiresIn,
                    ]);
                } else {  //If a token entry is not found, then we create a new token entry into the database
                    //Save the ESI Token in the database
                    $token = new App\Models\EsiToken;
                    $token->character_id  = $eve_user->id;
                    $token->access_token = $eve_user->token;
                    $token->refresh_token = $eve_user->refreshToken;
                    $token->expires_in = $eve_user->expiresIn;
                    $token->save();
                }

                //After creating the token, we need to update the table for scopes
                //First we look for all the scopes, then if need be add entries or delete entries from the database
                $this->SetScopes($eve_user->user['Scopes'], $eve_user->id);

            } else {
                //If the user is already in the database, but no refresh token was present in the callback, then just update the user
                DB::table('users')->where('character_id', $eve_user->id)->update([
                    'avatar' => $eve_user->avatar,
                ]);
            }
            //Return the user to the calling auth function
            return $authUser;
        } else {
            //Get the role for the character to be stored in the database
            $role = $this->GetRole(null, $eve_user->id);
            //Set the role for the user
            $this->SetRole($role, $eve_user->id);

            //Create a user account
            return User::create([
                'name' => $eve_user->getName(),
                'email' => null,
                'avatar' => $eve_user->avatar,
                'owner_hash' => $eve_user->owner_hash,
                'character_id'=> $eve_user->getId(),
                'expires_in' => $eve_user->expiresIn,
                'access_token' => $eve_user->token,
                'user_type' => $this->GetAccountType(null, $eve_user->id),
            ]);
        }
    }

    /**
     * Set the user role in the database
     * 
     * @param role
     * @param charId
     */
    private function SetRole($role, $charId) {
        //Insert the role into the database
        $roles = new \App\Models\UserRole;
        $roles->character_id = $charId;
        $roles->role = $role;
        $roles->save();
    }

    /**
     * Set the user scopes in the database
     * 
     * @param scopes
     * @param charId
     */
    private function SetScopes($scopes, $charId) {
        //Delete the current scopes, so we can add new scopes into the database
        DB::table('EsiScopes')->where('character_id', $charId)->delete();
        $scopes = explode(' ', $scopes);
        foreach($scopes as $scope) {
            $data = new \App\Models\EsiScope;
            $data->character_id = $charId;
            $data->scope = $scope;
            $data->save();
        }
    }

    /**
     * Get the current owner hash, and compare it with the new owner hash
     * 
     * @param hash
     * @param charId
     */
    private function OwnerHasChanged($hash, $newHash) {
        if($hash === $newHash) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the account type and returns it
     * 
     * @param refreshToken
     * @param character_id
     */
    private function GetRole($refreshToken, $charId) {
        $accountType = $this->GetAccountType($refreshToken, $charId);
        if($accountType == 'Guest') {
            $role = 'Guest';
        } else if($accountType == 'Legacy'){
            $role = 'User';
        } else if($accountType == 'W4RP') {
            $role = 'User';
        } else {
            $role = 'None';
        }

        return $role;
    }
    
    /**
     * Gets the appropriate account type the user should be assigned through ESI API
     * 
     * @param refreshToken
     * @param charId
     * 
     * @return text
     */
    private function GetAccountType($refreshToken, $charId) {
        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        // Instantiate a new ESI instance
        $esi = new Eseye();

        //Get the character information
        $character_info = $esi->invoke('get', '/characters/{character_id}/', [
            'character_id' => $charId,
        ]);

        //Get the corporation information
        $corp_info = $esi->invoke('get', '/corporations/{corporation_id}/', [
            'corporation_id' => $character_info->corporation_id,
        ]);
        //Send back the appropriate group
        if(isset($corp_info->alliance_id)) {
            if($corp_info->alliance_id == '99004116') {
                return 'W4RP';
            } else if(in_array($corp_info->alliance_id, array(99006297,   //Drone Walkers
                                                              498125261,  //Test Alliance Please Ignore
                                                              99003214,   //Brave Collective
                                                              99004136,   //Dangerous Voltage
                                                              99002367,   //Evictus    
                                                              99001657,   //Rezada Regnum
                                                              99006069,   //Tactical Supremacy
                                                              99001099,   //The Watchmen.
                                                              99006297,   //Drone Walkers
                                                              99003838))  //Requiem Eternal
                                                              ) {
                return 'Legacy';
            } else {
                return 'Guest';
            }
        } else {
            return 'None';
        }
    }
}
