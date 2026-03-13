<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function paginateUsersForInbox(int $authId, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->where('id', '!=', $authId)
            ->withCount(['messages as unread_count' => function ($query) use ($authId) {
                $query->whereNull('read_at')
                    ->whereHas('conversation', function ($q) use ($authId) {
                        $q->whereHas('users', fn ($sq) => $sq->where('users.id', $authId));
                    });
            }])
            ->orderByRaw('unread_count DESC')
            ->orderBy('name', 'ASC')
            ->paginate($perPage);
    }
}
