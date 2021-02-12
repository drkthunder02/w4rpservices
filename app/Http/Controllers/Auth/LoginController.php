<?php

namespace App\Http\Controllers\Auth;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

//Library
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use App\Library\Esi\Esi;

//Models
use App\Models\User\User;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;
use App\Models\Admin\AllowedLogin;
use App\Models\User\UserAlt;


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
    public function redirectToProvider($profile = null, Socialite $social) {
        
        return $social->driver('eveonline')
                         ->scopes([])
                         ->redirect();
        
        //return Socialite::driver('eveonline')->redirect();
    }

    /**
     * Get token from callback
     * Redirect to the dashboard if logging in successfully. 
     */
    public function handleProviderCallback(Socialite $social) {
        //Get the sso user from the socialite driver
        $ssoUser = $social->driver('eveonline')->user();

        dd($ssoUser);
        
        if(Auth::check()) {
            //If a refresh token is present, then we are doing a scope callback
            //to update scopes for an access token

            if(isset($ssoUser->refreshToken)) {
                //See if an access token is present already
                $tokenCount = EsiToken::where('character_id', $ssoUser->id)->count();
                if($tokenCount > 0) {
                    //Update the esi token
                    $this->UpdateEsiToken($ssoUser);
                } else {
                    //Save the ESI token
                    $this->SaveEsiToken($ssoUser);
                }

                //After creating the token, we need to update the table for scopes
                $this->SetScopes($ssoUser->user['Scopes'], $ssoUser->id);

                return redirect()->to('/dashboard')->with('success', 'Successfully updated ESI Scopes.');
            } else {
                $created = $this->createAlt($ssoUser);
                if($created == 1) {
                    return redirect()->to('/profile')->with('success', 'Alt registered.');
                } else {
                    return redirect()->to('/profile')->with('error', 'Alt was previously registered.');
                }
            }
        } else {
            $user = $this->createOrGetUser($ssoUser);

            auth()->login($user, true);

            return redirect()->to('/dashboard')->with('success', 'Successfully Logged In.');
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

     /**
     * Check if a user exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function createOrGetUser($eve_user) {
        $authUser = null;

        //Search to see if we have a matching user in the database.
        //At this point we don't care about the information
        $userCount = User::where('character_id', $eve_user->id)->count();
        
        //If the user is found, do more checks to see what type of login we are doing
        if($userCount > 0) {
            //Search for user in the database
            $authUser = User::where('character_id', $eve_user->id)->first();

            //Check to see if the owner has changed
            //If the owner has changed, then update their roles and permissions
            if($this->OwnerHasChanged($authUser->owner_hash, $eve_user->owner_hash)) {
                //Get the right role for the user
                $role = $this->GetRole(null, $eve_user->id);
                //Set the role for the user
                $this->SetRole($role, $eve_user->id);

                //Update the user information never the less.
                $this->UpdateUser($eve_user, $role);

                //Update the user's roles and permission
                $this->UpdatePermission($eve_user, $role);
            }

            //Return the user to the calling auth function
            return $authUser;
        } else {
            //Get the role for the character to be stored in the database
            $role = $this->GetRole(null, $eve_user->id);

            //Create the user account
            $user = $this->CreateNewUser($eve_user);

            //Set the role for the user
            $this->SetRole($role, $eve_user->id);

            //Create a user account
            return $user;
        }
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

    /**
     * Update avatar
     */
    private function UpdateAvatar($eve_user) {
        User::where('character_id', $eve_user->id)->update([
            'avatar' => $eve_user->avatar,
        ]);
    }

    /**
     * Update user permission
     */
    private function UpdatePermission($eve_user, $role) {
        UserPermission::where(['character_id' => $eve_user->id])->delete();
        $perm = new UserPermission();
        $perm->character_id = $eve_user->id;
        $perm->permission = $role;
        $perm->save();
    }

    /**
     * Update the user
     */
    private function UpdateUser($eve_user, $role) {
        User::where('character_id', $eve_user->id)->update([
            'avatar' => $eve_user->avatar,
            'owner_hash' => $eve_user->owner_hash,
            'role' => $role,
        ]);
    }

    /**
     * Create a new user account
     */
    private function CreateNewUser($eve_user) {
        $user = User::create([
            'name' => $eve_user->getName(),
            'avatar' => $eve_user->avatar,
            'owner_hash' => $eve_user->owner_hash,
            'character_id' => $eve_user->getId(),
            'inserted_at' => time(),
            'expires_in' => $eve_user->expiresIn,
            //'access_token' => $eve_user->token,
            'user_type' => $this->GetAccountType(null, $eve_user->id),
        ]);

        $token = EsiToken::create([
            'character_id' => $eve_user->id,
            'access_token' => $eve_user->token,
            'refresh_token' => null,
            'inserted_at' => time(),
            'expires_in' => $eve_user->expiresIn,
        ]);

        return $user;
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
        //Declare some variables
        $esiHelper = new Esi;

        //Instantiate a new ESI isntance
        $esi = $esiHelper->SetupEsiAuthentication();

        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Get the character information
        $character_info = $esiHelper->GetCharacterData($charId);

        //Get the corporation information
        $corp_info = $esiHelper->GetCorporationData($character_info->corporation_id);

        if($character_info == null || $corp_info == null) {
            return redirect('/')->with('error', 'Could not create user at this time.');
        }

        $legacy = AllowedLogin::where(['login_type' => 'Legacy'])->pluck('entity_id')->toArray();
        $renter = AllowedLogin::where(['login_type' => 'Renter'])->pluck('entity_id')->toArray();

        //Send back the appropriate group
        if(isset($corp_info->alliance_id)) {
            if($corp_info->alliance_id == '99004116') {
                return 'W4RP';
            } else if(in_array($corp_info->alliance_id, $legacy)) {
                return 'Legacy';
            } else if(in_array($corp_info->alliance_id, $renter)) {
                return 'Renter';
            } else {
                return 'Guest';
            }
        } else {
            return 'None';
        }
    }
}
