<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Twilio\Rest\Client;

class BookingSMSReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $twilio;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $pet = $this->booking->pet;
        $owner = $pet->owner;
        $address = $owner->address ?? 'Address not provided';
        
        $message = "REMINDER: Your pet booking starts in 1 hour!\n\n";
        $message .= "Pet: {$pet->name}\n";
        $message .= "Type: {$pet->type}\n";
        $message .= "Pickup: {$address}\n";
        $message .= "Time: {$this->booking->from_time}\n";
        $message .= "Duration: {$this->booking->from} to {$this->booking->to}\n";
        $message .= "Owner Contact: {$owner->phone}\n";
        $message .= "Special Instructions: {$pet->description}\n\n";
        $message .= "Thank you for using PetFriends!";

        // Send SMS via Twilio
        try {
            $this->twilio->messages->create(
                $notifiable->phone, // The phone number to send to
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
        }

        return [
            'message' => $message,
            'booking_id' => $this->booking->id
        ];
    }
} 