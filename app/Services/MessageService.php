<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Interfaces\ConversationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function __construct(
        protected ConversationRepositoryInterface $conversationRepository
    ) {}

    public function sendMessage(int $senderId, int $recipientId, string $body): Message
    {
        return DB::transaction(function () use ($senderId, $recipientId, $body) {
            $conversation = $this->conversationRepository->findOneBetweenUser($senderId, $recipientId);

            if (! $conversation) {
                $conversation = Conversation::create();
                $conversation->users()->attach([
                    $senderId,
                    $recipientId,
                ]);
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'body' => $body,
            ]);

            broadcast(new MessageSent($message))->toOthers();

            return $message;
        });
    }
}
