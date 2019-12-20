<?php

namespace App\Http\Controllers\Wiki;

//Laravel libraries
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

//User Libraries
use App\Library\Lookups\LookupHelper;
use App\Library\Wiki\WikiHelper;

//Models
use App\Models\Doku\DokuGroupNames;
use App\Models\Doku\DokuMember;
use App\Models\Doku\DokuUser;
use App\Models\Admin\AllowedLogin;
use App\Models\User\User;

class WikiController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Renter');
    }

    public function purgeUsers() {
        //Declare helper classes
        $lookup = new LookupHelper;
        $wikiHelper = new WikiHelper;

        //Get all the users from the database
        $users = DokuUser::pluck('name')->all();

        $legacy = AllowedLogin::where(['login_type' => 'Legacy'])->pluck('entity_id')->toArray();
        $renter = AllowedLogin::where(['login_type' => 'Renter'])->pluck('entity_id')->toArray();

        //Search the names and verify against the lookup table
        //to find the corporation and / or alliance they belong to.
        foreach($users as $user) {
            //Let's look up the character in the user table by their name.
            //If no name is found, then delete the user and have them start over with the wiki permissions
            $count = User::where(['name' => $user])->count();
            if($count > 0) {
                //If the user is not allowed, then delete the user, otherwise, leave the user untouched
                if(!$wikiHelper->AllowedUser($user)) {
                    $this->DeleteWikiUser($user);
                }
            } else {
                $this->DeleteWikiUser($user);
            }
        }

        return redirect('/admin/dashboard')->with('success', 'Wiki has been purged.');
    }
    
    public function displayRegister() {
        //make user name syntax like we want it.
        $name = Auth::user()->name;
        $name = strtolower($name);
        $name = str_replace(' ', '_', $name);

        //Check to see if the user is already registered in the database
        $check = DB::select('SELECT login FROM wiki_user WHERE login = ?', [$name]);
        if(isset($check[0]) && ($check[0]->login === $name)) {
            return redirect('/dashboard')->with('error', 'Already registered for the wiki!');            
        }

        return view('wiki.user.register')->with('name', $name);
    }

    public function storeRegister(Request $request) {
        $this->validate($request, [
            'password' => 'required',
            'password2' => 'required',
        ]);

        $password = '';

        //Check to make sure the password matches
        if($request->password !== $request->password2) {
            return view('/dashboard')->with('error');
        } else {
            $password = md5($request->password);
        }

        if(Auth::user()->hasRole('User')) {
            $role = 1; //User role id from wiki_groupname table
            $roleDescription = 'user';
        } else if(Auth::user()->hasRole('Renter')) {
            $role = 8; //Renter role id from wiki_groupname table
            $roleDescription = 'renter';
        }

        //Load the model
        $user = new DokuUser;
        $member = new DokuMember;

        //make user name syntax like we want it.
        $name = Auth::user()->name;
        $name = strtolower($name);
        $name = str_replace(' ', '_', $name);

        //Add the new user to the wiki
        $user->login = $name;
        $user->pass = $password;
        $user->name = Auth::user()->name;
        $user->save();

        //Get the user from the table to get the uid
        $uid = DB::select('SELECT id FROM wiki_user WHERE login = ?', [$name]);
        $member->uid = $uid[0]->id;
        $member->gid = $role;
        $member->groupname = $roleDescription;
        $member->save();
        //Return to the dashboard view
        return redirect('/dashboard')->with('success', 'Registration successful.  Your username is: ' . $name);
    }

    public function displayChangePassword() {
        $name = Auth::user()->name;
        $name = strtolower($name);
        $name = str_replace(' ', '_', $name);
        $check = DB::select('SELECT login FROM wiki_user WHERE login = ?', [$name]);
        if(!isset($check[0])) {
            return redirect('/dashboard')->with('error', 'Login Not Found!');
        } 

        return view('wiki.user.changepassword')->with('name', $name);
    }

    public function changePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required',
            'password2' => 'required',
        ]);

        //Check for a valid password
        $password = '';
        if($request->password !== $request->password2) {
            return redirect('/wiki/changepassword')->with('error', 'Passwords did not match');
        } else {
            $password = md5($request->password);
        }
        //Get a model ready for the database
        $user = new DokuUser;
        //Find the username for the database through the  character name in auth
        $name = Auth::user()->name;
        $name = strtolower($name);
        $name = str_replace(' ', '_', $name);
        //Update the password for the login name
        DB::table('wiki_user')
            ->where('login', $name)
            ->update(['pass' => $password]);

        return redirect('/dashboard')->with('success', 'Password changed successfully.  Your username is: ' . $name);
    }

    private function DeleteWikiUser($user) {
        //Get the uid of the user as we will need to purge them from the member table as well.
        //the member table holds their permissions.
        $uid = DokuUser::where([
            'name' => $user,
        ])->value('id');
        //Delete the permissions of the user first.
        DokuMember::where([
            'uid' => $uid,
        ])->delete();

        //Delete the user from the user table
        DokuUser::where(['name' => $user])->delete();
    }
}
