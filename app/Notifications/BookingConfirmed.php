<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
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
            ->subject('Your Booking is Confirmed')
            ->line('Thank you for booking with us. Your booking has been confirmed.')
            ->line('See booking details below:')
            ->line('Date: ' . $this->booking->date)
            ->line('Time: ' . $this->booking->time)
            ->line('Duration: ' . $this->booking->duration . ' hours')
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('Owner: ' . $this->booking->pet->user->name)
            ->line('Thank you for using our service! Please contact us if you have any questions.')
            ->action('View Booking', route('frontend.bookings.show', $this->booking->id));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
} 