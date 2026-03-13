<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Repositories\Interfaces\ConversationRepositoryInterface;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function findOneBetweenUser(int $userA, int $userB): ?Conversation
    {
        return Conversation::query()
            ->whereHas('users', function ($q) use ($userA) {
                $q->where('user_id', $userA);
            })
            ->whereHas('users', function ($q) use ($userB) {
                $q->where('user_id', $userB);
            })
            ->first();
    }
}
