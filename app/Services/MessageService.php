<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Interfaces\ConversationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MessageService
{
    public function __construct(
        protected ConversationRepositoryInterface $conversationRepository
    ) {}

    public function sendMessage(int $senderId, int $recipientId, string $body): Message
    {

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
    }

    /**
     * @return LengthAwarePaginator<int, Message>|null
     */
    public function paginatedConversationMessages(int $userId, int $recipientId): ?LengthAwarePaginator
    {
        $conversation = $this->conversationRepository->findOneBetweenUser($userId, $recipientId);

        if (! $conversation) {
            return null;
        }

        return $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    /**
     * @param  iterable<int, Message>  $messages
     * @return Collection<int, Message>
     */
    public function markAsRead(iterable $messages, int $senderId): Collection
    {
        $messages = collect($messages)->transform(function (Message $message) use ($senderId) {
            $message->setAttribute('is_unread', ($message->read_at === null && $message->sender_id !== $senderId));

            return $message;
        });

        $messageIds = $messages->pluck('id');

        if ($messageIds->isNotEmpty()) {
            Message::whereIn('id', $messageIds)
                ->where('sender_id', '!=', $senderId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return $messages;
    }
}
