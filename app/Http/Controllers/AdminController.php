<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function displayDashboard() {
        return view('admin.dashboard');
    }

    public function addRole(Request $request) {
        //Get the user and role from the form
        $user = $request->user;
        $role = $request->role;
        //Get the character id from the username using the user table
        $character = DB::table('users')->where('name', $user)->first();
        //Delete the current roles from the database
        DB::table('user_roles')->where(['character_id' => $character->character_id])->delete();
        //Insert the new role into the database
        DB::table('user_roles')->insert([
            'character_id' => $characer->character->id,
            'role'=> $role,
        ]);
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
            DB::table('user_roles')->where(['character_id' => $character->id,
                                            'role' => $role])
                                            ->delete();
            return view('admin.dashboard')->with('success', 'User Updated.');
        }

        return view('admin.dashboard')->with('error', 'User did not have the role.');
    }
}
