<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function index(PaginationRequest $request): View
    {
        $users = $this->userRepository->paginateUsersForInbox(
            $request->user()->id,
            $request->perPage()
        );

        return view('user.index', compact('users'));
    }
}
