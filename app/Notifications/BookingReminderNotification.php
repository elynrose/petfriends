<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $cancelUrl = route('frontend.bookings.destroy', $this->booking->id);
        
        return (new MailMessage)
            ->subject('Quick Reminder: Your Pet Booking is Tomorrow')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder that your pet booking is scheduled for tomorrow.')
            ->line('Here are the booking details:')
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('Pick-up: ' . $this->booking->from . ' ' . $this->booking->from_time)
            ->line('Drop-off: ' . $this->booking->to . ' ' . $this->booking->to_time)
            ->line('If you do not wish to go ahead with this booking, please click the button below to cancel.')
            ->action('Cancel Booking', $cancelUrl)
            ->line('Thank you for using our service!');
    }
} 