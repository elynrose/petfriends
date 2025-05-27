<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReferralInvitation extends Notification
{
    use Queueable;

    protected $referral;
    protected $invitationLink;

    public function __construct($referral, $invitationLink)
    {
        $this->referral = $referral;
        $this->invitationLink = $invitationLink;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You\'ve Been Invited to PetFriends!')
            ->greeting('Hello!')
            ->line($this->referral->referrer->name . ' has invited you to join PetFriends!')
            ->line('Sign up now and get started with pet sitting services.')
            ->action('Join PetFriends', $this->invitationLink)
            ->line('Thank you for using our application!');
    }
} 