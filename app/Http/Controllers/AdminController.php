<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\User\AvailableUserPermission;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Corporation\CorpStructure;
use App\Models\Admin\AllowedLogin;

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
        $character = User::where(['name' => $user])->get(['character_id']);

        //Check to see if the character already has the permission
        $check = UserPermission::where(['character_id' => $character[0]->character_id, 'permission' => $permission])->get(['permission']);
        
        if(!isset($check[0]->permission)) {
            $perm = new UserPermission;
            $perm->character_id = $character[0]->character_id;
            $perm->permission = $permission;
            $perm->save();

            return redirect('/admin/dashboard')->with('success', 'User udpated!');
        } else {
            return redirect('/admin/dashboard')->with('error', 'User not updated or already has the permission.');
        }   
    }

    public function removeUser(Request $request) {
        //Get the user from the form to delete
        $user = $request->user;

        //Get the user data from the table
        $data = User::where(['name' => $user])->get();

        //Delete the user's ESI Scopes
        DB::table('EsiScopes')->where(['character_id' => $data[0]->character_id])->delete();

        //Delete the user's ESI Token
        DB::table('EsiTokens')->where(['character_id' => $data[0]->character_id])->delete();

        //Delete the user's role from the roles table
        DB::table('user_roles')->where(['character_id' => $data[0]->character_id])->delete();

        //Delete the user from the user table
        DB::table('users')->where(['character_id' => $data[0]->character_id])->delete();

        return redirect('/admin/dashboard')->with('success', 'User deleted from the site.');
    }

    public function displayAllowedLogins() {
        $logins = AllowedLogin::all();

        return view('admin.allowedlogins')->with('logins', $logins);
    }

    public function addAllowedLogin(Request $request) {
        //Set the parameters to validate the form
        $this->validate($request, [
            'entity_id' => 'required',
            'entity_type' => 'required',
            'entity_name' => 'required',
            'login_type' => 'required',
        ]);

        //Check to see if the entity exists in the database already
        $found = AllowedLogin::where([
            'entity_type' => $request->entityType,
            'entity_name' => $request->entityName,
        ])->get();
        if($found != null) {
            AllowedLogin::where([
                'entity_type' => $request->entityType,
                'entity_name' => $request->entityName,
            ])->update([
                'entity_id' => $request->entityId,
                'entity_type' => $request->entityType,
                'entity_name' => $request->entityName,
                'login_type' => $request->loginType,
            ]);
        } else {
            $login = new AllowedLogin;
            $login->entity_id = $request->entityId;
            $login->entity_name = $request->entityName;
            $login->entity_type = $request->entityType;
            $login->login_type = $request->loginType;
        }

        return redirect('/admin/dashboard')->with('success', 'Entity added to allowed login list.');
    }

    public function removeAllowedLogin(Request $request) {
        //Set the parameters to validate the form
        $this->validate($request, [
            'entity_id' => 'required',
            'entity_type' => 'required',
            'entity_name' => 'required',
            'login_type' => 'required',
        ]);

        AllowedLogin::where([
            'entity_id' => $request->entityId,
            'entity_type' => $request->entityType,
            'entity_name' => $request->entityName,
            'login_type' => $request->loginType,
        ])->delete();

        return redirect('/admin/dashboard')->with('success', 'Entity removed from allowed login list.');
    }
}
