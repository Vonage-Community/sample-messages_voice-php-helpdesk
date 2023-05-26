<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Vonage\Laravel\Facade\Vonage;
use Vonage\Messages\Channel\SMS\SMSText;
use Vonage\Messages\Channel\Viber\ViberText;
use Vonage\Messages\Channel\WhatsApp\WhatsAppText;
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
        if ($ticket->type === 'realtime') {
            return view('ticket.realtime', [
                'ticket' => $ticket
            ]);
        }

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

            if ($userTicket->notification_method === 'whatsapp') {
                $sms = new WhatsAppText(
                    $userTicket->phone_number,
                    config('vonage.sms_from'),
                    $ticketEntry->content
                );
                $client = app(Client::class);
                $client->messages()->send($sms);
            }

            if ($userTicket->notification_method === 'viber') {
                $sms = new ViberText(
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
        // superuser cannot create a ticket
        $superUser = User::where('email', 'admin@vonage.com')->get()->first();

        if ($superUser->id === Auth::id()) {
            redirect()->route('ticket.index');
        }

        //TODO spin this off into a validation request
        $validatedRequestData = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'channel' => 'required',
            'isConversation' => 'nullable'
        ]);

        // If it's conversation api, we need to do some different things
        if (array_key_exists('isConversation', $validatedRequestData)) {
            $ticket = Ticket::create([
                'title' => $validatedRequestData['title'],
                'status' => 'open',
                'type' => 'realtime',
                'user_id' => Auth::id(),
            ]);

            $ticket->save();

            // Get me a CAPI Instance to attach to the ticket
            $response = Vonage::post('https://api.nexmo.com/v0.3/conversations', [
                $ticket->id . '_chat',
                'Ticket number ' . $ticket->id . ' Support Chat',
                'properties' => [
                    'ttl' => 1200
                ]
            ]);

            $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $ticket->capi_conversation_id = $responsePayload['id'];
            $ticket->save();

            // Add the super admin and this user to the conversation
            Vonage::post('https://api.nexmo.com/v0.3/conversations/' . $ticket->capi_conversation_id . '/members', [
                'user' => [
                    'id' => $superUser->capi_user_id,
                    'name' => Str::snake($superUser->name)
                ]
            ]);

            Vonage::post('https://api.nexmo.com/v0.3/conversations/' . $ticket->capi_conversation_id . '/members', [
                'user' => [
                    'id' => Auth::user()->capi_user_id,
                    'name' => Str::snake(Auth::user()->name)
                ]
            ]);

            return redirect()->route('ticket.show', $ticket->id);
        }

        $ticket = Ticket::create([
            'title' => $validatedRequestData['title'],
            'status' => 'open',
            'type' => 'sync',
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

        return redirect()->route('ticket.show', $ticket->id);
    }
}
