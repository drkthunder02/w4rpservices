<?php

namespace App\Http\Controllers\Dashboard;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Support\Facades\Auth;

//Libraries
use App\Library\Taxes\TaxesHelper;
use App\Library\Wiki\WikiHelper;
use App\Library\Lookups\LookupHelper;
use App\Library\SRP\SRPHelper;

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
        $this->middleware('role:Guest');
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
