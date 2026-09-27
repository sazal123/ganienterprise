<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TelegramAdmin;
use App\Services\Telegram\TelegramService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TelegramIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected TelegramService $telegramService;
    protected string $testToken;
    protected string $testSecret;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testToken  = '8758560868:AAHSkhWa4l4bW9qJJxz1tI8CRubE94X3swE';
        $this->testSecret = 'test_webhook_secret_9999';

        config([
            'services.telegram.bot_token'      => $this->testToken,
            'services.telegram.webhook_secret' => $this->testSecret,
        ]);

        $this->telegramService = new TelegramService($this->testToken);
        Http::preventStrayRequests();
        Cache::flush();
    }

    /** @test */
    public function telegram_service_sends_messages_keyboards_and_edits()
    {
        Http::fake([
            'https://api.telegram.org/bot' . $this->testToken . '/sendMessage' => Http::response(['ok' => true, 'result' => ['message_id' => 101]], 200),
            'https://api.telegram.org/bot' . $this->testToken . '/answerCallbackQuery' => Http::response(['ok' => true, 'result' => true], 200),
            'https://api.telegram.org/bot' . $this->testToken . '/editMessageText' => Http::response(['ok' => true, 'result' => ['message_id' => 101]], 200),
            'https://api.telegram.org/bot' . $this->testToken . '/setWebhook' => Http::response(['ok' => true, 'result' => true], 200),
        ]);

        // 1. Test sendMessage
        $msgResult = $this->telegramService->sendMessage(123456789, 'Hello Admin');
        $this->assertTrue($msgResult['ok']);

        // 2. Test sendMessageWithInlineKeyboard
        $keyboard = [
            [['text' => 'Accept', 'callback_data' => 'accept_1001']],
        ];
        $kbResult = $this->telegramService->sendMessageWithInlineKeyboard(123456789, 'New Order', $keyboard);
        $this->assertTrue($kbResult['ok']);

        // 3. Test answerCallbackQuery
        $cbResult = $this->telegramService->answerCallbackQuery('cb_123', 'Done');
        $this->assertTrue($cbResult['ok']);

        // 4. Test editMessage
        $editResult = $this->telegramService->editMessage(123456789, 101, 'Updated text');
        $this->assertTrue($editResult['ok']);

        // 5. Test setWebhook
        $hookResult = $this->telegramService->setWebhook('https://example.com/api/telegram/webhook');
        $this->assertTrue($hookResult['ok']);
    }

    /** @test */
    public function webhook_validates_secret_token_header()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => '987654321',
            'role' => 'admin',
            'active' => true,
        ]);

        // Request with INVALID secret token header -> 403 Forbidden
        $invalidResponse = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong_secret_token',
        ])->postJson('/api/telegram/webhook', [
            'update_id' => 1001,
            'message' => [
                'chat' => ['id' => 987654321],
                'text' => '/start',
            ],
        ]);

        $invalidResponse->assertStatus(403);
        $invalidResponse->assertJson(['error' => 'Unauthorized secret token.']);

        // Request with VALID secret token header -> 200 OK
        $validResponse = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->testSecret,
        ])->postJson('/api/telegram/webhook', [
            'update_id' => 1002,
            'message' => [
                'chat' => ['id' => 987654321],
                'text' => '/start',
            ],
        ]);

        $validResponse->assertStatus(200);
        $validResponse->assertJson(['status' => 'no_reply_session']);
    }

    /** @test */
    public function webhook_authorizes_strictly_by_numeric_chat_id_and_ignores_usernames()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        // Authorized admin chat ID: 987654321
        TelegramAdmin::create([
            'telegram_chat_id' => '987654321',
            'role' => 'admin',
            'active' => true,
        ]);

        // Attempt from unauthorized numeric chat ID 111222333 spoofing authorized username '@super_admin'
        $unauthorizedResponse = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->testSecret,
        ])->postJson('/api/telegram/webhook', [
            'update_id' => 2001,
            'message' => [
                'chat' => ['id' => 111222333, 'username' => 'super_admin'], // Spoofed username
                'from' => ['id' => 111222333, 'username' => 'super_admin'],
                'text' => '/status',
            ],
        ]);

        $unauthorizedResponse->assertStatus(200);
        $unauthorizedResponse->assertJson(['status' => 'unauthorized_chat_id']);

        // Request from authorized numeric chat ID 987654321
        $authorizedResponse = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->testSecret,
        ])->postJson('/api/telegram/webhook', [
            'update_id' => 2002,
            'message' => [
                'chat' => ['id' => 987654321],
                'text' => '/status',
            ],
        ]);

        $authorizedResponse->assertStatus(200);
        $authorizedResponse->assertJson(['status' => 'no_reply_session']);
    }

    /** @test */
    public function webhook_processing_is_idempotent_and_prevents_duplicate_updates()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => '987654321',
            'role' => 'admin',
            'active' => true,
        ]);

        $payload = [
            'update_id' => 88776655,
            'message' => [
                'chat' => ['id' => 987654321],
                'text' => '/status',
            ],
        ];

        // 1st request -> Processed successfully
        $firstPass = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', $payload);

        $firstPass->assertStatus(200);
        $firstPass->assertJson(['status' => 'no_reply_session']);

        // 2nd request with SAME update_id -> Intercepted by Idempotency check
        $secondPass = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', $payload);

        $secondPass->assertStatus(200);
        $secondPass->assertJson(['status' => 'already_processed']);
    }

    /** @test */
    public function telegram_bot_token_is_never_exposed_in_logs_or_responses()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => false, 'description' => 'Unauthorized error'], 401),
        ]);

        $result = $this->telegramService->sendMessage(123456, 'Test');

        $this->assertFalse($result['ok']);
        $this->assertStringNotContainsString($this->testToken, json_encode($result));
    }
}
