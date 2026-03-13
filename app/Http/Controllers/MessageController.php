<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\User;
use App\Repositories\Interfaces\ConversationRepositoryInterface;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function show(int $recipientId, Request $request, ConversationRepositoryInterface $conversationRepository): Response
    {
        $userId = (int) $request->user()?->getAuthIdentifier();
        $conversation = $conversationRepository->findOneBetweenUser($userId, $recipientId);

        if (! $conversation) {
            return response()->json([
                'messages' => [],
            ]);
        }

        $messages = $conversation
            ->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ]);
    }

    public function store(StoreMessageRequest $request, MessageService $messageService): Response
    {
        /** @var User $sender */
        $sender = $request->user();

        $message = $messageService->sendMessage(
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
