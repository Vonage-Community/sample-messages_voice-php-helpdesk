<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['title', 'status'];

    public function entries()
    {
        return $this->hasMany(TicketEntry::class);
    }
}
