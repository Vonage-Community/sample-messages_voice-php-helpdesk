<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\TicketSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard', ['tickets' => Ticket::all()]);
    }

    public function create(Request $request)
    {
        return view('ticket/create');
    }

    public function show(Ticket $ticket)
    {
        return view('ticket.show', [
            'ticket' => $ticket
        ]);
    }

    public function store(Request $request)
    {
        //TODO spin this off into a validation request
        $validatedRequestData = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'recipient' => 'required|exists:users,id',
            'channel' => 'required',
        ]);

        $ticket = Ticket::create([
            'title' => $validatedRequestData['title'],
            'status' => 'open',
            'channel' => $validatedRequestData['channel']
        ]);

        $ticketEntry = new TicketEntry([
            'content' => $validatedRequestData['content'],
        ]);

        $ticketEntry->user()->associate(Auth::user());
        $ticketEntry->ticket()->associate($ticket);
        $ticketEntry->save();

        $ticketSubscription = new TicketSubscription();
        $ticketSubscription->user()->associate(User::find($validatedRequestData['recipient']));
        $ticketSubscription->ticket()->associate($ticketEntry);
        $ticketSubscription->save();

        return view('dashboard', ['tickets' => Ticket::all()]);
    }
}
