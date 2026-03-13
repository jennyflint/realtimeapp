<?php

namespace App\Repositories\Interfaces;

use App\Models\Conversation;

interface ConversationRepositoryInterface
{
    /**
     * Get a conversation between two users.
     */
    public function findOneBetweenUser(int $userA, int $userB): ?Conversation;
}
