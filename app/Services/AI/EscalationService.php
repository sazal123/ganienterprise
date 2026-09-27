<?php

namespace App\Services\AI;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\TelegramAdmin;
use App\Models\Order;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class EscalationService
{
    protected TelegramService $telegramService;

    /**
     * Keywords and phrases that immediately trigger human escalation.
     */
    protected array $escalationKeywords = [
        'human', 'agent', 'person', 'representative', 'support team', 'speak to someone',
        'refund', 'refund dispute', 'money back', 'chargeback',
        'payment failed', 'payment error', 'charged twice', 'double charged',
        'cancel order', 'cancellation',
        'damaged', 'broken', 'defective', 'faulty',
        'wrong product', 'wrong item', 'incorrect item',
        'missing delivery', 'never arrived', 'lost package', 'not delivered',
        'scam', 'fraud', 'complaint', 'terrible service', 'bad service',
    ];

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Determine if a customer message or tool execution result requires human escalation.
     *
     * @param string $message
     * @param array $toolOutputs
     * @param array|null $aiResult
     * @return bool
     */
    public function shouldEscalate(string $message, array $toolOutputs = [], ?array $aiResult = null): bool
    {
        $lowerMessage = strtolower($message);

        // 1. Check keyword triggers
        foreach ($this->escalationKeywords as $keyword) {
            if (str_contains($lowerMessage, $keyword)) {
                return true;
            }
        }

        // 2. Check if a required tool failed or errored out
        foreach ($toolOutputs as $output) {
            if (is_array($output) && (isset($output['error']) || ($output['success'] ?? true) === false)) {
                if (($output['requires_authentication'] ?? false) === false) {
                    return true;
                }
            }
        }

        // 3. Check if AI response indicated inability to verify policy
        if ($aiResult && !empty($aiResult['content'])) {
            if (str_contains($aiResult['content'], "connect you with our support team") ||
                str_contains($aiResult['content'], "don't have verified information")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Execute conversation mode escalation to human, generate summary, and send Telegram notifications.
     *
     * @param Conversation $conversation
     * @param Message $triggerMessage
     * @param string $reason
     * @return void
     */
    public function escalate(Conversation $conversation, Message $triggerMessage, string $reason = 'Customer requested human support'): void
    {
        // 1. Set conversation mode to human
        $conversation->update([
            'mode'            => 'human',
            'last_message_at' => now(),
        ]);

        // Dispatch real-time WebSocket events safely
        try {
            event(new \App\Events\HumanSupportStarted($conversation, $reason));
            event(new \App\Events\ConversationUpdated($conversation, 0));
        } catch (Throwable $e) {
            Log::info('[EscalationService] WebSocket broadcast skipped: ' . $e->getMessage());
        }

        // 2. Determine customer identity
        $customerName = 'Guest Customer';
        if ($conversation->user_id && $conversation->user) {
            $customerName = $conversation->user->name ?? 'Customer #' . $conversation->user_id;
        }

        // 3. Find authorized related order if available
        $recentOrder = null;
        if ($conversation->user_id) {
            $recentOrder = Order::where('customer_id', $conversation->user_id)->orderBy('id', 'desc')->first();
        }
        $orderInvoice = $recentOrder ? "#{$recentOrder->invoice_id}" : 'N/A';

        // 4. Build short AI summary with HTML escaping for safe Telegram rendering
        $cleanReason = htmlspecialchars($reason);
        $cleanTopic  = htmlspecialchars(Str::limit($triggerMessage->message, 80));
        $summary     = "Reason: {$cleanReason}. Topic: \"{$cleanTopic}\"";

        // 5. Format Telegram HTML message
        $telegramText = implode("\n", [
            "🔴 <b>HUMAN SUPPORT</b>",
            "",
            "<b>Customer:</b>",
            "{$customerName}",
            "",
            "<b>Conversation:</b>",
            "#{$conversation->uuid}",
            "",
            "<b>Order:</b>",
            "{$orderInvoice}",
            "",
            "<b>Message:</b>",
            "<i>" . htmlspecialchars(Str::limit($triggerMessage->message, 300)) . "</i>",
            "",
            "<b>AI Summary:</b>",
            "{$summary}",
        ]);

        // 6. Build Inline Keyboard Buttons (View Order ONLY added if authorized order exists)
        $inlineKeyboard = [
            [
                ['text' => '💬 Reply', 'callback_data' => "reply_{$conversation->uuid}"],
            ],
            [
                ['text' => '📄 View Conversation', 'callback_data' => "view_conv_{$conversation->uuid}"],
            ],
        ];

        // Add [View Order] ONLY when an authorized order exists
        if ($recentOrder) {
            $inlineKeyboard[] = [
                ['text' => '🛒 View Order', 'callback_data' => "view_order_{$recentOrder->id}"],
            ];
        }

        $inlineKeyboard[] = [
            ['text' => '🔒 Close', 'callback_data' => "close_conv_{$conversation->uuid}"],
        ];

        // 7. Dispatch Telegram notifications to active Telegram admins
        $this->notifyAdmins($telegramText, $inlineKeyboard);
    }

    /**
     * Dispatch notification to all registered active Telegram admins or fall back to default chat ID.
     */
    protected function notifyAdmins(string $text, array $inlineKeyboard): void
    {
        try {
            $admins = TelegramAdmin::where('active', 1)->get();

            if ($admins->isNotEmpty()) {
                foreach ($admins as $admin) {
                    $this->telegramService->sendMessageWithInlineKeyboard(
                        $admin->telegram_chat_id,
                        $text,
                        $inlineKeyboard
                    );
                }
            } else {
                // Fall back to default TELEGRAM_CHAT_ID config if no DB admin rows
                $defaultChatId = config('services.telegram.chat_id');
                if (!empty($defaultChatId)) {
                    $this->telegramService->sendMessageWithInlineKeyboard(
                        $defaultChatId,
                        $text,
                        $inlineKeyboard
                    );
                }
            }
        } catch (Throwable $e) {
            Log::error('[EscalationService] Failed to send Telegram escalation notification: ' . $e->getMessage());
        }
    }
}
