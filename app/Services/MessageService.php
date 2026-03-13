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

    /**
     * @return array<array, mixed>
     */
    public function getConversationMessages(int $userId, int $recipientId): array
    {
        $conversation = $this->conversationRepository->findOneBetweenUser($userId, $recipientId);

        if (! $conversation) {
            return ['messages' => []];
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();

        $messages->transform(function ($message) use ($userId) {
            $message->is_unread = ($message->read_at === null && $message->sender_id !== $userId);

            return $message;
        });

        $conversation->messages()
            ->where('sender_id', $recipientId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return [
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ];
    }
}
