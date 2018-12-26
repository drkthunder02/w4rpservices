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
        //Return the view form to create a new ticket
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
        //Return the message the ticket has been submitted, and the main dashboard
        return redirect('/dashboard')->with('success', 'Ticket submitted.');
    }

    /**
     * Display current open tickets for the user
     */
    public function displayMyTickets() {
        //Get the active tickets from the database
        $tickets = HelpDeskTicket::where(['user_id' => auth()->user()->character_id])->get();
        
        //Return the view with the tickets variable
        return view('helpdesk.mytickets')->with('tickets', $tickets);
    }

    /**
     * Modify currently open ticket for the user
     */
    public function editTicket(Request $request) {
        //Update the ticket
        HelpDeskTicket::where(['user_id' => auth()->user()->character_id, 'active' => 1])
                        ->update([
                            'department' => $request->department,
                            'subject' => $request->subject,
                            'body' => $request->body,
                        ]);

        return redirect('/dashboard')->with('success', 'Ticket modified.');
    }
}
