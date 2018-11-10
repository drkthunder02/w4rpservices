<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class RequireRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $confirmed = false;
        $ranking = array([
            'None' => 0,
            'Guest' => 1,
            'User' => 2,
            'Admin' => 3,
        ]);
        $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
        foreach($ranking as $rank => $value) {
            if($role === $check['role']) {
                $confirmed = true;
                break;
            } else {
                if($rank[$check['role']] > $rank[$check['role']]) {
                    $confirmed = true;
                    break;
                }
            }
        }

        abort_unless(auth()->check() && $confirmed, 403, "You don't have permissions to access this area!");

        return $next($request);
    }
}
