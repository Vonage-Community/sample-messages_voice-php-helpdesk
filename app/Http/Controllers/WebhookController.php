<?php

namespace App\Http\Controllers;

use App\Models\TicketEntry;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Vonage\Laravel\Facade\Vonage;

class WebhookController extends Controller
{
    public function answer(TicketEntry $ticketEntry): JsonResponse
    {
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
                'text' => 'This is a message from the Vonage Helpdesk'
            ],
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
                'eventUrl' => [config('helpdesk.public_url') . '/webhook/recordings/' .  $ticketEntry->id]
            ],
            [
                'action' => 'talk',
                'text' => 'Thank you, your ticket has been updated.',
            ]
        ]);
    }

    public function recording(TicketEntry $ticketEntry, Request $request): Response|Application|ResponseFactory
    {
        $params = $request->all();
        Log::info('Recording event', $params);

        $audio = Vonage::voice()->getRecording($params['recording_url']);
        Storage::put('call_recording.mp3', $audio);

        $ticketContent = $this->transcribeRecordingOpenAi();

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

    public function transcribeRecordingOpenAi()
    {
        $client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
        ]);

        $audioPath = Storage::path('call_recording.mp3');

        $multipart = new MultipartStream([
            [
                'name'     => 'file',
                'contents' => fopen($audioPath, 'rb'),
                'filename' => basename($audioPath),
            ],
            [
                'name'     => 'model',
                'contents' => 'whisper-1',
            ],
        ]);

        $response = $client->request('POST', 'audio/transcriptions', [
            'headers' => [
                'Authorization' => 'Bearer ' . config('helpdesk.open_ai_secret'),
                'Content-Type'   => 'multipart/form-data; boundary=' . $multipart->getBoundary(),
            ],
            'body' => $multipart,
        ]);

        Storage::delete('call_recording.mp3');

        $responseBody = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $responseBody['text'];
    }

    /**
     * @deprecated in favour of OpenAI
     */
    public function transcribeRecordingDeepgram($audio)
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

        return $transcription;
    }
}
