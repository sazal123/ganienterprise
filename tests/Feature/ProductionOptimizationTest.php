<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AiUsageLog;
use App\Models\Product;
use App\Models\KnowledgeArticle;
use App\Services\AI\CustomerSupportAIService;
use App\Services\AI\AiMetricsService;
use App\Services\AI\Tools\ProductSearchTool;
use App\Services\AI\Tools\KnowledgeSearchTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductionOptimizationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.openrouter.api_key' => 'sk-or-v1-prod-optimization-test']);
        Http::preventStrayRequests();
        Cache::flush();
    }

    /** @test */
    public function ai_service_records_token_usage_cost_and_response_time_logs()
    {
        Http::fake([
            'https://openrouter.ai/*' => Http::response([
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'Production test response']],
                ],
                'usage' => [
                    'prompt_tokens'     => 120,
                    'completion_tokens' => 40,
                    'total_tokens'      => 160,
                ],
            ], 200),
        ]);

        $conversation = Conversation::create(['status' => 'open', 'mode' => 'ai']);
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'message'         => 'What hours are you open on weekends?',
        ]);

        $aiSupportService = app(CustomerSupportAIService::class);
        $responseMessage  = $aiSupportService->generateResponse($conversation, $message);

        $this->assertNotNull($responseMessage);

        $this->assertDatabaseHas('ai_usage_logs', [
            'conversation_id' => $conversation->id,
            'input_tokens'    => 120,
            'output_tokens'   => 40,
            'total_tokens'    => 160,
            'status'          => 'success',
        ]);

        $log = AiUsageLog::where('conversation_id', $conversation->id)->first();
        $this->assertGreaterThan(0, (float) $log->estimated_cost);
        $this->assertGreaterThanOrEqual(0, (int) $log->response_time_ms);
    }

    /** @test */
    public function public_search_tools_cache_results_safely()
    {
        KnowledgeArticle::create([
            'title'    => 'Production Return Policy',
            'content'  => 'Items can be returned within 7 days in original condition.',
            'category' => 'returns',
            'status'   => 'published',
        ]);

        $tool = new KnowledgeSearchTool();

        // 1st Search -> Hits DB & populates cache
        $res1 = $tool->search('Production Return Policy');
        $this->assertTrue($res1['found']);

        // Mutate DB row to verify 2nd Search comes from cache
        KnowledgeArticle::where('title', 'Production Return Policy')->update(['title' => 'Changed Title']);

        $res2 = $tool->search('Production Return Policy');
        $this->assertTrue($res2['found']);
        $this->assertEquals('Production Return Policy', $res2['results'][0]['title']);
    }

    /** @test */
    public function ai_metrics_service_calculates_production_analytics()
    {
        AiUsageLog::create([
            'model'            => 'openai/gpt-4o-mini',
            'input_tokens'     => 1000,
            'output_tokens'    => 500,
            'total_tokens'     => 1500,
            'estimated_cost'   => 0.00045,
            'response_time_ms' => 450,
            'status'           => 'success',
        ]);

        AiUsageLog::create([
            'model'            => 'openai/gpt-4o-mini',
            'input_tokens'     => 500,
            'output_tokens'    => 100,
            'total_tokens'     => 600,
            'estimated_cost'   => 0.000135,
            'response_time_ms' => 300,
            'status'           => 'failed',
        ]);

        $metricsService = new AiMetricsService();
        $metrics = $metricsService->getMetrics();

        $this->assertGreaterThanOrEqual(2, $metrics['total_requests']);
        $this->assertGreaterThanOrEqual(1, $metrics['failed_requests']);
        $this->assertGreaterThan(0, $metrics['failure_rate_pct']);
        $this->assertGreaterThan(0, $metrics['total_tokens']);
    }
}
