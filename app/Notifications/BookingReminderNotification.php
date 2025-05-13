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
            ->subject('Reminder: Your Pet Booking is Tomorrow')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder that your pet booking is scheduled for tomorrow.')
            ->line('Booking Details:')
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('From: ' . $this->booking->from . ' ' . $this->booking->from_time)
            ->line('To: ' . $this->booking->to . ' ' . $this->booking->to_time)
            ->action('Cancel Booking', $cancelUrl)
            ->line('If you need to cancel your booking, please click the button above.')
            ->line('Thank you for using our service!');
    }
} 