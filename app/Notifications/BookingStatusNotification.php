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
        $fromDate = \Carbon\Carbon::parse($this->booking->from);
        $fromTime = \Carbon\Carbon::parse($this->booking->from_time);
        $toTime = \Carbon\Carbon::parse($this->booking->to_time);

        return (new MailMessage)
            ->subject('Booking Request Update - ' . $this->booking->pet->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->line('Booking Details:')
            ->line('• Pet: ' . $this->booking->pet->name)
            ->line('• Date: ' . $fromDate->format('F j, Y'))
            ->line('• Time: ' . $fromTime->format('g:i A') . ' - ' . $toTime->format('g:i A'))
            ->line('• Duration: ' . $fromTime->diffInHours($toTime) . ' hours')
            ->line('• Status: ' . ucfirst($this->booking->status))
            ->action('View Booking Details', route('frontend.bookings.show', $this->booking))
            ->line('Thank you for using PetFriends! If you have any questions, please don\'t hesitate to contact us.')
            ->salutation('Best regards, PetFriends Team');
    }
} 