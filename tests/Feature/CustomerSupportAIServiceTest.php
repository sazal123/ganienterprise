<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\GuestTokenService;
use App\Services\AI\CustomerSupportAIService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;

class CustomerSupportAIServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.openrouter.api_key' => 'sk-or-v1-test-key-123456']);
        $this->tokenService = new GuestTokenService();
        Http::preventStrayRequests();
    }

    /** @test */
    public function sending_hello_triggers_ai_service_and_saves_both_messages()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'finish_reason' => 'stop',
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Hello! Welcome to Gani Enterprise. How can I help you today?',
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 20,
                    'completion_tokens' => 15,
                    'total_tokens' => 35,
                ],
            ], 200),
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status' => 'open',
            'mode' => 'ai',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Hello',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertEquals('Hello', $data['user_message']['message']);
        $this->assertEquals('Hello! Welcome to Gani Enterprise. How can I help you today?', $data['ai_message']['message']);

        // Assert database records
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'guest',
            'message' => 'Hello',
        ]);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'ai',
            'message' => 'Hello! Welcome to Gani Enterprise. How can I help you today?',
        ]);

        // Assert AI token usage logged
        $this->assertDatabaseHas('ai_usage_logs', [
            'conversation_id' => $conversation->id,
            'total_tokens' => 35,
        ]);
    }

    /** @test */
    public function it_passes_system_prompt_and_respects_history_limit()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'finish_reason' => 'stop',
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'I have processed your request.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $customer = Customer::create([
            'name' => 'History Test Customer',
            'slug' => 'history-cust',
            'phone' => '01900000099',
            'email' => 'history@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $conversation = Conversation::create([
            'customer_id' => $customer->id,
            'status' => 'open',
            'mode' => 'ai',
        ]);

        // Seed 15 past messages in conversation
        for ($i = 1; $i <= 15; $i++) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => ($i % 2 === 0) ? 'ai' : 'customer',
                'message' => "Old message {$i}",
            ]);
        }

        config(['services.openrouter.history_limit' => 5]);

        $response = $this->actingAs($customer, 'customer')
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Latest new message',
            ]);

        $response->assertStatus(201);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $messages = $payload['messages'];

            // Index 0 must be system prompt
            $hasSystemPrompt = ($messages[0]['role'] === 'system') &&
                str_contains($messages[0]['content'], 'customer support assistant');

            // Must include history limit items (5 past + 1 new = 6 user/assistant messages)
            $nonSystemCount = count($messages) - 1;

            return $hasSystemPrompt && ($nonSystemCount <= 6);
        });
    }

    /** @test */
    public function openrouter_failure_returns_friendly_fallback_ai_response()
    {
        // Simulate OpenRouter HTTP 500 failure
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'error' => ['message' => 'OpenRouter Service Unavailable'],
            ], 500),
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status' => 'open',
            'mode' => 'ai',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Hello, anyone there?',
            ]);

        $response->assertStatus(201);
        $data = $response->json('data');

        $this->assertEquals('Hello, anyone there?', $data['user_message']['message']);
        $this->assertStringContainsString('trouble connecting', $data['ai_message']['message']);

        // Assert fallback AI message saved in database
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'ai',
        ]);
    }
}
