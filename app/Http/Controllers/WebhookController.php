<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRecording;
use App\Models\TicketEntry;
use App\Models\TicketRecording;
use GuzzleHttp\Client;
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

//        Storage::put('public/' . $filename . '.mp3', $audio);
        $ticketContent = $this->transcribeRecording($audio);

        $newTicketEntry = new TicketEntry([
            'content' => $ticketContent,
            'channel' => 'voice',
        ]);

        $parentTicket = $ticketEntry->ticket()->get()->first();
        $newTicketEntryUser = $parentTicket->user()->get()->first();
        $newTicketEntry->user()->associate($newTicketEntryUser);
        $newTicketEntry->ticket()->associate($parentTicket);
        $newTicketEntry->save();

        return response('', 204);
    }

    public function transcribeRecording($audio)
    {
        $client = new Client([
            'base_uri' => 'https://api.deepgram.com/v1/'
        ]);

        $transcriptionResponse = $client->request(
            'POST',
            'listen?punctuate=true',
            [
                'headers' => [
                    'Authorization' => 'Token ' . env('DEEPGRAM_API_SECRET'),
                    'Content-Type' => 'audio/mpeg',
                ],
                'body' => $audio
            ]);

        if ($transcriptionResponse->getStatusCode() !== 200) {
            Log::error('Transcription service failed, check your credentials');
            return false;
        }

        $transcriptionResponseBody = json_decode($transcriptionResponse->getBody(), true);
        Log::info($transcriptionResponseBody);
        $transcription = $transcriptionResponseBody['results']['channels'][0]['alternatives'][0]['transcript'];

        Log::info('Voice Response', [$transcription]);
        Storage::delete('public/' . $filename . '.mp3');

        return $transcription;
    }
}
