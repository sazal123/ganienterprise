<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TelegramAdmin;
use App\Models\TelegramReplySession;
use App\Services\GuestTokenService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TelegramAdminReplyTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;
    protected string $testSecret;
    protected string $adminChatId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testSecret  = 'secret_webhook_token_123';
        $this->adminChatId = '987654321';

        config([
            'services.telegram.bot_token'      => '8758560868:AAHSkhWa4l4bW9qJJxz1tI8CRubE94X3swE',
            'services.telegram.webhook_secret' => $this->testSecret,
        ]);

        $this->tokenService = new GuestTokenService();
        Http::preventStrayRequests();
        Cache::flush();
    }

    /** @test */
    public function valid_admin_can_start_reply_session_and_send_reply_to_customer()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true, 'result' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => $this->adminChatId,
            'role'             => 'admin',
            'active'           => true,
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        // 1. Admin presses [Reply] button via Telegram Callback Query
        $callbackResponse = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 1001,
                'callback_query' => [
                    'id'   => 'cb_123',
                    'data' => "reply_{$conversation->uuid}",
                    'from' => ['id' => $this->adminChatId],
                    'message' => ['chat' => ['id' => $this->adminChatId]],
                ],
            ]);

        $callbackResponse->assertStatus(200);
        $callbackResponse->assertJson(['status' => 'reply_session_started']);

        // Assert TelegramReplySession created in DB
        $this->assertDatabaseHas('telegram_reply_sessions', [
            'telegram_chat_id' => $this->adminChatId,
            'conversation_id' => $conversation->id,
        ]);

        // 2. Admin sends text reply message
        $replyTextResponse = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 1002,
                'message' => [
                    'message_id' => 888,
                    'chat'       => ['id' => $this->adminChatId],
                    'text'       => 'Hello, this is human support! How can I help you?',
                ],
            ]);

        $replyTextResponse->assertStatus(200);
        $replyTextResponse->assertJson(['status' => 'reply_sent']);

        // Assert admin message saved in database
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type'     => 'admin',
            'message'         => 'Hello, this is human support! How can I help you?',
        ]);

        // Assert reply session cleared after use
        $this->assertDatabaseMissing('telegram_reply_sessions', [
            'telegram_chat_id' => $this->adminChatId,
        ]);
    }

    /** @test */
    public function unauthorized_telegram_user_cannot_start_session_or_reply()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        $unauthorizedChatId = '111222333';

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        // Unauthorized user attempts callback query
        $response = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 2001,
                'callback_query' => [
                    'id'   => 'cb_999',
                    'data' => "reply_{$conversation->uuid}",
                    'from' => ['id' => $unauthorizedChatId],
                    'message' => ['chat' => ['id' => $unauthorizedChatId]],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'unauthorized_chat_id']);

        $this->assertDatabaseMissing('telegram_reply_sessions', [
            'telegram_chat_id' => $unauthorizedChatId,
        ]);
    }

    /** @test */
    public function expired_reply_session_blocks_reply_and_clears_session()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => $this->adminChatId,
            'role'             => 'admin',
            'active'           => true,
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        // Create expired reply session
        TelegramReplySession::create([
            'telegram_chat_id' => $this->adminChatId,
            'conversation_id' => $conversation->id,
            'expires_at'       => now()->subMinutes(1), // EXPIRED
        ]);

        $response = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 3001,
                'message' => [
                    'chat' => ['id' => $this->adminChatId],
                    'text' => 'Late reply',
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'session_expired']);

        // Assert message was NOT saved
        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $conversation->id,
            'message'         => 'Late reply',
        ]);

        // Assert expired session deleted
        $this->assertDatabaseMissing('telegram_reply_sessions', [
            'telegram_chat_id' => $this->adminChatId,
        ]);
    }

    /** @test */
    public function text_sent_without_active_session_does_not_post_to_customer()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => $this->adminChatId,
            'role'             => 'admin',
            'active'           => true,
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        $response = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 4001,
                'message' => [
                    'chat' => ['id' => $this->adminChatId],
                    'text' => 'Random admin message without session',
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'no_reply_session']);

        $this->assertDatabaseMissing('messages', [
            'message' => 'Random admin message without session',
        ]);
    }

    /** @test */
    public function closed_conversation_blocks_reply()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => $this->adminChatId,
            'role'             => 'admin',
            'active'           => true,
        ]);

        $token = $this->tokenService->generateToken();
        $closedConversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'closed', // CLOSED CONVERSATION
            'mode'             => 'human',
        ]);

        // Attempt callback on closed conversation
        $response = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 5001,
                'callback_query' => [
                    'id'   => 'cb_closed',
                    'data' => "reply_{$closedConversation->uuid}",
                    'from' => ['id' => $this->adminChatId],
                    'message' => ['chat' => ['id' => $this->adminChatId]],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'conversation_closed']);
    }

    /** @test */
    public function reply_is_strictly_routed_to_the_correct_customer_conversation_preventing_cross_talk()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => $this->adminChatId,
            'role'             => 'admin',
            'active'           => true,
        ]);

        $tokenA = $this->tokenService->generateToken();
        $convA = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($tokenA),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        $tokenB = $this->tokenService->generateToken();
        $convB = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($tokenB),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        // Admin selects Conv A
        $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 6001,
                'callback_query' => [
                    'id'   => 'cb_a',
                    'data' => "reply_{$convA->uuid}",
                    'from' => ['id' => $this->adminChatId],
                    'message' => ['chat' => ['id' => $this->adminChatId]],
                ],
            ]);

        // Admin sends reply text
        $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 6002,
                'message' => [
                    'chat' => ['id' => $this->adminChatId],
                    'text' => 'Targeted reply for Conv A',
                ],
            ]);

        // Assert message saved ONLY in Conv A
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $convA->id,
            'message'         => 'Targeted reply for Conv A',
        ]);

        // Assert Conv B has NO admin message
        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $convB->id,
            'message'         => 'Targeted reply for Conv A',
        ]);
    }

    /** @test */
    public function duplicate_telegram_webhook_updates_are_processed_only_once()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        TelegramAdmin::create([
            'telegram_chat_id' => $this->adminChatId,
            'role'             => 'admin',
            'active'           => true,
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'human',
        ]);

        TelegramReplySession::create([
            'telegram_chat_id' => $this->adminChatId,
            'conversation_id' => $conversation->id,
            'expires_at'       => now()->addMinutes(15),
        ]);

        $payload = [
            'update_id' => 778899,
            'message'   => [
                'chat' => ['id' => $this->adminChatId],
                'text' => 'Single execution test message',
            ],
        ];

        // 1st request -> Processes reply
        $first = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', $payload);

        $first->assertStatus(200);
        $first->assertJson(['status' => 'reply_sent']);

        // 2nd request with SAME update_id -> Intercepted by Idempotency check
        $second = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => $this->testSecret])
            ->postJson('/api/telegram/webhook', $payload);

        $second->assertStatus(200);
        $second->assertJson(['status' => 'already_processed']);

        // Assert only ONE message record created
        $this->assertEquals(1, Message::where('conversation_id', $conversation->id)->where('message', 'Single execution test message')->count());
    }
}
