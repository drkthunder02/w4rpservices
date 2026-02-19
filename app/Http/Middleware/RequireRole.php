<?php

namespace App\Http\Middleware;

use App\Models\User\AvailableUserRole;
use App\Models\User\UserRole;
use Closure;

class RequireRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $ranking = [];
        $roles = AvailableUserRole::all();

        foreach ($roles as $r) {
            $ranking[$r->role] = $r->rank;
        }

        $check = UserRole::where('character_id', auth()->user()->character_id)->get(['role']);

        if (! isset($check[0]->role)) {
            abort(403, "You don't have any roles.  You don't belong here.");
        }

        if ($ranking[$check[0]->role] < $ranking[$role]) {
            abort(403, "You don't have the correct role to be in this area.");
        }

        return $next($request);
    }
}
