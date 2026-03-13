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

            broadcast(new MessageSent($message, $recipientId))->toOthers();

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
            return ['messages' => [], 'hasMore' => false];
        }

        $paginator = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $messages = collect($paginator->items())->transform(function ($message) use ($userId) {
            $message->setAttribute('is_unread', ($message->read_at === null && $message->sender_id !== $userId));

            return $message;
        });

        $conversation->messages()
            ->where('sender_id', $recipientId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return [
            'conversation_id' => $conversation->id,
            'messages' => $messages,
            'next_page' => $paginator->currentPage() + 1,
            'has_more' => $paginator->hasMorePages(),
        ];
    }
}
