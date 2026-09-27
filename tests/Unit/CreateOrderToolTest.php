<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\Payment;
use App\Services\AI\Tools\CreateOrderTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateOrderToolTest extends TestCase
{
    use DatabaseTransactions;

    protected CreateOrderTool $tool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tool = new CreateOrderTool();
    }

    /** @test */
    public function it_successfully_creates_order_for_valid_product()
    {
        $category = Category::create([
            'name'       => 'Test Electronics',
            'slug'       => 'test-electronics',
            'status'     => 1,
            'front_view' => 1,
        ]);

        $product = Product::create([
            'name'           => 'Test iPhone 15 Pro',
            'slug'           => 'test-iphone-15-pro',
            'product_code'   => 'TEST-IPH-15P',
            'category_id'    => $category->id,
            'purchase_price' => 100000,
            'old_price'      => 130000,
            'new_price'      => 120000,
            'stock'          => 10,
            'status'         => 1,
        ]);

        $result = $this->tool->create([
            'product_query'    => 'Test iPhone 15 Pro',
            'customer_name'    => 'Kazi Rahim',
            'customer_phone'   => '01711223344',
            'customer_address' => 'House 42, Road 11, Dhanmondi, Dhaka',
            'quantity'         => 1,
            'area'             => 'Inside Dhaka',
            'payment_method'   => 'Cash on Delivery',
        ]);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['invoice_id']);
        $this->assertEquals('Test iPhone 15 Pro', $result['product_name']);
        $this->assertEquals(120060, $result['total_amount']);

        // Verify Database Persistence
        $order = Order::find($result['order_id']);
        $this->assertNotNull($order);
        $this->assertEquals(120060, $order->amount);

        $details = OrderDetails::where('order_id', $order->id)->first();
        $this->assertNotNull($details);
        $this->assertEquals($product->id, $details->product_id);

        $shipping = Shipping::where('order_id', $order->id)->first();
        $this->assertNotNull($shipping);
        $this->assertEquals('Kazi Rahim', $shipping->name);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('Cash on Delivery', $payment->payment_method);
    }

    /** @test */
    public function it_rejects_order_if_product_out_of_stock()
    {
        $category = Category::create([
            'name'       => 'Test Category',
            'slug'       => 'test-cat-out',
            'status'     => 1,
            'front_view' => 1,
        ]);

        $product = Product::create([
            'name'         => 'Out of Stock Product',
            'slug'         => 'out-of-stock-product',
            'product_code' => 'OOS-123',
            'category_id'  => $category->id,
            'purchase_price' => 400,
            'new_price'      => 500,
            'stock'        => 0,
            'status'       => 1,
        ]);

        $result = $this->tool->create([
            'product_query'    => 'Out of Stock Product',
            'customer_name'    => 'Ali Ahmed',
            'customer_phone'   => '01800000000',
            'customer_address' => 'Gulshan 2, Dhaka',
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('exceeds available stock', $result['error']);
    }

    /** @test */
    public function it_rejects_order_if_required_fields_missing()
    {
        $result = $this->tool->create([
            'product_query' => 'iPhone 15 Pro Max',
            'customer_name' => '',
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('required', $result['error']);
    }
}
