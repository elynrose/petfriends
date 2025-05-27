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
        $formattedAmount = number_format($this->amount, 2);
        $purchaseDate = now()->format('F j, Y g:i A');

        return (new MailMessage)
            ->subject('Credit Purchase Confirmed - PetFriends')
            ->view('emails.credit-purchase', [
                'credits' => $this->credits,
                'amount' => $formattedAmount,
                'newBalance' => $this->newBalance,
                'firstName' => $notifiable->name,
                'header' => '💎 Credits Added Successfully!',
                'ctaUrl' => route('frontend.credit-logs.index'),
                'ctaText' => 'View Credit History',
                'image' => asset('images/credits.jpg'),
                'imageAlt' => 'PetFriends Credits',
                'purchaseDate' => $purchaseDate,
                'message' => "Thank you for your purchase! Your account has been credited with {$this->credits} credits. Your new balance is {$this->newBalance} credits.",
                'details' => [
                    'Purchase Date' => $purchaseDate,
                    'Credits Purchased' => $this->credits,
                    'Amount Paid' => '$' . $formattedAmount,
                    'New Balance' => $this->newBalance . ' credits'
                ]
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
} 