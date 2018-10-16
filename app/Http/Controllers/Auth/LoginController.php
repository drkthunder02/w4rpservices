<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
use App\User;

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
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

    public function redirectToProvider() {
        return Socialite::driver('eveonline')->setScopes(['publicData'])->redirect();
    }

    public function handleProviderCallback() {
        $ssoUser = Socialite::driver('eveonline')->user();

        $user = $this->createOrGetUser($ssoUser);

        auth()->login($user, true);

        return redirect()->to('/dashboard');
    }

     /**
     * Check if a user exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function createOrGetUser($eve_user) {
        //Search for user in the database
        $authUser = User::where('id', $eve_user->id)->first();
        if($authUser) {
            return $authUser;
        } else {
            //Get what type of account the user should have
            $accountType = $this->getAccountType($eve_user->refreshToken, $eve_user->getId());

            return User::create([
                'name' => $eve_user->getName(),
                'email' => null,
                'avatar' => $eve_user->avatar,
                'owner_hash' => $eve_user->owner_hash,
                'character_id'=> $eve_user->getId(),
                'expires_in' => $eve_user->expiresIn,
                'access_token' => $eve_user->token,
                'refresh_token' => $eve_user->refreshToken,
                'user_type' => $accountType,
            ]);
        }
    }

    private function getAccountType($refreshToken, $charId) {
        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Prepare an authentication container for ESI
        $authentication = new EsiAuthentication([
            'client_id' => env('EVEONLINE_CLIENT_ID'),
            'secret' => env('EVEONLINE_CLIENT_SECRET'),
            'refresh_token' => $refreshToken,
        ]);

        // Instantiate a new ESI instance
        $esi = new Eseye($authentication);

        //Get the character information
        $character_info = $esi->invoke('get', '/characters/{character_id}/', [
            'character_id' => $charId,
        ]);

        //Get the corporation information
        $corp_info = $esi->invoke('get', '/corporations/{corporation_id}/', [
            'corporation_id' => $character_info->corporation_id,
        ]);

        if($corp_info->alliance_id == '99004116') {
            return 'W4RP';
        } else if(in_array($alliance_info->alliance_id, array(99006297, 498125261, 99003214, 99004136, 9900237, 99001657, 99006069, 99001099, 99003838))) {
            return 'Legacy';
        } else {
            return 'Guest';
        }
    }
}
