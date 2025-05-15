<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditPurchase extends Notification
{
    use Queueable;

    public function __construct($credits, $amount, $newBalance)
    {
        $this->credits = $credits;
        $this->amount = $amount;
        $this->newBalance = $newBalance;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Credit Purchase Confirmed - PetPal Club')
            ->view('emails.credit-purchase', [
                'credits' => $this->credits,
                'amount' => $this->amount,
                'newBalance' => $this->newBalance,
                'firstName' => $notifiable->name,
                'header' => '💎 Credits Added!',
                'ctaUrl' => route('frontend.credit-logs.index'),
                'ctaText' => 'View Credit History',
                'image' => asset('images/credits.jpg'),
                'imageAlt' => 'PetPal Club Credits'
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
} 