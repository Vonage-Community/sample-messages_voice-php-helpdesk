<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Client;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_deepgram_response()
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
                        'url' =>env('PUBLIC_URL') . '/storage/itjoHGRGmE.mp3'
                    ], JSON_THROW_ON_ERROR
                )
            ]
        );

        $transcriptionResponseBody = json_decode($transcriptionResponse->getBody(), true);
        $transcription = $transcriptionResponseBody['results']['channels'][0]['alternatives'][0]['transcript'];
        dd($transcription);
    }
}
