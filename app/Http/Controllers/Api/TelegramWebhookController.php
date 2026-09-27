<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TelegramAdmin;
use App\Models\TelegramReplySession;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle incoming Telegram webhook updates.
     *
     * POST /api/telegram/webhook
     */
    public function handle(Request $request)
    {
        // 1. Security: Validate Telegram Webhook Secret Token Header
        $configuredSecret = config('services.telegram.webhook_secret');
        $headerSecret     = $request->header('X-Telegram-Bot-Api-Secret-Token');

        if (!empty($configuredSecret)) {
            if (!$headerSecret || !hash_equals((string) $configuredSecret, (string) $headerSecret)) {
                Log::warning('[TelegramWebhook] Unauthorized webhook request with invalid secret token.');
                return response()->json(['error' => 'Unauthorized secret token.'], 403);
            }
        }

        $payload = $request->all();

        // 2. Idempotency: Prevent duplicate update processing
        $updateId = $payload['update_id'] ?? null;
        if ($updateId !== null) {
            $cacheKey = "telegram_update_{$updateId}";
            if (Cache::has($cacheKey)) {
                return response()->json(['status' => 'already_processed'], 200);
            }
            Cache::put($cacheKey, true, now()->addHours(24));
        }

        // 3. Process Callback Queries (Buttons)
        if (isset($payload['callback_query'])) {
            return $this->handleCallbackQuery($payload['callback_query']);
        }

        // 4. Process Incoming Text Messages
        if (isset($payload['message'])) {
            return $this->handleTextMessage($payload['message']);
        }

        return response()->json(['status' => 'ignored'], 200);
    }

    /**
     * Handle Telegram inline keyboard callback queries.
     */
    protected function handleCallbackQuery(array $callbackQuery)
    {
        $callbackId   = $callbackQuery['id'] ?? '';
        $callbackData = $callbackQuery['data'] ?? '';
        $chatId       = (string) ($callbackQuery['message']['chat']['id'] ?? '');
        $fromId       = (string) ($callbackQuery['from']['id'] ?? '');
        $effectiveId  = $chatId ?: $fromId;

        if (empty($effectiveId)) {
            return response()->json(['status' => 'no_chat_id'], 200);
        }

        // Authorization: Verify Telegram Numeric Chat ID or From ID (Auto-authorizes configured chat IDs)
        $admin = $this->resolveAdmin($effectiveId, $fromId);

        if (!$admin) {
            $this->telegramService->answerCallbackQuery($callbackId, 'Unauthorized Telegram chat ID.', true);
            return response()->json(['status' => 'unauthorized_chat_id'], 200);
        }

        // Handle Reply Action (e.g. reply_{uuid_or_id})
        if (str_starts_with($callbackData, 'reply_')) {
            $identifier   = substr($callbackData, 6);
            $conversation = Conversation::where('uuid', $identifier)
                ->orWhere('id', $identifier)
                ->first();

            if (!$conversation || $conversation->status === 'closed') {
                $this->telegramService->answerCallbackQuery($callbackId, 'Conversation is closed or invalid.', true);
                return response()->json(['status' => 'conversation_closed'], 200);
            }

            // Create or replace temporary reply session for BOTH group chat_id and user from_id (expires in 15 mins)
            $targetIds = array_unique(array_filter([$effectiveId, $fromId]));
            TelegramReplySession::whereIn('telegram_chat_id', $targetIds)->delete();

            foreach ($targetIds as $idToSave) {
                TelegramReplySession::create([
                    'telegram_chat_id' => $idToSave,
                    'conversation_id'  => $conversation->id,
                    'expires_at'       => now()->addMinutes(15),
                ]);
            }

            $this->telegramService->answerCallbackQuery($callbackId, 'Reply session started. Type your message below!');

            // Send confirmation message to Telegram
            $this->telegramService->sendMessage(
                $effectiveId,
                "💬 <b>Replying to Conversation #{$conversation->uuid}</b>\nPlease type your message below and press Send."
            );

            if ($fromId && $fromId !== $effectiveId) {
                $this->telegramService->sendMessage(
                    $fromId,
                    "💬 <b>Replying to Conversation #{$conversation->uuid}</b>\nPlease type your message below and press Send."
                );
            }

            return response()->json(['status' => 'reply_session_started'], 200);
        }

        // Handle Close Action (e.g. close_conv_{uuid_or_id})
        if (str_starts_with($callbackData, 'close_conv_')) {
            $identifier   = substr($callbackData, 11);
            $conversation = Conversation::where('uuid', $identifier)
                ->orWhere('id', $identifier)
                ->first();

            if ($conversation) {
                $conversation->update(['status' => 'closed']);
                TelegramReplySession::where('conversation_id', $conversation->id)->delete();

                try {
                    event(new \App\Events\ConversationClosed($conversation));
                    event(new \App\Events\ConversationUpdated($conversation->fresh(), 0));
                } catch (\Throwable $e) {
                    Log::info('[TelegramWebhookController] WebSocket broadcast skipped: ' . $e->getMessage());
                }

                $this->telegramService->answerCallbackQuery($callbackId, 'Conversation closed.');
                $this->telegramService->sendMessage($effectiveId, "🔒 Conversation #{$conversation->uuid} closed.");
            }

            return response()->json(['status' => 'conversation_closed'], 200);
        }

        $this->telegramService->answerCallbackQuery($callbackId, 'Action processed.');
        return response()->json(['status' => 'callback_processed'], 200);
    }

    /**
     * Handle incoming admin text messages.
     */
    protected function handleTextMessage(array $message)
    {
        $chatId      = (string) ($message['chat']['id'] ?? '');
        $fromId      = (string) ($message['from']['id'] ?? '');
        $effectiveId = $chatId ?: $fromId;
        $messageText = trim($message['text'] ?? '');
        $messageId   = $message['message_id'] ?? null;

        if (empty($effectiveId)) {
            return response()->json(['status' => 'no_chat_id'], 200);
        }

        // Authorization: Verify Telegram Numeric Chat ID or From ID
        $admin = $this->resolveAdmin($effectiveId, $fromId);

        if (!$admin) {
            $this->telegramService->sendMessage(
                $effectiveId,
                "⚠️ <b>Access Denied</b>\nYour Telegram numeric Chat ID (<code>{$effectiveId}</code>) is not authorized."
            );

            return response()->json(['status' => 'unauthorized_chat_id'], 200);
        }

        // Check if an active reply session exists for either chat_id or from_id
        $searchIds = array_unique(array_filter([$effectiveId, $fromId]));
        $session   = TelegramReplySession::whereIn('telegram_chat_id', $searchIds)->first();

        if ($session) {
            // Verify session hasn't expired
            if ($session->expires_at->isPast()) {
                TelegramReplySession::whereIn('telegram_chat_id', $searchIds)->delete();
                $this->telegramService->sendMessage(
                    $effectiveId,
                    "⚠️ <b>Reply session expired</b>. Please click [Reply] on the notification again."
                );

                return response()->json(['status' => 'session_expired'], 200);
            }

            // Load and verify conversation
            $conversation = Conversation::find($session->conversation_id);

            if (!$conversation || $conversation->status === 'closed') {
                TelegramReplySession::whereIn('telegram_chat_id', $searchIds)->delete();
                $this->telegramService->sendMessage(
                    $effectiveId,
                    "⚠️ <b>Cannot reply</b>. The conversation is closed or invalid."
                );

                return response()->json(['status' => 'conversation_closed'], 200);
            }

            // Save admin reply message to database
            $adminMessage = Message::create([
                'conversation_id'     => $conversation->id,
                'sender_type'         => 'admin',
                'sender_id'           => $admin->user_id,
                'message_type'        => 'text',
                'message'             => $messageText,
                'telegram_message_id' => $messageId ? (string) $messageId : null,
            ]);

            $updateData = ['last_message_at' => now()];
            if ($admin->user_id) {
                $updateData['assigned_admin_id'] = $admin->user_id;
            }
            $conversation->update($updateData);

            // Dispatch real-time WebSocket events to customer channel
            try {
                event(new \App\Events\MessageCreated($adminMessage, $conversation));
                event(new \App\Events\ConversationUpdated($conversation->fresh(), 0));
            } catch (\Throwable $e) {
                Log::info('[TelegramWebhookController] WebSocket broadcast skipped: ' . $e->getMessage());
            }

            // Clear single-use reply session for all target IDs
            TelegramReplySession::whereIn('telegram_chat_id', $searchIds)->delete();

            // Confirm to Admin via Telegram
            $this->telegramService->sendMessage(
                $effectiveId,
                "✓ Message sent"
            );

            return response()->json([
                'status'          => 'reply_sent',
                'message_id'      => $adminMessage->id,
                'conversation_id' => $conversation->id,
            ], 200);
        }

        // Handle slash commands when no reply session exists
        $command = strtolower($messageText);
        if ($command === '/start' || $command === '/help') {
            $this->telegramService->sendMessage(
                $effectiveId,
                "👋 <b>Welcome Admin</b>\nYou are authenticated via Chat ID: <code>{$effectiveId}</code>.\nTo reply to a customer, click the <b>[Reply]</b> button on a notification."
            );
        } elseif ($command === '/status') {
            $this->telegramService->sendMessage(
                $effectiveId,
                "✅ <b>Support Chatbot System Status</b>: Active\nAll channels operating normally."
            );
        } else {
            $this->telegramService->sendMessage(
                $effectiveId,
                "ℹ️ No active reply session found. Click the <b>[Reply]</b> button on a customer notification to reply."
            );
        }

        return response()->json(['status' => 'no_reply_session'], 200);
    }

    /**
     * Resolve TelegramAdmin record by numeric chat_id or from_id.
     * Auto-registers default TELEGRAM_CHAT_ID if configured.
     */
    protected function resolveAdmin(string $chatId, ?string $fromId = null): ?TelegramAdmin
    {
        $ids = array_unique(array_filter([$chatId, $fromId]));

        $admin = TelegramAdmin::whereIn('telegram_chat_id', $ids)
            ->where('active', 1)
            ->first();

        if (!$admin) {
            $defaultChatId = (string) config('services.telegram.chat_id');
            if (!empty($defaultChatId)) {
                foreach ($ids as $id) {
                    if ((string) $id === $defaultChatId) {
                        $admin = TelegramAdmin::firstOrCreate(
                            ['telegram_chat_id' => $id],
                            ['role' => 'admin', 'active' => 1]
                        );
                    }
                }
            }
        }

        return $admin;
    }

    /**
     * Helper endpoint to set up Telegram webhook URL and register admin chat ID.
     *
     * POST /api/telegram/setup-webhook
     */
    public function setupWebhook(Request $request)
    {
        $url = $request->input('url');
        if (empty($url)) {
            $url = url('/api/telegram/webhook');
        }

        $secretToken = config('services.telegram.webhook_secret');
        $res = $this->telegramService->setWebhook($url, $secretToken);

        // Auto register default TELEGRAM_CHAT_ID if present
        $defaultChatId = (string) config('services.telegram.chat_id');
        if (!empty($defaultChatId)) {
            TelegramAdmin::firstOrCreate(
                ['telegram_chat_id' => $defaultChatId],
                ['role' => 'admin', 'active' => 1]
            );
        }

        return response()->json([
            'success'          => true,
            'webhook_url'      => $url,
            'telegram_response'=> $res,
            'registered_admin' => $defaultChatId ?: 'None configured in .env',
        ]);
    }
}
