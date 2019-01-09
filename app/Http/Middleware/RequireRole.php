<?php

namespace App\Http\Middleware;

use Closure;
use DB;

use App\Models\User\UserPermission;

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
        $ranking = [
            'role.none' => 0,
            'role.guest' => 1,
            'role.user' => 2,
            'role.director' => 3,
            'role.admin' => 4,
        ];
        //Using eloquent let's get the roles for the character
        $check = UserPermission::where(['character_id' => auth()->user()->character_id, 'permission' => $role])->get(['permission']);
        
        if(!isset($check[0]->role)) {
            abort(403, "You don't have permissions to access this area!");    
        }

        if($ranking[$check[0]->role] === $ranking[$role]) {
            $confirmed = true;
        }
        if($ranking[$check[0]->role] >= $ranking[$role]) {
            $confirmed = true;
        }

        abort_unless(auth()->check() && $confirmed, 403, "You don't have permissions to access this area!");

        return $next($request);
    }
}
