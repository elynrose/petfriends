<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Completed')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your booking has been automatically marked as completed.')
            ->line('Booking Details:')
            ->line('From: ' . $this->booking->from)
            ->line('To: ' . $this->booking->to)
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('Thank you for using our service!')
            ->line('If you have any questions, please contact us.');
    }
} 