<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;

use App\Models\Doku\DokuGroupNames;
use App\Models\Doku\DokuMember;
use App\Models\Doku\DokuUser;

class WikiController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
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

        return view('wiki/user/register')->with('name', $name);
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
        } else if(Auth::user()->hasRole('Renter')) {
            $role = 8; //Renter role id from wiki_groupname table
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

        return view('wiki/user/changepassword')->with('name', $name);
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

    /**
     * Displays the page to add a user to a certain group
     */
    public function displayAddUserToGroup() {
        return view('wiki/displayaddug');
    }

    /**
     * Stores the modifications to the user to add to a group to give permissions
     * 
     * @param uid
     * @param gid
     * @param gname
     */
    public function storeAddUserToGroup($uid, $gid, $gname) {

        return redirect('/dashboard')->with('success', 'User added to the group: ' . $gid . ' with name of ' . $gname);
    }
}
