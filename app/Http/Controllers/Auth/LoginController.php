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
        if($authUser) {
            if($eve_user->refreshToken !== null) {
                DB::table('users')->where('character_id', $eve_user->id)->update([
                    'name' => $eve_user->getName(),
                    'email' => null,
                    'avatar' => $eve_user->avatar,
                    'owner_hash' => $eve_user->owner_hash,
                    'character_id' => $eve_user->getId(),
                    'inserted_at' => time(),
                    'expires_in' => $eve_user->expiresIn,
                    'access_token' => $eve_user->token,
                    'refresh_token' => $eve_user->refreshToken,
                    'scopes' => $eve_user->user['Scopes'],
                ]);
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
                } else {
                    //Save the ESI Token in the database
                    $token = new App\Models\EsiToken;
                    $token->character_id  = $eve_user->id;
                    $token->access_token = $eve_user->token;
                    $token->refresh_token = $eve_user->refreshToken;
                    $token->expires_in = $eve_user->expiresIn;
                    $token->save();
                    /*
                    DB::table('EsiTokens')->insert([
                        'character_id' => $eve_user->getId(),
                        'access_token' => $eve_user->token,
                        'refresh_token' => $eve_user->refreshToken,
                        'expires_in' => $eve_user->expiresIn,
                    ]);
                    */
                }
                //After creating the token, we need to update the table for scopes
                //First we look for all the scopes, then if need be add entries or delete entries from the database
                DB::table('UserEsiScopes')->where('character_id', $eve_user->id)->delete();
                //EsiScopes::where('character_id', $eve_user->id)->delete();
                $scopes = explode(' ', $eve_user->user['Scopes']);
                foreach($scopes as $scope) {
                    $data = new App\Models\EsiScope;
                    $data->character_id = $eve_user->id;
                    $data->scopoe = $scope;
                    $data->save();
                    /*
                    DB::table('UserEsiScopes')->insert([
                        'character_id' => $eve_user->id,
                        'scope' => $scope,
                    ]);
                    */
                }
            } else {
                DB::table('users')->where('character_id', $eve_user->id)->update([
                    'name' => $eve_user->getName(),
                    'email' => null,
                    'avatar' => $eve_user->avatar,
                    'owner_hash' => $eve_user->owner_hash,
                    'character_id' => $eve_user->getId(),
                ]);
            }

            return $authUser;
        } else {
            //Get what type of account the user should have
            $accountType = $this->getAccountType(null, $eve_user->getId());
            if($accountType == 'Guest') {
                $role = 'Guest';
            } else if($accountType == 'Legacy'){
                $role = 'User';
            } else if($accountType == 'W4RP') {
                $role = 'User';
            } else {
                $role = 'None';
            }

            //Create a user account
            return User::create([
                'name' => $eve_user->getName(),
                'email' => null,
                'avatar' => $eve_user->avatar,
                'owner_hash' => $eve_user->owner_hash,
                'character_id'=> $eve_user->getId(),
                'expires_in' => $eve_user->expiresIn,
                'access_token' => $eve_user->token,
                'user_type' => $accountType,
                'role' => $role,
            ]);
        }
    }
    
    /**
     * Gets the appropriate account type the user should be assigned through ESI API
     * 
     * @param refreshToken
     * @param charId
     * 
     * @return text
     */
    private function getAccountType($refreshToken, $charId) {
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
        if($corp_info->alliance_id == '99004116') {
            return 'W4RP';
        } else if(in_array($alliance_info->alliance_id, array(99006297, 
                                                              498125261, 
                                                              99003214, 
                                                              99004136, 
                                                              9900237, 
                                                              99001657, 
                                                              99006069, 
                                                              99001099, 
                                                              99003838))) {
            return 'Legacy';
        } else {
            return 'Guest';
        }
    }
}
