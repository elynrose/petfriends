<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReferralCreditAwarded extends Notification
{
    use Queueable;

    protected $referral;

    public function __construct($referral)
    {
        $this->referral = $referral;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You\'ve Earned Referral Credits!')
            ->greeting('Congratulations!')
            ->line('Someone you referred has joined PetFriends!')
            ->line('You\'ve been awarded 4 hours of free credits.')
            ->line('Thank you for helping us grow our community!')
            ->action('View Your Credits', route('frontend.credit-logs.index'));
    }
} 