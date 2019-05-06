<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Display the profile of the user
     * The profile will include the ESI Scopes Registered, the character image, and character name
     * 
     * @return \Illuminate\Http\Response
     */
    public function profile() {
        $scopes = EsiScope::where('character_id', Auth()->user()->character_id)->get();
        $permissions = UserPermission::where('character_id', Auth()->user()->characer_id)->get();
        $roles = UserRole::where('character_id', Auth()->user()->character_id)->get();
        
        $data = [
            'scopes' => $scopes,
            'permissions' => $permissions,
            'roles' => $roles,
        ];

        return view('dashboard.profile')->with('data', $data);
    }
}
