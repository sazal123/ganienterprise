<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\KnowledgeArticle;
use App\Services\AI\Tools\KnowledgeSearchTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KnowledgeSearchToolTest extends TestCase
{
    use DatabaseTransactions;

    protected KnowledgeSearchTool $tool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tool = new KnowledgeSearchTool();
    }

    /** @test */
    public function it_finds_and_returns_published_policy_articles()
    {
        KnowledgeArticle::create([
            'title'    => 'Standard Return Policy',
            'content'  => 'Customers may return eligible items within 7 days of delivery for a full refund.',
            'category' => 'returns',
            'status'   => 'published',
        ]);

        KnowledgeArticle::create([
            'title'    => 'Shipping Options and Fees',
            'content'  => 'Standard shipping takes 2-3 business days across Bangladesh.',
            'category' => 'shipping',
            'status'   => 'published',
        ]);

        $returnResult   = $this->tool->search('return eligible items');
        $shippingResult = $this->tool->search('shipping options');

        $this->assertTrue($returnResult['found']);
        $this->assertGreaterThanOrEqual(1, $returnResult['count']);
        $this->assertEquals('Standard Return Policy', $returnResult['results'][0]['title']);

        $this->assertTrue($shippingResult['found']);
        $this->assertEquals('Shipping Options and Fees', $shippingResult['results'][0]['title']);
    }

    /** @test */
    public function it_ignores_unpublished_or_draft_knowledge_articles()
    {
        KnowledgeArticle::create([
            'title'    => 'Draft Secret Refund Policy',
            'content'  => 'Internal draft: Secret 30-day refund window for VIP customers.',
            'category' => 'refunds',
            'status'   => 'draft', // UNPUBLISHED DRAFT
        ]);

        $result = $this->tool->search('Secret 30-day refund');

        $this->assertFalse($result['found']);
        $this->assertEquals(0, $result['count']);
        $this->assertEquals(KnowledgeSearchTool::UNVERIFIED_FALLBACK_MESSAGE, $result['message']);
    }

    /** @test */
    public function it_filters_articles_by_policy_category()
    {
        KnowledgeArticle::create([
            'title'    => 'Cancellation Policy',
            'content'  => 'Orders can be cancelled prior to dispatch.',
            'category' => 'cancellation',
            'status'   => 'published',
        ]);

        KnowledgeArticle::create([
            'title'    => 'Payment Methods',
            'content'  => 'We accept bKash, Nagad, Cash on Delivery, and Credit Cards.',
            'category' => 'payment',
            'status'   => 'published',
        ]);

        $result = $this->tool->search('cancellation', 'cancellation');

        $this->assertTrue($result['found']);
        $titles = array_column($result['results'], 'title');
        $this->assertContains('Cancellation Policy', $titles);
    }

    /** @test */
    public function it_returns_exact_unverified_fallback_message_when_no_policy_matches()
    {
        $result = $this->tool->search('quantum space shipping option 999');

        $this->assertFalse($result['found']);
        $this->assertEquals(0, $result['count']);
        $this->assertEquals("I don't have verified information about that. Would you like me to connect you with our support team?", $result['message']);
    }
}
