<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

//Libraries
use App\Library\Taxes\TaxesHelper;

//Models
use App\Models\User\User;
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

    public function displayUsersPaginated() {
        //Declare array variables
        $user = array();
        $permission = array();
        $userArr = array();
        $permString = null;

        $usersArr = User::orderBy('name', 'asc')->paginate(50);

        foreach($usersArr as $user) {
            $user->role = $user->getRole();

            $permCount = UserPermission::where([
                'character_id' => 92626011,
            ])->count();
            
            if($permCount > 0) {
                $perms = UserPermission::where([
                    'character_id' => 92626011,
                ])->get('permission')->toArray();

                for($i = 0; $i < $permCount; $i++) {
                    if($i != $permCount - 1) {
                        $permString .= $perms[$i]['permission'] . ',';
                    } else {
                        $permString .= $perms[$i]['permission'];
                    }
                }

                dd($permString);

                foreach($perms as $perm) {
                    $permString .= implode(', ', $perm);
                }

                dd($permString);

                $user->permission = $permString;
            } else {
                $user->permission = 'No Permissions';
            }
        }

        return view('admin.dashboards.userspaged')->with('usersArr', $usersArr);
    }

    public function displayUsers($page) {
        //Declare array variables
        $user = array();
        $permission = array();
        $userArr = array();

        /**
         * For each user we want to build their name and permission set into one array
         * Having all of the data in one array will allow us to build the table for the admin page more fluently.
         * Example:  userArr[0]['name'] = Minerva Arbosa
         *           userArr[0]['role'] = W4RP
         *           userArr[0]['permissions'] = ['admin', 'contract.admin', superuser]
         */
        $usersTable = User::orderBy('name', 'asc')->get()->toArray();
        foreach($usersTable as $user) {
            $perms = UserPermission::where([
                'character_id' => $user['character_id'],
            ])->get('permission')->toArray();

            $tempUser['name'] = $user['name'];
            $tempUser['role'] = $user['user_type'];
            $tempUser['permissions'] = $perms;

            array_push($userArr, $tempUser);
        }

        //Get a user count for the view so we can do pagination
        $userCount = User::orderBy('name', 'asc')->count();
        //Set the amount of pages for the data
        $userPages = ceil($userCount / 50);
        $users = User::pluck('name')->all();

        return view('admin.dashboards.users')->with('users', $users)
                                             ->with('userArr', $userArr)
                                             ->with('userCount', $userCount)
                                             ->with('userPages', $userPages);
    }

    public function displayAllowedLogins() {
        //Declare array variables
        $entities = array();

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

        return view('admin.dashboards.allowed_logins')->with('entities', $entities);
    }

    public function displayPurgeWiki() {
        return view('admin.dashboards.purge_wiki');
    }

    public function displayTaxes() {
        //Declare variables needed for displaying items on the page
        $months = 3;
        $pi = array();
        $industry = array();
        $reprocessing = array();
        $office = array();
        $corpId = 98287666;
        $srpActual = array();
        $srpLoss = array();

        /** Taxes Pane */
        //Declare classes needed for displaying items on the page
        $tHelper = new TaxesHelper();
        //Get the dates for the tab panes
        $dates = $tHelper->GetTimeFrameInMonths($months);
        //Get the data for the Taxes Pane
        foreach($dates as $date) {
            //Get the srp actual pay out for the date range
            $srpActual[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetAllianceSRPActual($date['start'], $date['end']), 2, ".", ","),
            ];

            //Get the srp loss value for the date range
            $srpLoss[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetAllianceSRPLoss($date['start'], $date['end']), 2, ".", ","),
            ];

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
                'gross' => number_format($tHelper->GetAllianceMarketGross($date['start'], $date['end']), 2, ".", ","),
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

        return view('admin.dashboards.taxes')->with('pis', $pis)
                                            ->with('industrys', $industrys)
                                            ->with('offices', $offices)
                                            ->with('markets', $markets)
                                            ->with('jumpgates', $jumpgates)
                                            ->with('reprocessings', $reprocessings)
                                            ->with('pigross', $pigross)
                                            ->with('srpActual', $srpActual)
                                            ->with('srpLoss', $srpLoss);
    }

    public function displayModifyUser(Request $request) {
        $permissions = array();
        
        $name = $request->user;

        //Get the user information from the name
        $user = User::where(['name' => $name])->first();

        $perms = AvailableUserPermission::all();
        foreach($perms as $p) {
            $permissions[$p->permission] = $p->permission;
        }

        //Pass the user information to the page for hidden text entries
        return view('admin.user.modify')->with('user', $user)
                                        ->with('permissions', $permissions);
    }

    public function modifyUser(Request $request) {
        $type = $request->type;
        if(isset($request->permission)) {
            $permission = $request->permission;
        }
        if(isset($request->user)) {
            $user = $request->user;
        }

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
        ])->count();
        if($found != 0) {
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
