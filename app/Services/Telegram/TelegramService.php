<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    protected string $botToken;
    protected string $baseUrl;

    public function __construct(?string $botToken = null)
    {
        $this->botToken = $botToken ?? config('services.telegram.bot_token', '');
        $this->baseUrl  = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send text message to a numeric Telegram chat ID.
     *
     * @param string|int $chatId
     * @param string $text
     * @param array $params
     * @return array
     */
    public function sendMessage($chatId, string $text, array $params = []): array
    {
        $payload = array_merge([
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ], $params);

        return $this->sendRequest('sendMessage', $payload);
    }

    /**
     * Send message with an inline keyboard.
     *
     * @param string|int $chatId
     * @param string $text
     * @param array $inlineKeyboard
     * @param array $params
     * @return array
     */
    public function sendMessageWithInlineKeyboard($chatId, string $text, array $inlineKeyboard, array $params = []): array
    {
        $params['reply_markup'] = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        return $this->sendMessage($chatId, $text, $params);
    }

    /**
     * Answer Telegram callback query.
     *
     * @param string $callbackQueryId
     * @param string|null $text
     * @param bool $showAlert
     * @return array
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, bool $showAlert = false): array
    {
        $payload = [
            'callback_query_id' => $callbackQueryId,
            'show_alert'        => $showAlert,
        ];

        if ($text !== null) {
            $payload['text'] = $text;
        }

        return $this->sendRequest('answerCallbackQuery', $payload);
    }

    /**
     * Edit existing Telegram message text and optional inline keyboard.
     *
     * @param string|int $chatId
     * @param int $messageId
     * @param string $text
     * @param array|null $inlineKeyboard
     * @return array
     */
    public function editMessage($chatId, int $messageId, string $text, ?array $inlineKeyboard = null): array
    {
        $payload = [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];

        if ($inlineKeyboard !== null) {
            $payload['reply_markup'] = [
                'inline_keyboard' => $inlineKeyboard,
            ];
        }

        return $this->sendRequest('editMessageText', $payload);
    }

    /**
     * Configure Telegram webhook URL and secret token.
     *
     * @param string $url
     * @param string|null $secretToken
     * @return array
     */
    public function setWebhook(string $url, ?string $secretToken = null): array
    {
        $token = $secretToken ?? config('services.telegram.webhook_secret');

        $payload = [
            'url' => $url,
        ];

        if (!empty($token)) {
            $payload['secret_token'] = $token;
        }

        return $this->sendRequest('setWebhook', $payload);
    }

    /**
     * Delete Telegram webhook URL to allow long polling.
     */
    public function deleteWebhook(): array
    {
        return $this->sendRequest('deleteWebhook', []);
    }

    /**
     * Get pending updates from Telegram API (Long polling for local dev).
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array
    {
        $payload = [
            'offset'  => $offset,
            'limit'   => $limit,
            'timeout' => $timeout,
        ];

        return $this->sendRequest('getUpdates', $payload);
    }

    /**
     * Send HTTP POST request to Telegram Bot API.
     */
    protected function sendRequest(string $method, array $payload): array
    {
        if (empty($this->botToken)) {
            Log::error('[TelegramService] Bot token is missing.');
            return [
                'ok'          => false,
                'description' => 'Telegram bot token is not configured.',
            ];
        }

        try {
            $url = "{$this->baseUrl}/{$method}";

            $response = Http::timeout(10)->post($url, $payload);

            $data = $response->json() ?? [];

            if ($response->successful() && ($data['ok'] ?? false)) {
                return $data;
            }

            Log::warning("[TelegramService] Telegram API method {$method} failed.", [
                'status'      => $response->status(),
                'description' => $this->sanitizeLogMessage($data['description'] ?? 'API error'),
            ]);

            return [
                'ok'          => false,
                'description' => $data['description'] ?? 'Telegram API call failed.',
                'error_code'  => $data['error_code'] ?? $response->status(),
            ];

        } catch (Throwable $e) {
            Log::error("[TelegramService] Exception during {$method}: " . $this->sanitizeLogMessage($e->getMessage()));
            return [
                'ok'          => false,
                'description' => 'Failed to connect to Telegram API.',
            ];
        }
    }

    /**
     * Redact Telegram bot token from log messages.
     */
    protected function sanitizeLogMessage(string $message): string
    {
        if (empty($this->botToken)) {
            return $message;
        }

        return str_replace($this->botToken, '***REDACTED_TELEGRAM_TOKEN***', $message);
    }
}
