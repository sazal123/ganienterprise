<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Conversation;
use App\Services\GuestTokenService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;

class ProductSearchToolIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.openrouter.api_key' => 'sk-or-v1-test-key-999']);
        $this->tokenService = new GuestTokenService();
        Http::preventStrayRequests();
    }

    /** @test */
    public function openrouter_tool_calling_executes_product_search_and_returns_final_ai_answer()
    {
        $category = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'status' => 1,
            'front_view' => 1,
        ]);

        $product = Product::create([
            'name' => 'Gani Flagship Smartphone Pro',
            'slug' => 'gani-smartphone-pro',
            'product_code' => 'PHONE-PRO-01',
            'category_id' => $category->id,
            'purchase_price' => 600, // CONFIDENTIAL COST
            'new_price' => 999,
            'stock' => 10,
            'description' => 'Flagship smartphone with 108MP camera and 5G connectivity.',
            'status' => 1,
        ]);

        // Sequence HTTP fakes:
        // 1st request to OpenRouter returns a tool call request for product_search
        // 2nd request to OpenRouter receives tool result and returns final assistant text
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
                ->push([
                    'choices' => [
                        [
                            'finish_reason' => 'tool_calls',
                            'message' => [
                                'role' => 'assistant',
                                'content' => null,
                                'tool_calls' => [
                                    [
                                        'id' => 'call_search_123',
                                        'type' => 'function',
                                        'function' => [
                                            'name' => 'product_search',
                                            'arguments' => json_encode(['query' => 'Gani Flagship Smartphone']),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200)
                ->push([
                    'choices' => [
                        [
                            'finish_reason' => 'stop',
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Yes, we have the Gani Flagship Smartphone Pro available in stock for $999.00.',
                            ],
                        ],
                    ],
                    'usage' => [
                        'prompt_tokens' => 50,
                        'completion_tokens' => 20,
                        'total_tokens' => 70,
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
                'message' => 'Do you have the Gani Flagship Smartphone?',
            ]);

        $response->assertStatus(201);
        $data = $response->json('data');

        $this::assertEquals('Yes, we have the Gani Flagship Smartphone Pro available in stock for $999.00.', $data['ai_message']['message']);

        // Assert database records
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'ai',
            'message' => 'Yes, we have the Gani Flagship Smartphone Pro available in stock for $999.00.',
        ]);

        // Verify two HTTP requests were sent to OpenRouter (1st for tool request, 2nd with tool results)
        Http::assertSentCount(2);

        // Verify second HTTP request payload contained the tool result
        Http::assertSent(function ($request) {
            $data = $request->data();
            $messages = $data['messages'] ?? [];

            foreach ($messages as $msg) {
                if (($msg['role'] ?? '') === 'tool' && ($msg['name'] ?? '') === 'product_search') {
                    return str_contains($msg['content'], 'Gani Flagship Smartphone Pro') &&
                           !str_contains($msg['content'], 'purchase_price') &&
                           !str_contains($msg['content'], '600');
                }
            }

            return false;
        });
    }
}
