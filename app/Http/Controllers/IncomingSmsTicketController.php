<?php

namespace App\Http\Controllers;

use App\Models\TicketEntry;
use App\Models\User;
use Illuminate\Http\Request;

class IncomingSmsTicketController extends Controller
{
    public function store(Request $request): bool
    {
        $requestData = $request->all();
        $user = User::where('phone_number', $requestData['from'])->get()->first();

        if (!$user) {
            // @TODO make this an actual exception class
            throw new \Exception('Could not parse SMS webhook, cannot find user');
        }

        $ticket = $user->latestTicketWithActivity();

        $entry = new TicketEntry([
            'content' => $request->text,
            'channel' => 'sms',
        ]);

        $entry->user()->associate($user);
        $entry->ticket()->associate($ticket);
        $entry->save();

        return true;
    }
}
