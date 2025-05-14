<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Chat $message)
    {
        $this->message = $message;
        Log::info('NewChatMessage event constructed', [
            'message_id' => $message->id,
            'booking_id' => $message->booking_id
        ]);
    }

    public function broadcastOn()
    {
        \Log::info('Broadcasting message on channel', [
            'channel' => 'booking.' . $this->message->booking_id,
            'chat_id' => $this->message->id,
            'message' => $this->message->message,
            'from_id' => $this->message->from_id,
            'from_name' => $this->message->from->name,
            'from_photo' => $this->message->from->getFirstMediaUrl('photo', 'thumb')
        ]);
        return new PrivateChannel('booking.' . $this->message->booking_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'from_id' => $this->message->from_id,
            'from_name' => $this->message->from->name,
            'from_photo' => $this->message->from->getFirstMediaUrl('photo', 'thumb'),
            'created_at' => $this->message->created_at->format('Y-m-d H:i:s')
        ];
    }
} 