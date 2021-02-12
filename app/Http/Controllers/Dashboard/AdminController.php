<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

//Libraries
use App\Library\Taxes\TaxesHelper;
use App\Library\Lookups\LookupHelper;
use App\Library\SRP\SRPHelper;

//Models
use App\Models\User\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\User\AvailableUserPermission;
use App\Models\User\AvailableUserRole;
use App\Models\Admin\AllowedLogin;
use App\Models\Doku\DokuGroupNames;
use App\Models\Doku\DokuMember;
use App\Models\Doku\DokuUser;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function displayTestAdminDashboard() {
        return view('admin.dashboards.testdashboard');
    }

    public function showJournalEntries() {
        $dateInit = Carbon::now();
        $date = $dateInit->subDays(30);

        $journal = DB::select('SELECT amount,reason,description,date FROM `player_donation_journal` WHERE corporation_id=98287666 AND date >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 2 MONTH) ORDER BY date DESC');
        
        return view('admin.dashboards.walletjournal')->with('journal', $journal);
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
                'character_id' => $user->character_id,
            ])->count();
            
            if($permCount > 0) {
                $perms = UserPermission::where([
                    'character_id' => $user->character_id,
                ])->get('permission')->toArray();

                foreach($perms as $perm) {
                    $permString .= $perm['permission'] . ', ';
                }

                $user->permission = $permString;
            } else {
                $user->permission = 'No Permissions';
            }
        }

        return view('admin.dashboards.userspaged')->with('usersArr', $usersArr);
    }

    public function searchUsers(Request $request) {
        //Declare array variables
        $user = array();
        $permission = array();
        $userArr = array();
        $permString = null;

        //Validate the input from the form
        $this->validate($request, [
            'parameter' => 'required',
        ]);

        $usersArr = User::where('name', 'like', $request->parameter . "%")->paginate(50);

        foreach($usersArr as $user) {
            $user->role = $user->getRole();

            $permCount = UserPermission::where([
                'character_id' => $user->character_id,
            ])->count();

            if($permCount > 0) {
                $perms = UserPermission::where([
                    'character_id' => $user->character_id,
                ])->get('permission')->toArray();

                foreach($perms as $perm) {
                    $permString .= $perm['permission'] . ', ';
                }

                $user->permission = $permString;
            } else {
                $user->permission = 'No Permissions';
            }
        }

        return view('admin.dashboards.users.searched')->with('usersArr', $usersArr);
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
        $srpHelper = new SRPHelper();
        //Get the dates for the tab panes
        $dates = $tHelper->GetTimeFrameInMonths($months);
        //Get the data for the Taxes Pane
        foreach($dates as $date) {
            //Get the srp actual pay out for the date range
            $srpActual[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($srpHelper->GetAllianceSRPActual($date['start'], $date['end']), 2, ".", ","),
            ];

            //Get the srp loss value for the date range
            $srpLoss[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($srpHelper->GetAllianceSRPLoss($date['start'], $date['end']), 2, ".", ","),
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
        }

        return view('admin.dashboards.taxes')->with('pis', $pis)
                                            ->with('industrys', $industrys)
                                            ->with('offices', $offices)
                                            ->with('markets', $markets)
                                            ->with('jumpgates', $jumpgates)
                                            ->with('reprocessings', $reprocessings)
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

        $roles = AvailableUserRole::all();

        $role = $user->getRole();

        //Pass the user information to the page for hidden text entries
        return view('admin.user.modify')->with('user', $user)
                                        ->with('permissions', $permissions)
                                        ->with('role', $role)
                                        ->with('roles', $roles);
    }

    public function modifyRole(Request $request) {
        $this->validate($request, [
            'user' => 'required',
            'role' => 'required|role!=None',
        ]);

        UserRole::where(['character_id' => $user])->update([
            'role' => $request->role,
        ]);

        return redirect('/admin/dashboard/users')->with('success', "User: " . $user . " has been modified to a new role: " . $request->role . ".");
    }

    public function addPermission(Request $request) {
        //Get the user and permission from the form
        $character = $request->user;
        $permission = $request->permission;

        //Check to see if the character already has the permission
        $check = UserPermission::where(['character_id' => $character, 'permission' => $permission])->get(['permission']);
        
        if(!isset($check[0]->permission)) {
            $perm = new UserPermission;
            $perm->character_id = $character;
            $perm->permission = $permission;
            $perm->save();

            return redirect('/admin/dashboard/users')->with('success', 'User udpated!');
        } else {
            return redirect('/admin/dashboard/users')->with('error', 'User not updated or already has the permission.');
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

        return redirect('/admin/dashboard/users')->with('success', 'User deleted from the site.');
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
            $login->save();
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
