<?php

namespace App\Http\Controllers\MiningTaxes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiningTaxesAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Display current unpaid invoices
     */
    public function DisplayUnpaidInvoice() {

    }

    /**
     * Mark an invoice paid
     */
    public function UpdateInvoice() {

    }

    /**
     * Delete an invoice and mark items paid
     */
    public function DeleteInvoice() {
        
    }

    /**
     * Display past paid invoices
     */
    public function DisplayPaidInvoices() {

    }

    /**
     * Display admin mining ledgers by month
     */
    public function DisplayMonthlyMiningLedgers() {

    }
    
}
