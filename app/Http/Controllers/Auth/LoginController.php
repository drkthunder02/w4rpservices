<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
use App\User;

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
        $authUser = User::where('id', $eve_user->id)->first();
        if($authUser) {
            return $authUser;
        } else {
            
            return User::create([
                'name' => $eve_user->getName(),
                'email' => null,
                'avatar' => $eve_user->avatar,
                'owner_hash' => $eve_user->owner_hash,
                'character_id'=> $eve_user->getId(),
                'expires_in' => $eve_user->expiresIn,
                'access_token' => $eve_user->token,
                'refresh_token' => $eve_user->refreshToken,
            ]);
        }
    }
}
