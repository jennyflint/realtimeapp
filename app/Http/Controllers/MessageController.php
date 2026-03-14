<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $messageService
    ) {}

    public function show(int $recipientId, Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $paginator = $this->messageService->paginatedConversationMessages($user->id, $recipientId);

        if ($paginator) {
            $messages = $paginator->items();

            if ($messages) {
                $messages = $this->messageService->markAsRead($messages, $user->id);
            }

            $data = [
                'conversation_id' => $messages ? $messages->first()?->conversation_id : null,
                'messages' => $messages,
                'next_page' => $paginator->currentPage() + 1,
                'has_more' => $paginator->hasMorePages(),
            ];
        } else {
            $data = ['messages' => [], 'hasMore' => false];
        }

        return response()->json($data);
    }

    public function store(StoreMessageRequest $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $message = $this->messageService->sendMessage(
            $user->id,
            (int) $request->validated('recipient_id'),
            $request->validated('message')
        );

        return response()->json([
            'conversation_id' => $message->conversation_id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'created_at' => $message->created_at,

        ], Response::HTTP_CREATED);
    }
}
