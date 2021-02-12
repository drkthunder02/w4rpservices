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
        //The default scope is public data for everyone due to OAuth2 Tokens
        $scopes = ['publicData'];

        //Collect any other scopes we need if we are logged in.
        if(Auth::check()) {
            $extraScopes = EsiScope::where([
                'character_id' => auth()->user()->getId(),
            ])->get(['scope']);
            
            //Pop each scope onto the array of scopes
            foreach($extraScopes as $extra) {
                array_push($scopes, $extra->scope);
            }
        }

        //Place the scopes in the session to verify later after the redirect
        //has been completed and a token is received
        session()->put('scopes', $scopes);

        return $social->driver('eveonline')
                         ->scopes($scopes)
                         ->redirect();
    }

    /**
     * Redirect to the provider's website
     * 
     * @return Socialite
     */
    public function redirectToProviderAlt($profile = null, Socialite $social) {
        //The default scope is public data for everyone due to the OAuth2 Token
        $scopes = ['publicData'];

        //Let's put some information in the session to designate this is an alt call
        session()->put('altCall', true);

        dd($social);

        return $social->driver('eveonline')
                      ->scopes($scopes)
                      ->redirect();
    }

    /**
     * Get token from callback
     * Redirect to the dashboard if logging in successfully. 
     */
    public function handleProviderCallback(Socialite $social) {
        //Get the sso user from the socialite driver
        $ssoUser = $social->driver('eveonline')->user();

        $scpSession = session()->get('scopes');

        //If the user was already logged in, let's do some checks to see if we are adding
        //additional scopes to the user's account
        if(Auth::check()) {
            //If more scopes than just public Data are present on the return, then we are doing
            //a scope callback to update scopes for an access token
            if(sizeof($ssoUser->scopes) > 1) {
                //Check to see if an access token is present already for the character
                $tokenCount = EsiToken::where([
                    'character_id' => $ssoUser->id,
                ])->count();
                //If a token is present, we need to update it.
                if($tokenCount > 0) {
                    //Update the Esi Token
                    $this->UpdateEsiToken($ssoUser);
                } else {
                    //Save the ESI token as it is new
                    $this->SaveEsiToken($ssoUser);
                }

                //After either creating the new token or updating an old token, we need to set the scopes available for the token
                $this->SetScopes($ssoUser->scopes, $ssoUser->id);

                //Redirect to the dashboard with the correct message
                return redirect()->to('/dashboard')->with('success', 'Successfully updated ESI Scopes.');
            } else {
                //Retrieve the session data for altCall, and delete it from the session
                $alt = session()->pull('altCall');

                if($this->createAlt($ssoUser)) {
                    return redirect()->to('/profile')->with('success', 'Alt registered.');
                } else {
                    return redirect()->to('/profile')->with('error', 'Unable to register alt or it was previously registered.');
                }
            }
        } else {
            //If the user wasn't logged in, then create a new user
            $user = $this->createOrGetUser($ssoUser);
            //Login in the new user
            auth()->login($user, true);
            //Redirect back to the dashboard
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
    private function createOrGetUser($eveUser) {
        $authUser = null;

        //Search to see if we have a matching user in the database.
        //At this point we don't care about the information
        $userCount = User::where([
            'character_id' => $eveUser->id,
        ])->count();
        
        //If the user is found, do more checks to see what type of login we are doing
        if($userCount > 0) {
            //Search for user in the database
            $authUser = User::where([
                'character_id' => $eveUser->id,
            ])->first();

            //Check to see if the owner has changed
            //If the owner has changed, then update their roles and permissions
            if($this->OwnerHasChanged($authUser->owner_hash, $eveUser->owner_hash)) {
                //Get the right role for the user
                $role = $this->GetRole(null, $eveUser->id);
                //Set the role for the user
                $this->SetRole($role, $eveUser->id);

                //Update the user information never the less.
                $this->UpdateUser($eveUser, $role);

                //Update the user's roles and permission
                $this->UpdatePermission($eveUser, $role);
            }

            //Return the user to the calling auth function
            return $authUser;
        } else {
            //Get the role for the character to be stored in the database
            $role = $this->GetRole(null, $eveUser->id);

            //Create the user account
            $user = $this->CreateNewUser($eveUser);

            //Set the role for the user
            $this->SetRole($role, $eveUser->id);

            //Create a user account
            return $user;
        }
    }

    /**
     * Update the ESI Token
     */
    private function UpdateEsiToken($eveUser) {
        EsiToken::where('character_id', $eveUser->id)->update([
            'character_id' => $eveUser->getId(),
            'access_token' => $eveUser->token,
            'refresh_token' => $eveUser->refreshToken,
            'inserted_at' => time(),
            'expires_in' => $eveUser->expiresIn,
        ]);
    }

    /**
     * Create a new ESI Token in the database
     */
    private function SaveEsiToken($eveUser) {
        $token = new EsiToken;
        $token->character_id  = $eveUser->id;
        $token->access_token = $eveUser->token;
        $token->refresh_token = $eveUser->refreshToken;
        $token->inserted_at = time();
        $token->expires_in = $eveUser->expiresIn;
        $token->save();
    }

    /**
     * Update avatar
     */
    private function UpdateAvatar($eveUser) {
        User::where('character_id', $eveUser->id)->update([
            'avatar' => $eveUser->avatar,
        ]);
    }

    /**
     * Update user permission
     */
    private function UpdatePermission($eveUser, $role) {
        UserPermission::where(['character_id' => $eveUser->id])->delete();
        $perm = new UserPermission();
        $perm->character_id = $eveUser->id;
        $perm->permission = $role;
        $perm->save();
    }

    /**
     * Update the user
     */
    private function UpdateUser($eveUser, $role) {
        User::where('character_id', $eveUser->id)->update([
            'avatar' => $eveUser->avatar,
            'owner_hash' => $eveUser->owner_hash,
            'role' => $role,
        ]);
    }

    /**
     * Create a new user account
     */
    private function CreateNewUser($eveUser) {
        $user = User::create([
            'name' => $eveUser->getName(),
            'avatar' => $eveUser->avatar,
            'owner_hash' => $eveUser->owner_hash,
            'character_id' => $eveUser->getId(),
            'inserted_at' => time(),
            'expires_in' => $eveUser->expiresIn,
            'user_type' => $this->GetAccountType(null, $eveUser->id),
        ]);

        $token = EsiToken::create([
            'character_id' => $eveUser->id,
            'access_token' => $eveUser->token,
            'refresh_token' => $eveUser->refreshToken,
            'inserted_at' => time(),
            'expires_in' => $eveUser->expiresIn,
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
