<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEntry extends Model
{
    protected $fillable = ['content', 'channel'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function recordings()
    {
        return $this->hasMany(TicketRecording::class, 'ticket_entry_id', 'id');
    }
}
