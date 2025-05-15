<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingRequest extends Notification
{
    use Queueable;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Booking Request')
            ->line('You have received a new booking request.')
            ->line('Booking Details:')
            ->line('Date: ' . $this->booking->date)
            ->line('Time: ' . $this->booking->time)
            ->line('Duration: ' . $this->booking->duration . ' hours')
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('Requested by: ' . $this->booking->user->name)
            ->action('Review Request', route('frontend.bookings.show', $this->booking->id));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
} 