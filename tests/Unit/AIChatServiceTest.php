<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AI\AIChatService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatServiceTest extends TestCase
{
    protected AIChatService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure HTTP fakes are active before every test
        Http::preventStrayRequests();

        $this->service = new AIChatService([
            'api_key'   => 'sk-or-v1-test-key-1234567890abcdef',
            'base_url'  => 'https://openrouter.ai/api/v1',
            'model'     => 'openai/gpt-4o-mini',
            'timeout'   => 5,
            'retries'   => 1,
        ]);
    }

    /** @test */
    public function it_successfully_generates_ai_chat_response()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'id' => 'gen-12345',
                'model' => 'openai/gpt-4o-mini',
                'choices' => [
                    [
                        'finish_reason' => 'stop',
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Hello! How can I assist you today?',
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 15,
                    'completion_tokens' => 10,
                    'total_tokens' => 25,
                ],
            ], 200),
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'Hi there'],
        ];

        $result = $this->service->sendMessage($messages);

        $this::assertTrue($result['success']);
        $this::assertEquals('Hello! How can I assist you today?', $result['content']);
        $this::assertEquals('assistant', $result['role']);
        $this::assertEquals('openai/gpt-4o-mini', $result['model']);
        $this::assertEquals(25, $result['usage']['total_tokens']);

        // Assert request was sent with proper authorization header and payload
        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer sk-or-v1-test-key-1234567890abcdef') &&
                   $request->url() === 'https://openrouter.ai/api/v1/chat/completions' &&
                   $request['model'] === 'openai/gpt-4o-mini' &&
                   $request['messages'][0]['content'] === 'Hi there';
        });
    }

    /** @test */
    public function it_passes_system_prompt_custom_model_temperature_max_tokens_and_tools()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'finish_reason' => 'stop',
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Sample response',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'What is order #1001 status?'],
        ];

        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_order_status',
                    'description' => 'Check shipping status of an order',
                ],
            ],
        ];

        $result = $this->service->sendMessage($messages, [
            'system_prompt' => 'You are an e-commerce support assistant.',
            'model' => 'anthropic/claude-3.5-sonnet',
            'temperature' => 0.2,
            'max_tokens' => 500,
            'tools' => $tools,
        ]);

        $this::assertTrue($result['success']);

        Http::assertSent(function ($request) use ($tools) {
            $payload = $request->data();

            return $payload['model'] === 'anthropic/claude-3.5-sonnet' &&
                   $payload['temperature'] === 0.2 &&
                   $payload['max_tokens'] === 500 &&
                   $payload['messages'][0]['role'] === 'system' &&
                   $payload['messages'][0]['content'] === 'You are an e-commerce support assistant.' &&
                   $payload['messages'][1]['content'] === 'What is order #1001 status?' &&
                   $payload['tools'] === $tools;
        });
    }

    /** @test */
    public function it_handles_http_429_rate_limit_gracefully()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Rate limit exceeded'],
            ], 429),
        ]);

        $result = $this->service->sendMessage([['role' => 'user', 'content' => 'Hello']]);

        $this::assertFalse($result['success']);
        $this::assertEquals(429, $result['status_code']);
        $this::assertStringContainsString('rate limit', strtolower($result['error']));
    }

    /** @test */
    public function it_handles_http_500_server_error_gracefully()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Internal server error'],
            ], 500),
        ]);

        $result = $this->service->sendMessage([['role' => 'user', 'content' => 'Hello']]);

        $this::assertFalse($result['success']);
        $this::assertEquals(500, $result['status_code']);
        $this::assertStringContainsString('provider error', strtolower($result['error']));
    }

    /** @test */
    public function it_handles_invalid_or_malformed_json_responses()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(['choices' => []], 200),
        ]);

        $result = $this->service->sendMessage([['role' => 'user', 'content' => 'Hello']]);

        $this::assertFalse($result['success']);
        $this::assertEquals(502, $result['status_code']);
        $this::assertStringContainsString('invalid response format', strtolower($result['error']));
    }

    /** @test */
    public function it_handles_network_timeout_or_connection_failure()
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('cURL error 28: Operation timed out');
        });

        $result = $this->service->sendMessage([['role' => 'user', 'content' => 'Hello']]);

        $this::assertFalse($result['success']);
        $this::assertEquals(504, $result['status_code']);
        $this::assertStringContainsString('connection error', strtolower($result['error']));
    }

    /** @test */
    public function it_never_exposes_or_logs_api_key()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([], 500),
        ]);

        $result = $this->service->sendMessage([['role' => 'user', 'content' => 'Hello']]);

        $this::assertFalse($result['success']);
        $this::assertStringNotContainsString('sk-or-v1-test-key-1234567890abcdef', json_encode($result));
    }
}
