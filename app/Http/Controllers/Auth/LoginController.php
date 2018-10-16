<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;

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

    public function handleProviderCallback(AuthAccountService $service) {
        $ssoUser = Socialite::driver('eveonline')->user();
        
        $user = $service->createOrGetUser($ssoUser);

        auth()->login($user);

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
        $authUser = User::where($eve_user->id)->first();
        //$account = AuthAccount::whereProvider('eveonline')->whereProviderUserId($eve_user->getId())->first();
        if($authUser) {
            return $authUser;
        } else {
            return User::create([
                'name' => $user->getName(),
                'email' => null,
                'avatar' => $user->avatar,
                'owner_hash' => $user->character_owner_hash,
                'id'=> $user->getId(),
                'expiresIn' => $user->expiresIn,
                'token' => $user->token,
                'refreshToken' => $user->refreshToken,
            ]);
        }
    }
}
