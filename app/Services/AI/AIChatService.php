<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AIChatService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $defaultModel;
    protected int $timeout;
    protected int $retries;

    public function __construct(array $config = [])
    {
        $openrouterConfig = config('services.openrouter', []);

        $this->apiKey       = $config['api_key'] ?? $openrouterConfig['api_key'] ?? '';
        $this->baseUrl      = rtrim($config['base_url'] ?? $openrouterConfig['base_url'] ?? 'https://openrouter.ai/api/v1', '/');
        $this->defaultModel = $config['model'] ?? $openrouterConfig['model'] ?? 'openai/gpt-4o-mini';
        $this->timeout      = (int) ($config['timeout'] ?? $openrouterConfig['timeout'] ?? 30);
        $this->retries      = (int) ($config['retries'] ?? $openrouterConfig['retries'] ?? 3);
    }

    /**
     * Send messages array to OpenRouter API.
     *
     * @param array $messages Array of message items: [['role' => 'user'|'assistant'|'system', 'content' => '...']]
     * @param array $options Custom parameters (system_prompt, model, temperature, max_tokens, tools, timeout)
     * @return array Standardized result array with success, content, usage, and error safety.
     */
    public function sendMessage(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            Log::error('[AIChatService] OpenRouter API key is missing.');
            return [
                'success'     => false,
                'error'       => 'AI service is not properly configured.',
                'status_code' => 500,
            ];
        }

        $formattedMessages = $this->formatMessages($messages, $options['system_prompt'] ?? null);

        $payload = [
            'model'    => $options['model'] ?? $this->defaultModel,
            'messages' => $formattedMessages,
        ];

        $payload['max_tokens'] = (int) ($options['max_tokens'] ?? config('services.openrouter.max_tokens', 1000));

        if (isset($options['temperature']) && is_numeric($options['temperature'])) {
            $payload['temperature'] = (float) $options['temperature'];
        }

        if (!empty($options['tools']) && is_array($options['tools'])) {
            $payload['tools'] = $options['tools'];
        }

        $requestTimeout = (int) ($options['timeout'] ?? $this->timeout);

        try {
            $url = "{$this->baseUrl}/chat/completions";

            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer'  => config('app.url', 'http://localhost'),
                'X-Title'       => config('app.name', 'Gani Enterprise'),
                'Content-Type'  => 'application/json',
            ];

            $response = Http::withHeaders($headers)
                ->timeout($requestTimeout)
                ->retry($this->retries, 100, function ($exception, $request) {
                    // Retry on connection exceptions or 5xx server errors
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                           ($exception instanceof \Illuminate\Http\Client\RequestException && $exception->response->status() >= 500);
                }, throw: false)
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseSuccessResponse($data, $payload['model']);
            }

            return $this->handleHttpErrorResponse($response);

        } catch (Throwable $e) {
            $sanitizedMessage = $this->sanitizeErrorMessage($e->getMessage());
            Log::error('[AIChatService] Exception during request: ' . $sanitizedMessage);

            return [
                'success'     => false,
                'error'       => 'AI service request failed due to a network or connection error.',
                'status_code' => 504,
            ];
        }
    }

    /**
     * Format and normalize messages array, prepending system prompt if provided.
     */
    protected function formatMessages(array $messages, ?string $systemPrompt = null): array
    {
        $formatted = [];

        if (!empty($systemPrompt)) {
            $formatted[] = [
                'role'    => 'system',
                'content' => trim($systemPrompt),
            ];
        }

        foreach ($messages as $msg) {
            if (is_string($msg)) {
                $formatted[] = [
                    'role'    => 'user',
                    'content' => $msg,
                ];
            } elseif (is_array($msg) && isset($msg['role'])) {
                $item = [
                    'role' => (string) $msg['role'],
                ];

                if (array_key_exists('content', $msg)) {
                    $item['content'] = $msg['content'];
                }

                if (!empty($msg['tool_calls'])) {
                    $item['tool_calls'] = $msg['tool_calls'];
                }

                if (!empty($msg['tool_call_id'])) {
                    $item['tool_call_id'] = $msg['tool_call_id'];
                }

                if (!empty($msg['name'])) {
                    $item['name'] = $msg['name'];
                }

                $formatted[] = $item;
            }
        }

        return $formatted;
    }

    /**
     * Parse valid OpenRouter completion response.
     */
    protected function parseSuccessResponse(?array $data, string $requestedModel): array
    {
        if (empty($data) || !isset($data['choices']) || !is_array($data['choices']) || empty($data['choices'])) {
            Log::error('[AIChatService] Invalid or empty choices array in response.', ['data' => $data]);
            return [
                'success'     => false,
                'error'       => 'Invalid response format received from AI provider.',
                'status_code' => 502,
            ];
        }

        $choice       = $data['choices'][0];
        $message      = $choice['message'] ?? [];
        $content      = $message['content'] ?? '';
        $role         = $message['role'] ?? 'assistant';
        $toolCalls    = $message['tool_calls'] ?? null;
        $finishReason = $choice['finish_reason'] ?? 'stop';
        $usage        = $data['usage'] ?? [];

        return [
            'success'       => true,
            'content'       => $content,
            'role'          => $role,
            'tool_calls'    => $toolCalls,
            'finish_reason' => $finishReason,
            'usage'         => [
                'prompt_tokens'     => $usage['prompt_tokens'] ?? 0,
                'completion_tokens' => $usage['completion_tokens'] ?? 0,
                'total_tokens'      => $usage['total_tokens'] ?? 0,
            ],
            'model'         => $data['model'] ?? $requestedModel,
            'raw'           => $data,
        ];
    }

    /**
     * Handle non-2xx HTTP responses (429 rate limit, 500 provider error, 401 unauthorized).
     */
    protected function handleHttpErrorResponse(\Illuminate\Http\Client\Response $response): array
    {
        $status = $response->status();

        Log::warning("[AIChatService] OpenRouter HTTP error {$status}", [
            'status' => $status,
            'body'   => $this->sanitizeErrorMessage($response->body()),
        ]);

        if ($status === 429) {
            return [
                'success'     => false,
                'error'       => 'AI service rate limit exceeded. Please try again shortly.',
                'status_code' => 429,
            ];
        }

        if ($status >= 500) {
            return [
                'success'     => false,
                'error'       => 'AI service provider error.',
                'status_code' => 500,
            ];
        }

        return [
            'success'     => false,
            'error'       => 'Failed to generate AI response.',
            'status_code' => $status,
        ];
    }

    /**
     * Remove any trace of the API key from log output or error strings.
     */
    protected function sanitizeErrorMessage(string $message): string
    {
        if (empty($this->apiKey)) {
            return $message;
        }

        return str_replace($this->apiKey, '***REDACTED_API_KEY***', $message);
    }
}
