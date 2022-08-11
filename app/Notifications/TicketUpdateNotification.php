<?php

namespace App\Notifications;

use App\Models\TicketEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class TicketUpdateNotification extends Notification
{
    use Queueable;

    protected TicketEntry $ticketEntry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TicketEntry $ticketEntry)
    {
        $this->ticketEntry = $ticketEntry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['vonage'];
    }

    public function toVonage($notifiable)
    {
        return (new VonageMessage())
            ->content($this->ticketEntry->content);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
