<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\HelpDesk\HelpDeskTicket;
use App\Models\HelpDesk\HelpDeskTicketResponse;

class HelpDeskController extends Controller
{
    /**
     * Display form to submit a new ticket
     */
    public function displayNewTicket() {
        return view('helpdesk.newticket');
    }

    public function storeTicket(Request $request) {
        //Using the request populate the ticket and save to the database
        $ticket = new HelpDeskTicket;
        $ticket->user_id = Auth()-user()->character_id;
        $ticket->department = $request->department;
        $ticket->subject = $request->subject;
        $ticket->body = $request->body;
        $ticket->save();

        return redirect('/dashboard')->with('success', 'Ticket submitted.');
    }

    /**
     * Display current open tickets for the user
     */
    public function displayMyTickets() {

    }

    /**
     * Modify currently open ticket for the user
     */
    public function editTicket(Request $request) {
        
    }
}
