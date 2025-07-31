<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;
    use InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->chat_room_id)
        ];
    }

    public function boradcastWith()
    {
        return [
            'message_id' => $this->message->message_id,
            'chat_room_id' => $this->message->chat_room_id,
            'sender_id' => $this->message->sender_id,
            'pesan' => $this->message->pesan,
            'created_at' => $this->message->created_at,
        ];
    }
}
