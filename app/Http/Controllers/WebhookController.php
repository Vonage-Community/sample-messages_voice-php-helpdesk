<?php

namespace App\Http\Controllers;

use App\Models\TicketEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Ticket;
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
        if (isset($params['recording_url'])) {
            if ($voiceResponse = $this->transcribeRecording($params['recording_url'])) {

                $entry = new TicketEntry([
                    'content' => $voiceResponse,
                    'channel' => 'voice',
                ]);

                error_log(print_r($entry, true));

                $entry->user()->associate($ticketEntry->user()->first());
                $entry->ticket()->associate($ticketEntry->ticket()->first());
                $entry->save();
            }
        }
        return response('', 204);
    }

    public function event(Request $request) {
        $params = $request->all();
        Log::info('Call event', $params);
        if (isset($params['recording_url'])) {
            if ($voiceResponse = $this->transcribeRecording($params['recording_url'])) {

                $ticket = Ticket::all()->last();
                $user = $ticket->subscribedUsers()->first();

                $entry = new TicketEntry([
                    'content' => $voiceResponse,
                    'channel' => 'voice',
                ]);

                error_log(print_r($entry, true));

                $entry->user()->associate($user);
                $entry->ticket()->associate($ticket);
                $entry->save();
            }
        }
        return response('', 204);
    }

    public function transcribeRecording($recordingUrl)
    {
        $audio = Vonage::get($recordingUrl)->getBody();
//
//        $client = new \GuzzleHttp\Client([
//            'base_uri' => 'https://stream.watsonplatform.net/'
//        ]);
//
//        $transcriptionResponse = $client->request('POST', 'speech-to-text/api/v1/recognize', [
//            'auth' => ['apikey', env('IBM_API_KEY')],
//            'headers' => [
//                'Content-Type' => 'audio/mpeg',
//            ],
//            'body' => $audio
//        ]);
//
//        if ($transcriptionResponse->getStatusCode() != 200) {
//            Log::error('Transcription service failed, check your credentials');
//            return false;
//        }
//
//        $transcription = json_decode($transcriptionResponse->getBody());
//
//        $voiceResponse = '';
//        foreach ($transcription->results as $result) {
//            $voiceResponse .= $result->alternatives[0]->transcript.' ';
//        }
//
//        Log::info('Voice Response', [$voiceResponse]);
        return 'test transcription';
    }
}
