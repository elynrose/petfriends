<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataChangeEmailNotification extends Notification
{
    use Queueable;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return $this->getMessage();
    }

    public function getMessage()
    {
        $action = ucfirst($this->data['action']);
        $modelName = ucfirst($this->data['model_name']);
        $timestamp = now()->format('F j, Y g:i A');

        return (new MailMessage)
            ->subject("PetFriends: {$modelName} {$action}")
            ->greeting('Hello,')
            ->line("A {$modelName} has been {$this->data['action']} in the system.")
            ->line("Timestamp: {$timestamp}")
            ->line('Please log in to review the changes and take any necessary actions.')
            ->action('View ' . $modelName, config('app.url'))
            ->line('This is an automated notification. Please do not reply to this email.')
            ->line('If you have any questions, please contact the system administrator.')
            ->salutation('Best regards, PetFriends Team');
    }
}
