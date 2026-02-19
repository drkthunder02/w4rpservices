<?php

namespace App\Http\Controllers\AfterActionReports;

use App\Http\Controllers\Controller;

class AfterActionReportsAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:fc.lead');
    }

    public function DeleteReport() {}

    public function DeleteComment() {}

    public function PruneReports() {}

    public function DisplayStastics() {}
}
