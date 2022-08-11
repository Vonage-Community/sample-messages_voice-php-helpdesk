<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\TicketSubscription;
use App\Models\User;
use App\Notifications\TicketUpdateNotification;
use Illuminate\Http\Request;
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

    public function update(Ticket $ticket, Request $request)
    {
        $validatedRequestData = $request->validate([
            'content' => 'required',
            'channel' => 'required'
        ]);

        $ticketEntry = new TicketEntry([
            'content' => $validatedRequestData['content'],
            'channel' => $validatedRequestData['channel'],
        ]);

        $ticketEntry->user()->associate(Auth::user());
        $ticketEntry->ticket()->associate($ticket);
        $ticketEntry->save();

        // If this is not my ticket, I need to notifiy its creator
        if (!$ticket->user()->get()->id() === Auth::id()) {
            if ($ticket->notification_method === 'sms') {
                NotificationFacade::send(
                    $ticket->user()->get(),
                    new TicketUpdateNotification($ticketEntry)
                );
            }
        }

        return redirect()->route('ticket.show', [$ticket]);
    }

    public function store(Request $request)
    {
        //TODO spin this off into a validation request
        $validatedRequestData = $request->validate([
            'title' => 'required',
            'content' => 'required',
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

        $user = Auth::user();
        $ticketEntry->user()->associate($user);
        $ticketEntry->ticket()->associate($ticket);
        $ticketEntry->save();

        return view('dashboard', ['tickets' => Ticket::all()]);
    }
}
