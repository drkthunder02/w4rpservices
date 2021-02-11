<?php

namespace App\Http\Controllers\Dashboard;

//Internal Libraries
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Khill\Lavacharts\Lavacharts;
use Carbon\Carbon;

//Models
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\User\UserPermission;
use App\Models\User\UserRole;
use App\Models\SRP\SRPShip;
use App\Models\User\UserAlt;
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Ledger;

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
        $open = array();
        $approved = array();
        $denied = array();
        $ores = array();
        $altCount = null;
        $alts = null;

        /**
         * Alt Counts
         */
        //Get the number of the user's alt which are registered so we can process the alt's on the main dashboard page
        $altCount = UserAlt::where([
            'main_id' => auth()->user()->character_id,
        ])->count();

        //If the alt count is greater than 0 get all of the alt accounts
        if($altCount > 0) {
            $alts = UserAlt::where([
                'main_id' => auth()->user()->character_id,
            ])->get();
        }

        /**
         * SRP Items
         */
        //See if we can get all of the open SRP requests
        $openCount = SRPShip::where([
            'character_id' => auth()->user()->character_id,
            'approved' => 'Under Review',
        ])->count();
        if($openCount > 0) {
            $open = SRPShip::where([
                'character_id' => auth()->user()->character_id,
                'approved' => 'Under Review'
            ])->get()->toArray();
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
            ])->take(10)->get()->toArray();
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
            ])->take(10)->get()->toArray();
        }

        //Process all types of srp requests for the alt of the main and add to the main's page
        if($altCount > 0) {
            //For each alt, get the open requests, and increment the open request counter
            foreach($alts as $alt) {
                $altOpenCount = SRPShip::where([
                    'character_id' => $alt->character_id,
                    'approved' => 'Under Review',
                ])->count();
                if($altOpenCount > 0) {
                    //If the number of open requests is greater than zero, add to the open count
                    $openCount += $altOpenCount;
                    
                    //Get the alt's open srp requests
                    $altOpen = SRPShip::where([
                        'character_id' => $alt->character_id,
                        'approved' => 'Under Review',
                    ])->get()->toArray();

                    //Add the alt's open requests to the open requests array
                    foreach($altOpen as $aOpen) {
                        array_push($open, $aOpen);
                    }
                }

                $altApprovedCount = SRPShip::where([
                    'character_id' => $alt->character_id,
                    'approved' => 'Approved',
                ])->count();
                if($altApprovedCount > 0) {
                    //If the number of approved requests is greater than zero, add to the approved count
                    $approvedCount += $altApprovedCount;

                    //Get the alt's approved srp request
                    $altApproved = SRPShip::where([
                        'character_id' => $alt->character_id,
                        'approved' => 'Approved',
                    ])->take(5)->get()->toArray();

                    //For each alt add it to the array
                    foreach($altApproved as $aApproved) {
                        array_push($approved, $aApproved);
                    }
                }

                $altDeniedCount = SRPShip::where([
                    'character_id' => $alt->character_id,
                    'approved' => 'Denied',
                ])->count();
                if($altDeniedCount > 0) {
                    //If the denied count is greater then zero for the alt, add it to the count
                    $deniedCount += $altDeniedCount;

                    //Get the denied alt's srp requests
                    $altDenied = SRPShip::where([
                        'character_id' => $alt->character_id,
                        'approved' => 'Denied',
                    ])->take(5)->get()->toArray();

                    //For each alt's denied request add it to the array
                    foreach($altDenied as $aDenied) {
                        array_push($denied, $aDenied);
                    }
                }
            }
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

        /**
         * Mining Tax Items
         */
        /*
        $invoice = Ledger::where([
            'character_id' => auth()->user()->getId(),
            'invoiced' => 'No',
        ])->sum('amount');

        $rows = Ledger::where([
            'character_id' => auth()->user()->getId(),
            'invoiced' => 'No',
        ])->get();

        foreach($rows as $row) {
            $ores[$row->ore_name] = $ores[$row->ore_name] + $row->quantity;
        }
        */
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
        $scopeCount = EsiScope::where('character_id', Auth()->user()->character_id)->count();
        if($scopeCount > 0) {
            $scopes = EsiScope::where('character_id', Auth()->user()->character_id)->get();
        }

        //Get the permission count and permission of the user
        $permissionCount = UserPermission::where('character_id', auth()->user()->character_id)->count();
        if($permissionCount > 0) {
            $permissions = UserPermission::where('character_id', auth()->user()->character_id)->get();
        }

        //Get the roles and role count of the user
        $roleCount = UserRole::where('character_id', Auth()->user()->character_id)->count();
        if($roleCount > 0) {
            $roles = UserRole::where('character_id', Auth()->user()->character_id)->get();
        }

        //Get the alt count and alts of the user
        $altCount = UserAlt::where('main_id', Auth()->user()->character_id)->count();
        if($altCount > 0) {
            $alts = UserAlt::where(['main_id' => Auth()->user()->character_id])->get();
        }
        
        //Return the view with that data
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
