<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function getPaginatedExcept(int $excludeId, int $perPage = 15): LengthAwarePaginator;
}
