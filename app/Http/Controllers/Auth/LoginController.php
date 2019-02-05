<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;
use Auth;

use App\User;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;
use App\Models\Admin\AllowedLogin;

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
                    User::where('character_id', $eve_user->id)->update([
                        'avatar' => $eve_user->avatar,
                        'owner_hash' => $eve_user->owner_hash,
                        'role' => $role,
                    ]);
                    //Update the user's roles and permission
                    UserPermission::where(['character_id' => $eve_user->id])->delete();
                    $perm = new UserPermission();
                    $perm->character_id = $eve_user->id;
                    $perm->permission = $role;
                    $perm->save();
                } else {
                    //Update the user information never the less.
                    User::where('character_id', $eve_user->id)->update([
                        'avatar' => $eve_user->avatar,
                    ]);
                }
                
                //See if we have an access token for the user.
                //If we have a token update the token, if not create an entry into the database
                $token = EsiToken::where('character_id', $eve_user->id)->first();
                if($token) {
                    //Update the ESI Token
                    EsiToken::where('character_id', $eve_user->id)->update([
                        'character_id' => $eve_user->getId(),
                        'access_token' => $eve_user->token,
                        'refresh_token' => $eve_user->refreshToken,
                        'expires_in' => $eve_user->expiresIn,
                    ]);
                } else {  //If a token entry is not found, then we create a new token entry into the database
                    //Save the ESI Token in the database
                    $token = new EsiToken;
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
                User::where('character_id', $eve_uesr->id)->update([
                    'avatar' => $eve_user->avatar,
                ]);
            }
            //Return the user to the calling auth function
            return $authUser;
        } else {
            //Get the role for the character to be stored in the database
            $role = $this->GetRole(null, $eve_user->id);

            //Create the user account
            $user = User::create([
                'name' => $eve_user->getName(),
                'email' => null,
                'avatar' => $eve_user->avatar,
                'owner_hash' => $eve_user->owner_hash,
                'character_id'=> $eve_user->getId(),
                'expires_in' => $eve_user->expiresIn,
                'access_token' => $eve_user->token,
                'user_type' => $this->GetAccountType(null, $eve_user->id),
            ]);

            //Set the role for the user
            $this->SetRole($role, $eve_user->id);

            //Create a user account
            return $user;
        }
    }

    /**
     * Set the user role in the database
     * 
     * @param role
     * @param charId
     */
    private function SetRole($role, $charId) {
        $permission = new UserRole;
        $permission->character_id = $charId;
        $permission->role = $role;
        $permission->save();
    }

    /**
     * Set the user scopes in the database
     * 
     * @param scopes
     * @param charId
     */
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
        } elseif($accountType == 'Renter') {
            $role = 'Renter';
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

        $legacy = AllowedLogin::where(['login_type' => 'Legacy'])->get(['entity_id']);
        $renter = AllowedLogin::where(['login_type' => 'Renter'])->get(['entity_id']);

        //Send back the appropriate group
        if(isset($corp_info->alliance_id)) {
            if($corp_info->alliance_id == '99004116') {
                return 'W4RP';
            } else if(in_array($corp_info->alliance_id, $legacy)) {
                return 'Legacy';
            } else if(in_arry($corp_info->alliance_id, $renter)) {
                return 'Renter';
            } else {
                return 'Guest';
            }
        } else {
            return 'None';
        }
    }
}
