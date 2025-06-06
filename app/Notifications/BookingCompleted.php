<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;
use App\Services\CreditService;

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
        // Get credit service instance
        $creditService = app(CreditService::class);
        
        // Calculate hours using the service
        $hours = $creditService->calculateBookingHours($this->booking);

        return (new MailMessage)
            ->subject('Booking Completed')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your booking has been automatically marked as completed because the booking time has passed.')
            ->line('See booking details below:')
            ->line('From: ' . $this->booking->from . ' ' . $this->booking->from_time)
            ->line('To: ' . $this->booking->to . ' ' . $this->booking->to_time)
            ->line('Hours of Care Provided: ' . $hours)
            ->line('Credits Earned: ' . $hours)
            ->line('Pet: ' . $this->booking->pet->name)
            ->line('Thank you for using our service!')
            ->line('If you have any questions, please contact us.');
    }
} 