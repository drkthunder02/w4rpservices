<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\DokuGroupNames;
use App\Models\DokuMember;
use App\Models\DokuUser;

class WikiController extends Controller
{
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

        return view('wiki.register')->with('name', $name);
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
        $gname = DB::select('SELECT gname FROM wiki_groupnames WHERE id = ?', [1]);
        $member->uid = $uid[0]->id;
        $member->gid = 1;
        $member->groupname = $gname[0]->gname;
        $member->save();
        //Return to the dashboard view
        return redirect('/dashboard')->with('success', 'Registration successful.<br>Your username is: ' . $name);
    }

    public function displayChangePassword() {
        $name = Auth::user()->name;
        $name = strtolower($name);
        $name = str_replace(' ', '_', $name);
        $check = DB::select('SELECT login FROM wiki_user WHERE login = ?', [$name]);
        if(!isset($check[0])) {
            return redirect('/dashboard')->with('error', 'Login Not Found!');
        } 

        return view('wiki.changepassword')->with('name', $name);
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

        return redirect('/dashboard')->with('success', 'Password changed successfully.<br>Your username is: ' . $name);
    }
}
