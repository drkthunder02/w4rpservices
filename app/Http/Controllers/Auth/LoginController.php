<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;

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
        dd($ssoUser);
        $user = $service->createOrGetUser($ssoUser);

        auth()->login($user);

        return redirect()->to('/dashboard');
    }
}
