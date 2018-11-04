<?php

namespace App\Http\Middleware;

use Closure;

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
        abort_unless(auth()->check() && auth()->user()->hasRole($role), 403, "You don't have permissions to access this area!");

        return $next($request);
    }
}
