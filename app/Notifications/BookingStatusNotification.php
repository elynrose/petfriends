<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingStatusNotification extends Notification
{
    use Queueable;

    protected $booking;
    protected $message;

    public function __construct(Booking $booking, string $message)
    {
        $this->booking = $booking;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Request Update')
            ->greeting('Hello ' . $notifiable->name)
            ->line($this->message)
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('Date: ' . $this->booking->from)
            ->line('Time: ' . $this->booking->from_time . ' - ' . $this->booking->to_time)
            ->action('View Booking', route('frontend.bookings.show', $this->booking))
            ->line('Thank you for using our platform!');
    }
} 