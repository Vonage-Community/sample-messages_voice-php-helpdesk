<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketRecording extends Model
{
    use HasFactory;

    protected $table = 'ticket_entry_files';

    protected $fillable = [
        'ticket_entry_id',
        'filename'
    ];

    public function ticketEntry(): HasOne
    {
        return $this->hasOne(TicketEntry::class, 'id', 'ticket_entry_id');
    }
}
