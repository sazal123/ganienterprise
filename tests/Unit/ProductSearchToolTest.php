<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Services\AI\Tools\ProductSearchTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductSearchToolTest extends TestCase
{
    use DatabaseTransactions;

    protected ProductSearchTool $tool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tool = new ProductSearchTool();
    }

    /** @test */
    public function it_returns_whitelisted_data_when_product_is_found()
    {
        $category = Category::create([
            'name' => 'Audio Electronics',
            'slug' => 'audio-electronics',
            'status' => 1,
            'front_view' => 1,
        ]);

        $product = Product::create([
            'name' => 'Wireless Noise Canceling Headphones',
            'slug' => 'wireless-nc-headphones',
            'product_code' => 'AUD-NC-001',
            'category_id' => $category->id,
            'purchase_price' => 150, // INTERNAL CONFIDENTIAL SUPPLIER COST
            'old_price' => 300,
            'new_price' => 250,
            'stock' => 15,
            'description' => 'Premium wireless noise canceling headphones with 30-hour battery life.',
            'status' => 1,
        ]);

        $result = $this->tool->search('AUD-NC-001');

        $this->assertTrue($result['found']);
        $this->assertGreaterThanOrEqual(1, $result['count']);

        $item = $result['results'][0];
        $this->assertEquals('Wireless Noise Canceling Headphones', $item['name']);
        $this->assertEquals('AUD-NC-001', $item['product_code']);
        $this->assertEquals('Audio Electronics', $item['category']);
        $this->assertEquals(250.0, $item['price']);
        $this->assertEquals(300.0, $item['old_price']);
        $this->assertEquals('In Stock', $item['availability']);
        $this->assertEquals(15, $item['stock_quantity']);
        $this->assertStringContainsString('Premium wireless', $item['description']);
    }

    /** @test */
    public function it_returns_not_found_when_product_does_not_exist()
    {
        $result = $this->tool->search('NONEXISTENT_ITEM_CODE_99999');

        $this->assertFalse($result['found']);
        $this->assertEquals(0, $result['count']);
        $this->assertEmpty($result['results']);
    }

    /** @test */
    public function it_correctly_reports_unavailable_out_of_stock_product()
    {
        $category = Category::create([
            'name' => 'Gadgets',
            'slug' => 'gadgets',
            'status' => 1,
        ]);

        $product = Product::create([
            'name' => 'Limited Edition Smart Watch',
            'slug' => 'smart-watch-limited',
            'product_code' => 'WATCH-009',
            'category_id' => $category->id,
            'purchase_price' => 500,
            'new_price' => 800,
            'stock' => 0, // OUT OF STOCK
            'description' => 'Sleek smart watch with health tracking.',
            'status' => 1,
        ]);

        $result = $this->tool->search('Smart Watch');

        $this->assertTrue($result['found']);
        $item = $result['results'][0];
        $this->assertEquals('Out of Stock', $item['availability']);
        $this->assertEquals(0, $item['stock_quantity']);
    }

    /** @test */
    public function it_safely_handles_malicious_search_input()
    {
        $maliciousQuery = "' OR '1'='1' -- <script>alert('xss')</script>";

        $result = $this->tool->search($maliciousQuery);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('found', $result);
    }

    /** @test */
    public function it_never_returns_internal_confidential_fields()
    {
        $category = Category::create([
            'name' => 'Internal Test',
            'slug' => 'internal-test',
            'status' => 1,
        ]);

        Product::create([
            'name' => 'Secret Cost Item',
            'slug' => 'secret-cost-item',
            'product_code' => 'SEC-001',
            'category_id' => $category->id,
            'purchase_price' => 42, // SECRET PURCHASE COST
            'new_price' => 100,
            'stock' => 5,
            'status' => 1,
        ]);

        $result = $this->tool->search('Secret Cost Item');

        $this->assertTrue($result['found']);
        $item = $result['results'][0];

        // Assert strictly whitelisted attributes are present
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayHasKey('price', $item);
        $this->assertArrayHasKey('availability', $item);

        // Assert internal confidential fields are NEVER present in output
        $this->assertArrayNotHasKey('purchase_price', $item);
        $this->assertArrayNotHasKey('cost_price', $item);
        $this->assertArrayNotHasKey('margin', $item);
        $this->assertArrayNotHasKey('profit', $item);
        $this->assertArrayNotHasKey('admin_notes', $item);
        $this->assertArrayNotHasKey('supplier', $item);

        // Raw serialized string check to guarantee no leaks
        $serialized = json_encode($item);
        $this->assertStringNotContainsString('purchase_price', $serialized);
        $this->assertStringNotContainsString('42', $serialized);
    }
}
