<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\TicketSubscription;
use App\Models\User;
use App\Notifications\TicketCreated;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;

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
            'notification_method' => 'required',
            'channel' => 'required'
        ]);

        $ticket = Ticket::create([
            'title' => $validatedRequestData['title'],
            'status' => 'open'
        ]);

        $ticketEntry = new TicketEntry([
            'content' => $validatedRequestData['content'],
            'channel' => $validatedRequestData['channel'],
        ]);

        $ticketEntry->user()->associate(Auth::user());
        $ticketEntry->ticket()->associate($ticket);
        $ticketEntry->save();

        $ticketSubscription = new TicketSubscription();
        $ticketSubscription->user()->associate(User::find($validatedRequestData['recipient']));
        $ticketSubscription->ticket()->associate($ticketEntry);
        $ticketSubscription->save();

        if ($validatedRequestData['notification_method'] === 'sms') {
            NotificationFacade::send(
                $ticket->subscribedUsers()->get(),
                new TicketCreated($ticketEntry)
            );
        }

        return view('dashboard', ['tickets' => Ticket::all()]);
    }
}
