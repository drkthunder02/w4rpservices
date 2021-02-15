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
        if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('moon.admin') || auth()->user()->hasPermission('srp.admin') || auth()->user()->hasPermission('contract.admin')) {
            //Do nothing and continue on
        } else {
            redirect('/dashboard');
        }

        
        return view('admin.dashboards.dashboard');
    }
}
