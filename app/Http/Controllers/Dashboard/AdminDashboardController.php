<?php

namespace App\Http\Controllers\Dashboard;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Support\Facades\Auth;

//Libraries
use App\Library\Helpers\TaxesHelper;
use App\Library\Helpers\LookupHelper;
use App\Library\Helpers\SRPHelper;

//Models
use App\Models\User\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\User\AvailableUserPermission;
use App\Models\User\AvailableUserRole;
use App\Models\Admin\AllowedLogin;
use App\Models\Finances\AllianceWalletJournal;

class AdminDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    /**
     * Show the administration dashboard.
     */
    public function displayAdminDashboard() {
        if(auth()->user()->hasRole('Admin') ||
           auth()->user()->hasPermission('srp.admin') || 
           auth()->user()->hasPermission('contract.admin' ||
           auth()->user()->hasPermission('mining.officer'))) {
            //Do nothing and continue on
        } else {
            redirect('/dashboard');
        }

        
        return view('admin.dashboards.dashboard');
    }

    /**
     * Display users in a paginated format
     */
    public function displayUsersPaginated() {
        $this->middleware('role:Admin');

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

    /**
     * Search users for a specific user
     */
    public function searchUsers(Request $request) {
        $this->middleware('role:Admin');

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

    /**
     * Display the allowed logins
     */
    public function displayAllowedLogins() {
        $this->middleware('role:Admin');

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

    /**
     * Display the taxes for the alliance
     * 
     */
    public function displayTaxes() {
        $this->middleware('role:Admin');

        //Declare variables needed for displaying items on the page
        $months = 3;
        $pi = array();
        $industry = array();
        $reprocessing = array();
        $office = array();
        $corpId = 98287666;
        $srpActual = array();
        $srpLoss = array();
        $miningTaxes = array();
        $miningTaxesLate = array();

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

            $miningTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMoonMiningTaxesGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $miningTaxesLate[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMoonMiningTaxesLateGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $moonRentalTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMoonRentalTaxesGross($date['start'], $date['end']), 2, ".", ","),
            ];
        }

        return view('admin.dashboards.taxes')->with('pis', $pis)
                                            ->with('industrys', $industrys)
                                            ->with('offices', $offices)
                                            ->with('markets', $markets)
                                            ->with('jumpgates', $jumpgates)
                                            ->with('reprocessings', $reprocessings)
                                            ->with('srpActual', $srpActual)
                                            ->with('srpLoss', $srpLoss)
                                            ->with('miningTaxes', $miningTaxes)
                                            ->with('miningTaxesLate', $miningTaxesLate);
    }

    /**
     * Display the modify user form
     */
    public function displayModifyUser(Request $request) {
        $this->middleware('role:Admin');

        $permissions = array();
        $roles = array();
        
        $name = $request->user;

        //Get the user information from the name
        $user = User::where(['name' => $name])->first();

        $perms = AvailableUserPermission::all();
        foreach($perms as $p) {
            $permissions[$p->permission] = $p->permission;
        }

        $tempRoles = AvailableUserRole::all();

        foreach($tempRoles as $tempRole) {
            array_push($roles, [
                $tempRole['role'] => $tempRole['role']
            ]);
        }

        $role = $user->getRole();

        //Pass the user information to the page for hidden text entries
        return view('admin.user.modify')->with('user', $user)
                                        ->with('permissions', $permissions)
                                        ->with('role', $role)
                                        ->with('roles', $roles);
    }

    /**
     * Modify a user's role
     */
    public function modifyRole(Request $request) {
        $this->middleware('role:Admin');

        $this->validate($request, [
            'user' => 'required',
            'role' => 'required',
        ]);

        UserRole::where(['character_id' => $request->user])->update([
            'role' => $request->role,
        ]);

        return redirect('/admin/dashboard/users')->with('success', "User: " . $request->user . " has been modified to a new role: " . $request->role . ".");
    }

    public function addPermission(Request $request) {
        $this->middleware('role:Admin');

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

    /**
     * Delete a user to reset their permissions
     */
    public function removeUser(Request $request) {
        $this->middleware('role:Admin');

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

    /**
     * Add an entity to the allowed login table
     */
    public function addAllowedLogin(Request $request) {
        $this->middleware('role:Admin');

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

    /**
     * Remove an entity from the allowed login table
     */
    public function removeAllowedLogin(Request $request) {
        $this->middleware('role:Admin');

        //Set the parameters to validate the form
        $this->validate($request, [
            'removeAllowedLogin' => 'required',
        ]);

        AllowedLogin::where([
            'entity_name' => $request->removeAllowedLogin,
        ])->delete();

        return redirect('/admin/dashboard')->with('success', 'Entity removed from allowed login list.');
    }

    /**
     * Show journal entries in a table for admins from alliance wallets
     */
    public function displayJournalEntries() {
        $this->middleware('role:Admin');

        $date = Carbon::now()->subDays(60);

        $journal = AllianceWalletJournal::where('date', '>=', $date)
                                         ->where([
                                            'corporation_id' => 98287666,
                                            'ref_type' => 'player_donation',
                                         ])->orderByDesc('date',)->get(['amount', 'reason', 'description', 'date']);

        return view('admin.dashboards.walletjournal')->with('journal', $journal);
    }
}
