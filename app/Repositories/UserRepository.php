<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getPaginatedExcept(int $excludeId, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->where('id', '!=', $excludeId)
            ->latest()
            ->paginate($perPage);
    }
}
