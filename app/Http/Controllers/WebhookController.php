<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRecordings;
use App\Models\TicketEntry;
use App\Models\TicketRecording;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vonage\Laravel\Facade\Vonage;

class WebhookController extends Controller
{
    public function answer(TicketEntry $ticketEntry) {
        if (!$ticketEntry->exists) {
            return response()->json([
                [
                    'action' => 'talk',
                    'text' => 'Sorry, there has been an error fetching your ticket information'
                ]
            ]);
        }

        return response()->json([
            [
                'action' => 'talk',
                'text' => $ticketEntry->content,
            ],
            [
                'action' => 'talk',
                'text' => 'To add a reply, please leave a message after the beep, then press the pound key',
            ],
            [
                'action'    => 'record',
                'endOnKey'  => '#',
                'beepStart' => true,
                'eventUrl' => [env('PUBLIC_URL') . '/webhook/recordings/' .  $ticketEntry->id]
            ],
            [
                'action' => 'talk',
                'text' => 'Thank you, your ticket has been updated.',
            ]
        ]);
    }

    public function recording(TicketEntry $ticketEntry, Request $request)
    {
        $params = $request->all();

        Log::info('Recording event', $params);

        $filename = Str::random(10);
        $audio = Vonage::get($params['recording_url'])->getBody();
        Storage::put('public/' . $filename . '.mp3', $audio);

        $ticketRecording = new TicketRecording([
            'ticket_entry_id' => $ticketEntry->id,
            'fileName' => $filename
        ]);

        $ticketRecording->save();

        return response('', 204);
    }
}
