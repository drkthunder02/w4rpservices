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
        if(strpos($permission, 'role.')  !== false) {
            $confirmed = $this->CheckRole($permission);
        } else {
            $confirmed = $this->CheckPermission($permission);
        }

        if($confirmed === false) {
            abort(403, "You don't have permission to access this area.");
        }

        return $next($request);
    }

    private function CheckPermission($permission) {
        $confirmed = false;

        $checks = UserPermission::where(['character_id' => auth()->user()->character_id, 'permission' => $permission])->get(['permission']);
        foreach($checks as $check) {
            if($check === $permission) {
                return true;
            }
        }

        return false;
    }

    private function CheckRole($role) {
        $confirmed = false;

        $ranking = [
            'role.none' => 0,
            'role.guest' => 1,
            'role.user' => 2,
            'role.director' => 3,
            'role.admin' => 4,
        ];
        //Using eloquent let's get the roles for the character
        $checks = UserPermission::where('character_id', auth()->user()->character_id)->get(['permission']);

        foreach($checks as $check) {
            if(!isset($check->permission)) {
                abort(403, "You don't have the correct role to view this area.");
            }

            if(strpos($role, 'role.') !== false) {
                if($ranking[$check->permission] >= $ranking[$role]) {
                    $confirmed = true;
                }
            }
        }

        return $confirmed;
    }
}
