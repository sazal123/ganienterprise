<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TelegramAdmin;
use App\Services\GuestTokenService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;

class HumanEscalationTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;
    protected string $testToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testToken = '8758560868:AAHSkhWa4l4bW9qJJxz1tI8CRubE94X3swE';
        config(['services.telegram.bot_token' => $this->testToken]);
        config(['services.openrouter.api_key' => 'sk-or-v1-test-key-999']);

        $this->tokenService = new GuestTokenService();
        Http::preventStrayRequests();
    }

    /** @test */
    public function customer_requesting_human_support_triggers_escalation_and_telegram_notification()
    {
        TelegramAdmin::create([
            'telegram_chat_id' => '99887766',
            'role'             => 'admin',
            'active'           => true,
        ]);

        Http::fake([
            'https://api.telegram.org/bot' . $this->testToken . '/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'ai',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'I want to speak with a human support agent please.',
            ]);

        $response->assertStatus(201);

        // Assert conversation mode changed from 'ai' to 'human'
        $conversation->refresh();
        $this->assertEquals('human', $conversation->mode);

        // Assert Telegram notification dispatches with inline keyboard buttons
        Http::assertSent(function ($request) use ($conversation) {
            $url     = $request->url();
            $payload = $request->data();

            if (str_contains($url, '/sendMessage')) {
                $text         = $payload['text'] ?? '';
                $chatId       = (string) ($payload['chat_id'] ?? '');
                $replyMarkup  = $payload['reply_markup'] ?? [];

                return $chatId === '99887766' &&
                       str_contains($text, 'HUMAN SUPPORT') &&
                       str_contains($text, $conversation->uuid) &&
                       !empty($replyMarkup['inline_keyboard']);
            }

            return false;
        });
    }

    /** @test */
    public function refund_and_payment_disputes_trigger_human_escalation()
    {
        TelegramAdmin::create([
            'telegram_chat_id' => '99887766',
            'role'             => 'admin',
            'active'           => true,
        ]);

        Http::fake([
            'https://api.telegram.org/bot' . $this->testToken . '/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'ai',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'My bKash payment failed and money was deducted. I demand a refund dispute!',
            ]);

        $response->assertStatus(201);

        $conversation->refresh();
        $this->assertEquals('human', $conversation->mode);
    }

    /** @test */
    public function subsequent_messages_in_human_mode_bypass_ai_and_notify_telegram_admins()
    {
        TelegramAdmin::create([
            'telegram_chat_id' => '99887766',
            'role'             => 'admin',
            'active'           => true,
        ]);

        Http::fake([
            'https://api.telegram.org/bot' . $this->testToken . '/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'human', // ALREADY IN HUMAN MODE
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Is anyone there yet?',
            ]);

        $response->assertStatus(201);
        $data = $response->json('data');

        $this->assertStringContainsString('assigned to human support', $data['ai_message']['message']);

        // Assert OpenRouter was NOT called
        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'openrouter.ai');
        });

        // Assert Telegram notification was sent
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/sendMessage');
        });
    }

    /** @test */
    public function view_order_button_is_included_only_when_authorized_order_exists()
    {
        TelegramAdmin::create([
            'telegram_chat_id' => '99887766',
            'role'             => 'admin',
            'active'           => true,
        ]);

        Http::fake([
            'https://api.telegram.org/bot' . $this->testToken . '/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        // 1. Escalation WITHOUT order (Guest) -> No View Order button
        $token = $this->tokenService->generateToken();
        $guestConv = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'ai',
        ]);

        $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$guestConv->uuid}/messages", [
                'message' => 'Please connect me to human support',
            ]);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $keyboard = $payload['reply_markup']['inline_keyboard'] ?? [];
            $flattened = json_encode($keyboard);

            return !str_contains($flattened, 'View Order');
        });
    }
}
