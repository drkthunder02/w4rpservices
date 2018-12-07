<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use App\Models\User\UserPermission;

class RequirePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $confirmed = false;

        $check = UserPermission::where(['character_id' => auth()->user()->character_id, 'permission' => $permission])->get(['permission']);
        if(!isset($check[0]->permission)) {
            abort(403, "You don't have permission to access this area.");
        }

        return $next($request);
    }
}
