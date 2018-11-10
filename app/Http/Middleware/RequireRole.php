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
        $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
        printf($check);
        printf($role);
        if($check === $role) {
            $confirmed = true;
        } else {
            $confirmed = false;
        }

        abort_unless(auth()->check() && $confirmed, 403, "You don't have permissions to access this area!");

        return $next($request);
    }
}
