<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vonage\Laravel\Facade\Vonage;
use Vonage\Messages\Channel\SMS\SMSText;
use Vonage\Voice\Endpoint\Phone;
use Vonage\Voice\OutboundCall;
use Vonage\Voice\Webhook;
use Vonage\Client;

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

        $userTicket = $ticket->user()->get()->first();

        // If this is not my ticket, I need to notify its creator
        if ($userTicket->id !== Auth::id()) {
            if ($userTicket->notification_method === 'sms') {
                $sms = new SMSText(
                    $userTicket->phone_number,
                    config('vonage.sms_from'),
                    $ticketEntry->content
                );
                $client = app(Client::class);
                $client->messages()->send($sms);
            }
            if ($userTicket->notification_method === 'voice') {
                $currentHost = env('PUBLIC_URL', url('/'));
                $outboundCall = new OutboundCall(
                    new Phone($userTicket->phone_number),
                    new Phone(config('vonage.sms_from'))
                );
                $outboundCall
                    ->setAnswerWebhook(
                        new Webhook($currentHost . '/webhook/answer/' . $ticketEntry->id)
                    )
                    ->setEventWebhook(
                        new Webhook($currentHost . '/webhook/event/' . $ticketEntry->id)
                    );
                Vonage::voice()->createOutboundCall($outboundCall);
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
            'status' => 'open',
            'user_id' => Auth::id()
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
