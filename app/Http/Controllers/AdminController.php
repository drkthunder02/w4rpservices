<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\User\AvailableUserPermission;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function displayDashboard() {
        //Get the users from the database to allow a selection of users for
        //adding and removing roles and permissions
        $users = User::pluck('name')->all();
        $permissions = AvailableUserPermission::pluck('permission')->all();

        $user = array();
        $permission = array();

        foreach($users as $key => $value) {
            $user[$value] = $value;
        }

        foreach($permissions as $key => $value) {
            $permission[$value] = $value;
        }

        $data = [
            'users' => $user,
            'permissions' => $permission,
        ];

        return view('admin.dashboard')->with('data', $data);
    }

    public function addPermission(Request $request) {
        //Get the user and permission from the form
        $user = $request->user;
        $permission = $request->permission;

        //Get the character id from the username using the user table
        $character = User::where(['name' => $user])->get();
        //Check to see if the character already has the permission
        $check = DB::table('user_permissions')->where(['character_id' => $character->character_id, 'permission' => $permission])->get();
        if(!isset($check[0])) {
            $perm = new UserPermission;
            $perm->character_id = $character->character_id;
            $perm->permission = $permission;
            $perm->save();

            return redirect('/admin/dashboard')->with('success', 'User udpated!');
        } else {
            return redirect('/admin/dashboard')->with('error', 'User not updated or already has the permission.');
        }   
    }

    public function removePermission(Request $request) {
        //Get the user and permission to be removed from the form
        $user = $request->user;
        $permission = $request->permission;
        //Get the character id from the username using the user table
        $character = DB::table('users')->where('name', $user)->first();
        //Check if the permission exists in the table
        $check = DB::table('user_permissions')->where(['character_id' => $character->character_id, 'permission' => $permission])->get();
        if($check !== null) {
            DB::table('user_permissions')->where(['character_id' => $character->character_id,
                                                  'permission' => $permission])
                                         ->delete();
            return view('admin.dashboard')->with('success', 'User Updated.');
        } else {
            return view('admin.dashboard')->with('error', 'User did not have the permission.');
        }
    }

    public function addRole(Request $request) {
        //Get the user and role from the form
        $user = $request->user;
        $role = $request->role;
        //Get the character id from the username using the user table
        $character = DB::table('users')->where('name', $user)->first();
        //Delete the current roles from the database to start with a clean state
        DB::table('user_roles')->where(['character_id' => $character->character_id])->delete();

        $userRoles = new UserRole;
        $userRoles->character_id = $character->character_id;
        $userRoles->role = $role;
        $userRoles->save();

        //Return the view and the message of user updated
        return view('admin.dashboard')->with('success', 'User Updated.');
    }

    public function removeRole(Request $request) {
        //Get the user and role from the form
        $user = $request->user;
        $role = $request->role;
        //Get the character id from teh username using the user table
        $character = DB::table('users')->where('name', $user)->first();
        $check = DB::table('user_roles')->where(['character_id' => $character->character_id, 'role' => $role])->get();
        if($check !== null) {
            DB::table('user_roles')->where(['character_id' => $character->character_id,
                                            'role' => $role])
                                            ->delete();
            return view('admin.dashboard')->with('success', 'User Updated.');
        }

        return view('admin.dashboard')->with('error', 'User did not have the role.');
    }
}
