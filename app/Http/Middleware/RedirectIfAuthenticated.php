<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Socialite;
use DB;
use App\User;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {       
        //dd($request);
        if($request->pathInfo == '/login') {
            if (Auth::guard($guard)->check()) {
                return redirect('/dashboard');
            }
    
            return $next($request);
        } else if ($request->pathInfo == '/callback') {
            $ssoUser = Socialite::driver('eveonline')->user();
            $this->updateUser($ssoUser);

            return $next($request);
        } else {
            return $next($request);
        }
    }

    /**
     * Update the user information in the database
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function updateUser($eve_user) {
        $userFound = DB::table('users')->where('character_id', $eve_user->id)->first();
        if($userFound != null) {
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
        }
        
    }
}
