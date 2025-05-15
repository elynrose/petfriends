<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditPurchaseReceipt extends Notification implements ShouldQueue
{
    use Queueable;

    protected $credits;
    protected $amount;

    public function __construct($credits, $amount)
    {
        $this->credits = $credits;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Credit Purchase Receipt - ' . config('app.name'))
            ->greeting('Thank you for your purchase!')
            ->line('Your credit purchase has been completed successfully.')
            ->line('Purchase Details:')
            ->line('- Credits Purchased: ' . $this->credits)
            ->line('- Amount Paid: $' . number_format($this->amount, 2))
            ->line('- New Credit Balance: ' . $notifiable->credits)
            ->line('Your credits are now available in your account.')
            ->action('View Your Credits', route('frontend.credits.purchase'))
            ->line('Thank you for using our service!');
    }
} 