<?php

namespace App\Jobs;

use App\Models\TicketEntry;
use App\Models\TicketRecording;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessRecordings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (TicketRecording::all() as $ticketRecording) {

            $newTicketEntry = new TicketEntry([
                'content' => $this->transcribeRecording($ticketRecording->filename),
                'channel' => 'voice',
            ]);

            $parentTicket = $ticketRecording->ticketEntry()->ticket()->get()->first();
            $newTicketEntryUser = $parentTicket->user()->get()->first();
            $newTicketEntry->user()->associate($newTicketEntryUser);
            $newTicketEntry->ticket()->associate($parentTicket);
            $newTicketEntry->save();

            $ticketRecording->delete();
        }
    }

    public function transcribeRecording($filename)
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
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'url' => env('PUBLIC_URL') . '/storage/' . $filename . '.mp3'
                    ], JSON_THROW_ON_ERROR
                )
            ]);

        if ($transcriptionResponse->getStatusCode() !== 200) {
            Log::error('Transcription service failed, check your credentials');
            return false;
        }

        $transcriptionResponseBody = json_decode($transcriptionResponse->getBody(), true);
        $transcription = $transcriptionResponseBody['results']['channels'][0]['alternatives'][0]['transcript'];

        Log::info('Voice Response', [$transcription]);
        Storage::delete('public/' . $filename . '.mp3');

        return $transcription;
    }
}
