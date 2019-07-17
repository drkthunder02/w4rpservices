<?php

namespace App\Http\Controllers\Dashboard;

//Internal Libraries
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Khill\Lavacharts\Lavacharts;

//Models
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;
use App\Models\SRP\SRPShip;

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
        //Set some variables to be used in if statements
        $open = null;
        $approved = null;
        $denied = null;

        //See if we can get all of the open SRP requests
        $openCount = SRPShip::where([
            'character_id' => auth()->user()->character_id,
            'approved' => 'Under Review',
        ])->count();
        if($openCount > 0) {
            $open = SRPShip::where([
                'character_id' => auth()->user()->character_id,
                'approved' => 'Under Review'
            ])->get();
        }

        //See if we can get all of the closed and approved SRP requests
        $approvedCount = SRPShip::where([
            'character_id' => auth()->user()->character_id,
            'approved' => 'Approved',
        ])->count();
        if($approvedCount > 0) {
            $approved = SRPShip::where([
                'character_id' => auth()->user()->character_id,
                'approved' => 'Approved',
            ])->take(10)->get();
        }

        //See if we can get all of the closed and denied SRP requests
        $deniedCount = SRPShip::where([
            'character_id' => auth()->user()->character_id,
            'approved' => 'Denied',
        ])->count();
        if($deniedCount > 0) {
            $denied = SRPShip::where([
                'character_id' => auth()->user()->character_id,
                'approved' => 'Denied',
            ])->take(10)->get();
        }

        //Create a chart of number of approved, denied, and open requests via a fuel gauge chart
        $lava = new Lavacharts;

        $adur = $lava->DataTable();

        $adur->addStringColumn('Type')
            ->addNumberColumn('Number')
            ->addRow(['SRP', $openCount]);

        $lava->GaugeChart('SRP', $adur, [
            'width' => 200,
            'max' => 15,
            'greenFrom' => 0,
            'greenTo' => 5,
            'yellowFrom' => 5,
            'yellowTo' => 10,
            'redFrom' => 10,
            'redTo' => 15,
            'majorTicks' => [
                'Safe',
                'Warning',
                'Critical',
            ],
        ]);

        return view('dashboard')->with('openCount', $openCount)
                                ->with('approvedCount', $approvedCount)
                                ->with('deniedCount', $deniedCount)
                                ->with('open', $open)
                                ->with('approved', $approved)
                                ->with('denied', $denied)
                                ->with('lava', $lava);
    }

    /**
     * Display the profile of the user
     * The profile will include the ESI Scopes Registered, the character image, and character name
     * 
     * @return \Illuminate\Http\Response
     */
    public function profile() {
        //Declare some variables
        $alts = null;
        $scopes = null;
        $permissions = null;
        $roles = null;

        //Get the Esi scopes, user permission set, and roles
        $scopeCount = EsiScope::where('character_id', auth()->user()->character_id)->count();
        if($scopeCount > 0) {
            $scopes = EsiScope::where('character_id', Auth()->user()->character_id)->get();
        }
        
        $permissionCount = UserPermission::where('character_id', auth()->user()->character_id)->count();
        if($permissionCount > 0) {
            $permissions = UserPermission::where('character_id', Auth()->user()->characer_id)->get();
        }
        
        $roleCount = UserRole::where('character_id', auth()->user()->character_id)->count();
        if($roleCount > 0) {
            $roles = UserRole::where('character_id', Auth()->user()->character_id)->get();
        }

        $altCount = UserAlt::where('main_id', auth()->user()->character_id)->count();
        if($altCount > 0) {
            $alts = UserAlt::where(['main_id' => auth()->user()->character_id])->get();
        }
    
        return view('dashboard.profile')->with('scopeCount', $scopeCount)
                                        ->with('scopes', $scopes)
                                        ->with('permissionCount', $permissionCount)
                                        ->with('permissions', $permissions)
                                        ->with('roleCount', $roleCount)
                                        ->with('roles', $roles)
                                        ->with('altCount', $altCount)
                                        ->with('alts', $alts);
    }

    public function removeAlt(Request $request) {
        $this->validate($request, [
            'character' => 'required',
        ]);

        UserAlt::where([
            'main_id' => auth()->user()->character_id,
            'character_id' => $request->character,
        ])->delete();

        return redirect('/dashboard');
    }
}
