<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Paginate users for inbox, excluding the authenticated user.
     *
     * @return LengthAwarePaginator<int, User>
     */
    public function paginateUsersForInbox(int $authId, int $perPage = 15): LengthAwarePaginator;
}
