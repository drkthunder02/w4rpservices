<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

//Libraries
use App\Library\Taxes\TaxesHelper;

//Models
use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\User\AvailableUserPermission;
use App\Models\Admin\AllowedLogin;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function displayDashboard() {
        //Declare variables needed for displaying items on the page
        $months = 3;
        $pi = array();
        $industry = array();
        $reprocessing = array();
        $office = array();
        $user = array();
        $permission = array();
        $entities = array();
        $corpId = 98287666;

        /** Taxes Pane */
        //Declare classes needed for displaying items on the page
        $tHelper = new TaxesHelper();
        //Get the dates for the tab panes
        $dates = $tHelper->GetTimeFrameInMonths($months);
        //Get the data for the Taxes Pane
        foreach($dates as $date) {
            //Get the pi taxes for the date range
            $pis[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetPIGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the industry taxes for the date range
            $industrys[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetIndustryGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the reprocessing taxes for the date range
            $reprocessings[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetReprocessingGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the office taxes for the date range
            $offices[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetOfficeGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the market taxes for the date range
            $markets[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMarketGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the jump gate taxes for the date range
            $jumpgates[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetJumpGateGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $pigross[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetPiSalesGross($date['start'], $date['end']), 2, ".", ","),
            ];
        }

        /** Users & Permissions Pane  */
        $userArr = array();
        /**
         * For each user we want to build their name and permission set into one array
         * Having all of the data in one array will allow us to build the table for the admin page more fluently.
         * Example:  userArrs[0]['name'] = Minerva Arbosa
         *           userArrs[0]['permissions'] = ['admin', 'contract.admin', superuser]
         */
        $users = User::orderBy('name', 'desc')->toArray();
        foreach($users as $user) {
            $permissions = UserPermission::where([
                'character_id' => $user['character_id'],
            ])->get()->toArray();

            $tempUser['name'] =  $user['name'];
            $tempUser['role'] = $user['role'];
            $tempUser['permissions'] = $permissions;

            array_push($userArry, $tempUser);
        }

        //Get the users from the database to allow a selection of users for various parts of the webpage
        $users = User::pluck('name')->all();
        //Get the available permissions from the database to allow a selection of permissions
        $permissions = AvailableUserPermission::pluck('permission')->all();
        
        foreach($users as $key => $value) {
            $user[$value] = $value;
        }
        //Create the permission key value pairs
        foreach($permissions as $key => $value) {
            $permission[$value] = $value;
        }
        //Create the data array
        $data = [
            'users' => $user,
            'permissions' => $permission,
        ];

        /** Entities for allowed logins */
        $legacys = AllowedLogin::where(['login_type' => 'Legacy'])->pluck('entity_name')->toArray();
        $renters = AllowedLogin::where(['login_type' => 'Renter'])->pluck('entity_name')->toArray();
        //Compile a list of entities by their entity_id
        foreach($legacys as $legacy) {
            $entities[] = $legacy;
        }
        foreach($renters as $renter) {
            $entities[] = $renter;
        }

        return view('admin.dashboard')->with('data', $data)
                                      ->with('userArr', $userArr)
                                      ->with('pis', $pis)
                                      ->with('industrys', $industrys)
                                      ->with('offices', $offices)
                                      ->with('markets', $markets)
                                      ->with('jumpgates', $jumpgates)
                                      ->with('reprocessings', $reprocessings)
                                      ->with('entities', $entities)
                                      ->with('pigross', $pigross);
    }

    public function modifyRole(Request $request) {
        return redirect('/admin/dashboard')->with('error', 'Not implemented yet.');
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

    public function addAllowedLogin(Request $request) {
        //Set the parameters to validate the form
        $this->validate($request, [
            'allowedEntityId' => 'required',
            'allowedEntityType' => 'required',
            'allowedEntityName' => 'required',
            'allowedLoginType' => 'required',
        ]);

        //Check to see if the entity exists in the database already
        $found = AllowedLogin::where([
            'entity_type' => $request->allowedentityType,
            'entity_name' => $request->allowedEntityName,
        ])->get();
        if($found != null) {
            AllowedLogin::where([
                'entity_type' => $request->allowedEntityType,
                'entity_name' => $request->allowedEntityName,
            ])->update([
                'entity_id' => $request->allowedEntityId,
                'entity_type' => $request->allowedEntityType,
                'entity_name' => $request->allowedEntityName,
                'login_type' => $request->allowedLoginType,
            ]);
        } else {
            $login = new AllowedLogin;
            $login->entity_id = $request->allowedEntityId;
            $login->entity_name = $request->allowedEntityName;
            $login->entity_type = $request->allowedEntityType;
            $login->login_type = $request->allowedLoginType;
        }

        return redirect('/admin/dashboard')->with('success', 'Entity added to allowed login list.');
    }

    public function removeAllowedLogin(Request $request) {
        //Set the parameters to validate the form
        $this->validate($request, [
            'removeAllowedLogin' => 'required',
        ]);

        AllowedLogin::where([
            'entity_name' => $request->removeAllowedLogin,
        ])->delete();

        return redirect('/admin/dashboard')->with('success', 'Entity removed from allowed login list.');
    }
}
