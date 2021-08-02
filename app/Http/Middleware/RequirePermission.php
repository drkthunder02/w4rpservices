<?php

namespace App\Http\Middleware;

use Closure;
use DB;

use App\Models\User\UserPermission;
use App\Models\User\UserRole;

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
        $role = UserRole::where([
            'character_id' => auth()->user()->character_id,
        ])->get(['role']);

        if($role[0]->role != "Admin") {
            $perms = UserPermission::where(['character_id' => auth()->user()->character_id, 'permission'=> $permission])->get(['permission']);

            abort_unless(auth()->check() && isset($perms[0]->permission), 403, "You don't have the correct permission to be in this area.");
        }       

        return $next($request);
    }
}
