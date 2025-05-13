<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class TwilioChannel
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.from');
    }

    public function send($notifiable, Notification $notification)
    {
        if (!$notifiable->phone_number || !$notifiable->sms_notifications) {
            return;
        }

        $message = $notification->toTwilio($notifiable);

        return $this->client->messages->create(
            $notifiable->phone_number,
            [
                'from' => $this->from,
                'body' => $message
            ]
        );
    }
} 