<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message,
        public ?int $receiverId = null
    ) {}

    public function broadcastOn(): array
    {

        $channels = [
            new PrivateChannel('conversation.'.$this->message->conversation_id),
        ];

        if ($this->receiverId) {
            $channels[] = new PrivateChannel('App.Models.User.'.$this->receiverId);
        }

        return $channels;
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'conversation_id' => $this->message->conversation_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}
