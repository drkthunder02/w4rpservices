<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

//Libraries
use App\Library\Taxes\TaxesHelper;
use App\Library\Wiki\WikiHelper;
use App\Library\Lookups\LookupHelper;
use App\Library\SRP\SRPHelper;

//Models
use App\Models\User\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\User\AvailableUserPermission;
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

        $journal = DB::select('SELECT amount,reason,description,date FROM `player_donation_journal` WHERE corporation_id=98287666 AND date >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH) ORDER BY date DESC');
        
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

        return redirect('/admin/dashboard/users')->with('error', 'Not Implemented Yet.');
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

    /**
     * Display the wiki dashboard for wiki functions
     */
    public function displayWikiDashboard() {
        //Declare some variables
        $wikiUsers = array();
        $wikiGroups = array();
        
        $tempUsers = DokuUser::all();
        $tempGroups = DokuGroupNames::all();
        $wikiMembership = DokuMember::all();

        //Create a list of users based on id and name for the select form
        foreach($tempUsers as $temp) {
            $wikiUsers[$temp->id] = $temp->name;
        }

        asort($wikiUsers);

        foreach($tempGroups as $temp) {
            $wikiGroups[$temp->id] = $temp->gname;
        }

        asort($wikiGroups);

        return view('admin.dashboards.wiki')->with('wikiUsers', $wikiUsers)
                                           ->with('wikiGroups', $wikiGroups)
                                           ->with('wikiMembership', $wikiMembership);
    }

    /**
     * Delete a wiki user
     */
    public function deleteWikiUser(Request $request) {
        $this->validate($request, [
            'user' => 'required',
        ]);

        //Declare helper variable
        $wikiHelper = new WikiHelper;

        $wikiHelper->DeleteWikiUser($request->user);

        redirect('/admin/dashboard/wiki')->with('success', 'User: ' . $request->user . ' has been deleted.');
    }

    /**
     * Add a group to a wiki user
     */
    public function addWikiUserGroup(Request $request) {
        $this->validate($request, [
            'user' => 'required',       //User Id number
            'groupname' => 'required',  //Group Id number
        ]);

        dd($request->groupname);

        //Declare some helper variables
        $wikiHelper = new WikiHelper;

        //Check to see if the user has the group we are going to add first
        if($wikiHelper->UserHasGroup($request->user, $request->groupname)) {
            return redirect('/admin/dashboard/wiki')->with('error', 'User already has the group.');
        }

        //Add the user to the wiki group
        $wikiHelper->AddUserToGroup($request->user, $request->groupname);

        return redirect('/admin/dashboard/wiki')->with('success', 'User added to group for the wiki.');
    }

    /**
     * Remove a group from a wiki user
     */
    public function removeWikiUserGroup(Request $request) {
        $this->validate($request, [
            'user' => 'required',
            'groupname' => 'required',
        ]);

        //Declare some helper variables
        $wikiHelper = new WikiHelper;

        //Check to see if the user has the group we are going to remove them from
        if(!$wikiHelper->UserHasGroup($request->user, $request->groupname)) {
            return redirect('/admin/dashboard/wiki')->with('error', 'User does not have the group to remove.');
        }

        //Remove the user from the wiki group
        $wikiHelper->RemoveUserFromGroup($request->user, $request->groupname);

        return redirect('/admin/dashboard/wiki')->with('success', 'Removed user from group ' . $request->grouopname);
    }

    /**
     * Remove a user from all wiki groups
     */
    public function removeWikiUserAllGroups(Request $request) {
        $this->validate($request, [
            'user' => 'required',
        ]);

        //Declare variable
        $wikiHelper = new WikiHelper;

        $wikiHelper->RemoveUserFromAllGroups($request->user);

        return redirect('/admin/dashboard/wiki')->with('success', 'User successfully removed from all groups.');
    }

    /**
     * Insert a new group for wiki user's to be added to
     */
    public function insertNewWikiUserGroup(Request $request) {
        $this->validate($request, [
            'group' => 'required',
            'description' => 'required',
        ]);

        //Declare variable
        $wikiHelper = new WikiHelper;

        $wikiHelper->AddNewUserGroup($request->group, $request->description);

        return redirect('/admin/dashboard/wiki')->with('success', 'Added new user group.');
    }

    public function purgeWikiUsers(Request $request) {
        $this->validate($request, [
            'admin' => 'required',
        ]);

        //Declare helper classes
        $lookup = new LookupHelper;
        $wikiHelper = new WikiHelper;

        //Search the names and verify against the lookup table
        //to find the corporation and / or alliance they belong to.
        foreach($users as $user) {
            //Let's look up the character in the user table by their name.
            //If no name is found, then delete the user and have them start over with the wiki permissions
            $count = User::where(['name' => $user])->count();
            if($count > 0) {
                //If the user is not allowed, then delete the user, otherwise, leave the user untouched
                if(!$wikiHelper->AllowedUser($user)) {
                    $wikiHelper->DeleteWikiUser($user);
                }
            } else {
                $wikiHelper->DeleteWikiUser($user);
            }
        }

        return redirect('/admin/dashboard/wiki')->with('success', 'Wiki has been purged.');
    }
}
