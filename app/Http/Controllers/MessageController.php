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
        $userId = (int) $request->user()?->getAuthIdentifier();

        return response()->json(
            $this->messageService->getConversationMessages($userId, $recipientId)
        );
    }

    public function store(StoreMessageRequest $request): Response
    {
        /** @var User $sender */
        $sender = $request->user();

        $message = $this->messageService->sendMessage(
            $sender->id,
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
