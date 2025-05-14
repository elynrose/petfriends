<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class ReviewReminder extends Notification
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
        return (new MailMessage)
            ->subject('Don\'t forget to leave a review!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We noticed that you haven\'t left a review for your recent booking with ' . $this->booking->pet->name . '.')
            ->line('Your feedback is valuable to our community and helps other pet owners make informed decisions.')
            ->action('Leave a Review', route('frontend.reviews.create', ['booking' => $this->booking->id]))
            ->line('Thank you for being a part of our pet-loving community!');
    }
} 