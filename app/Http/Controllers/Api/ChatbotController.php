<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AI\CustomerSupportAIService;
use App\Services\ChatbotAuthorizationService;
use App\Services\GuestTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Cookie;

class ChatbotController extends Controller
{
    protected GuestTokenService $tokenService;
    protected ChatbotAuthorizationService $authService;
    protected CustomerSupportAIService $aiSupportService;

    public function __construct(
        GuestTokenService $tokenService,
        ChatbotAuthorizationService $authService,
        CustomerSupportAIService $aiSupportService
    ) {
        $this->tokenService     = $tokenService;
        $this->authService      = $authService;
        $this->aiSupportService = $aiSupportService;
    }

    /**
     * Resolve guest token from request or generate a new one if missing.
     */
    protected function resolveGuestToken(Request $request, &$cookieToSet = null): string
    {
        $token = $this->tokenService->getTokenFromRequest($request);
        if (!$token) {
            $token = $this->tokenService->generateToken();
            $cookieToSet = $this->tokenService->makeCookie($token);
        }
        return $token;
    }

    /**
     * Helper to return standard JSON API response.
     */
    protected function jsonResponse(bool $success, $data = null, ?string $message = null, int $status = 200, ?Cookie $cookie = null)
    {
        $response = response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ], $status);

        if ($cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }

    /**
     * 1. Create a conversation (or return existing active conversation).
     *
     * POST /api/v1/chat/conversations
     */
    public function createConversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'         => 'nullable|string|max:255',
            'initial_message' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cookieToSet = null;
        $customer = auth('customer')->user();
        $guestToken = null;

        if ($customer) {
            $conversation = Conversation::where('customer_id', $customer->id)
                ->where('status', 'open')
                ->latest()
                ->first();
        } else {
            $guestToken = $this->resolveGuestToken($request, $cookieToSet);
            $hash = $this->tokenService->hashToken($guestToken);
            $conversation = Conversation::where('guest_token_hash', $hash)
                ->where('status', 'open')
                ->latest()
                ->first();
        }

        // Create new conversation if none active
        if (!$conversation) {
            $conversation = Conversation::create([
                'customer_id'      => $customer ? $customer->id : null,
                'user_id'          => null,
                'guest_token_hash' => $customer ? null : $this->tokenService->hashToken($guestToken),
                'status'           => 'open',
                'mode'             => 'ai',
                'subject'          => $request->input('subject', 'Customer Support Inquiry'),
                'last_message_at'  => now(),
            ]);
        }

        // Create initial message if provided and generate AI response if in AI mode
        if ($request->filled('initial_message')) {
            $senderType = $customer ? 'customer' : 'guest';
            $senderId   = $customer ? $customer->id : null;

            $userMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type'     => $senderType,
                'sender_id'       => $senderId,
                'message_type'    => 'text',
                'message'         => $request->input('initial_message'),
            ]);

            $conversation->update(['last_message_at' => now()]);

            if ($conversation->mode === 'ai') {
                $this->aiSupportService->generateResponse($conversation, $userMessage);
            }
        }

        $conversation->load('messages');

        return $this->jsonResponse(
            true,
            [
                'conversation' => $conversation,
                'guest_token'  => $customer ? null : $guestToken,
            ],
            'Conversation initialized successfully.',
            201,
            $cookieToSet
        );
    }

    /**
     * 2. Get current active conversation.
     *
     * GET /api/v1/chat/conversations/current
     */
    public function getCurrentConversation(Request $request)
    {
        $customer = auth('customer')->user();
        if ($customer) {
            $conversation = Conversation::where('customer_id', $customer->id)
                ->where('status', 'open')
                ->latest()
                ->first();
        } else {
            $guestToken = $this->tokenService->getTokenFromRequest($request);
            if (!$guestToken) {
                return $this->jsonResponse(true, ['conversation' => null], 'No active conversation.');
            }
            $hash = $this->tokenService->hashToken($guestToken);
            $conversation = Conversation::where('guest_token_hash', $hash)
                ->where('status', 'open')
                ->latest()
                ->first();
        }

        if ($conversation) {
            $conversation->load('messages');
        }

        return $this->jsonResponse(true, ['conversation' => $conversation], 'Current conversation retrieved.');
    }

    /**
     * 3. Get single conversation by UUID.
     *
     * GET /api/v1/chat/conversations/{conversation}
     */
    public function showConversation(Request $request, Conversation $conversation)
    {
        $guestToken = $this->tokenService->getTokenFromRequest($request);

        if (!$this->authService->authorizeAccess($conversation, $guestToken)) {
            return $this->jsonResponse(false, null, 'Unauthorized access to conversation.', 403);
        }

        $conversation->load('messages');

        return $this->jsonResponse(true, ['conversation' => $conversation], 'Conversation details retrieved.');
    }

    /**
     * 4. Send message to conversation and trigger AI response.
     *
     * POST /api/v1/chat/conversations/{conversation}/messages
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $guestToken = $this->tokenService->getTokenFromRequest($request);

        if (!$this->authService->authorizeAccess($conversation, $guestToken)) {
            return $this->jsonResponse(false, null, 'Unauthorized access to conversation.', 403);
        }

        if ($conversation->status === 'closed') {
            return $this->jsonResponse(false, null, 'Cannot send message to a closed conversation.', 422);
        }

        $validator = Validator::make($request->all(), [
            'message'      => 'required|string|min:1|max:2000',
            'message_type' => 'nullable|in:text,image,file',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Server strictly determines sender
        $customer = auth('customer')->user();
        $senderType = $customer ? 'customer' : 'guest';
        $senderId   = $customer ? $customer->id : null;

        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => $senderType,
            'sender_id'       => $senderId,
            'message_type'    => $request->input('message_type', 'text'),
            'message'         => $request->input('message'),
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Process response via CustomerSupportAIService (handles AI mode & human escalation mode)
        $aiMessage = $this->aiSupportService->generateResponse($conversation, $userMessage);

        // Dispatch real-time WebSocket events safely
        try {
            event(new \App\Events\MessageCreated($userMessage, $conversation));
            if ($aiMessage) {
                event(new \App\Events\MessageCreated($aiMessage, $conversation));
            }
            event(new \App\Events\ConversationUpdated($conversation->fresh(), 0));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::info('[ChatbotController] WebSocket broadcast skipped: ' . $e->getMessage());
        }

        return $this->jsonResponse(
            true,
            [
                'user_message' => $userMessage,
                'ai_message'   => $aiMessage,
                'message'      => $userMessage, // Kept for API backward compatibility
            ],
            'Message sent successfully.',
            201
        );
    }

    /**
     * 5. Get paginated messages for a conversation.
     *
     * GET /api/v1/chat/conversations/{conversation}/messages
     */
    public function getMessages(Request $request, Conversation $conversation)
    {
        $guestToken = $this->tokenService->getTokenFromRequest($request);

        if (!$this->authService->authorizeAccess($conversation, $guestToken)) {
            return $this->jsonResponse(false, null, 'Unauthorized access to conversation.', 403);
        }

        $perPage = min((int) $request->input('per_page', 20), 100);

        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        return $this->jsonResponse(true, $messages, 'Messages retrieved.');
    }

    /**
     * 6. Mark messages as read.
     *
     * POST /api/v1/chat/conversations/{conversation}/read
     */
    public function markAsRead(Request $request, Conversation $conversation)
    {
        $guestToken = $this->tokenService->getTokenFromRequest($request);

        if (!$this->authService->authorizeAccess($conversation, $guestToken)) {
            return $this->jsonResponse(false, null, 'Unauthorized access to conversation.', 403);
        }

        $updatedCount = Message::where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->jsonResponse(true, ['marked_read_count' => $updatedCount], 'Messages marked as read.');
    }

    /**
     * 7. Close conversation.
     *
     * POST /api/v1/chat/conversations/{conversation}/close
     */
    public function closeConversation(Request $request, Conversation $conversation)
    {
        $guestToken = $this->tokenService->getTokenFromRequest($request);

        if (!$this->authService->authorizeAccess($conversation, $guestToken)) {
            return $this->jsonResponse(false, null, 'Unauthorized access to conversation.', 403);
        }

        $conversation->update(['status' => 'closed']);

        $closeMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'system',
            'sender_id'       => null,
            'message_type'    => 'system',
            'message'         => 'Conversation has been closed.',
        ]);

        try {
            event(new \App\Events\MessageCreated($closeMessage, $conversation));
            event(new \App\Events\ConversationClosed($conversation));
            event(new \App\Events\ConversationUpdated($conversation->fresh(), 0));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::info('[ChatbotController] WebSocket broadcast skipped: ' . $e->getMessage());
        }

        return $this->jsonResponse(true, ['conversation' => $conversation->fresh()], 'Conversation closed.');
    }
}
