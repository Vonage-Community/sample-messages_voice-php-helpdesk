<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function latestTicketWithActivity()
    {
        // Get all tickets this user is watching
        $watchedTickets = TicketSubscription::select('ticket_id')->where('user_id', $this->id)->get()->pluck('ticket_id');

        // Grab the latest ticket with activity that's in this list
        $latestTicketEntry = TicketEntry::select("ticket_id")->whereIn('ticket_id', $watchedTickets)->orderBy('created_at', 'desc')->limit(1)->first();

        // Fetch the actual ticket
        return $latestTicketEntry->ticket()->first();
    }
}
