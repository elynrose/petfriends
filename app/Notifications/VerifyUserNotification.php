<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyUserNotification extends Notification
{
    use Queueable;

    private $user = null;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationLink = route('userVerification', $this->user->verification_token);
        $expiryTime = now()->addHours(24)->format('F j, Y g:i A');

        return (new MailMessage)
            ->subject('Welcome to PetFriends - Verify Your Account')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('Thank you for joining PetFriends! We\'re excited to have you as part of our community.')
            ->line('To get started, please verify your email address by clicking the button below.')
            ->line('This verification link will expire on ' . $expiryTime . '.')
            ->action('Verify Email Address', $verificationLink)
            ->line('If you did not create an account, no further action is required.')
            ->line('After verification, you can:')
            ->line('• Create your pet profile')
            ->line('• Browse available pets')
            ->line('• Start booking pet sitting services')
            ->line('If you have any questions, please don\'t hesitate to contact our support team.')
            ->salutation('Best regards, PetFriends Team');
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
