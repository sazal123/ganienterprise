<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Courierapi;
use App\Services\AI\Tools\ShippingStatusTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;

class ShippingStatusToolTest extends TestCase
{
    use DatabaseTransactions;

    protected ShippingStatusTool $tool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tool = new ShippingStatusTool();
        Http::preventStrayRequests();
    }

    /** @test */
    public function it_fetches_and_normalizes_mocked_external_courier_api_tracking_status()
    {
        // Seed active courier configuration in database
        Courierapi::create([
            'type'       => 'steadfast',
            'api_key'    => 'steadfast_test_api_key',
            'secret_key' => 'steadfast_test_secret_key',
            'url'        => 'https://packzy.com/api/v1',
        ]);

        $customer = Customer::create([
            'name'     => 'Ship Customer',
            'slug'     => 'ship-customer',
            'phone'    => '01700000001',
            'email'    => 'shipcust@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $order = Order::create([
            'invoice_id'      => 'INV-SHIP-101',
            'amount'          => 1500,
            'discount'        => 0,
            'shipping_charge' => 60,
            'customer_id'     => $customer->id,
            'order_status'    => 5, // Shipped
        ]);

        // Mock external Steadfast API response
        Http::fake([
            'https://packzy.com/api/v1/status_by_cid/INV-SHIP-101' => Http::response([
                'status'             => 200,
                'delivery_status'    => 'In Transit',
                'consignment_id'     => 'ST-998877',
                'updated_at'         => '2026-08-19 11:30:00',
                'estimated_delivery' => '2026-08-22',
            ], 200),
        ]);

        $this->actingAs($customer, 'customer');

        $result = $this->tool->execute($order->id);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['found']);
        $this->assertEquals('Steadfast Courier', $result['courier']);
        $this->assertEquals('ST-998877', $result['tracking_number']);
        $this->assertEquals('In Transit', $result['status']);
        $this->assertEquals('2026-08-19 11:30:00', $result['last_update']);
        $this->assertEquals('2026-08-22', $result['estimated_delivery']);
    }

    /** @test */
    public function it_falls_back_to_internal_order_shipping_status_when_courier_api_is_not_configured()
    {
        $customer = Customer::create([
            'name'     => 'Internal Ship Customer',
            'slug'     => 'internal-ship-cust',
            'phone'    => '01700000002',
            'email'    => 'internalship@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $order = Order::create([
            'invoice_id'      => 'INV-SHIP-202',
            'amount'          => 800,
            'discount'        => 0,
            'shipping_charge' => 50,
            'customer_id'     => $customer->id,
            'order_status'    => 'Processing',
        ]);

        $this->actingAs($customer, 'customer');

        $result = $this->tool->execute($order->id);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['found']);
        $this->assertEquals('Standard Delivery', $result['courier']);
        $this->assertEquals('INV-SHIP-202', $result['tracking_number']);
        $this->assertEquals('Processing', $result['status']);
    }

    /** @test */
    public function it_returns_unavailable_when_no_courier_or_shipment_data_exists()
    {
        $customer = Customer::create([
            'name'     => 'Empty Tracking Customer',
            'slug'     => 'empty-tracking-cust',
            'phone'    => '01700000003',
            'email'    => 'emptytracking@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $order = Order::create([
            'invoice_id'      => 'INV-SHIP-303',
            'amount'          => 200,
            'discount'        => 0,
            'shipping_charge' => 0,
            'customer_id'     => $customer->id,
            'order_status'    => '0',
        ]);

        $this->actingAs($customer, 'customer');

        $result = $this->tool->execute($order->id);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['found']);
        $this->assertEquals('unavailable', $result['courier']);
        $this->assertEquals('unavailable', $result['status']);
        $this->assertNull($result['tracking_number']);
    }

    /** @test */
    public function customer_a_cannot_access_customer_b_shipping_status_idor_protection()
    {
        $customerA = Customer::create([
            'name'     => 'Ship Cust A',
            'slug'     => 'ship-cust-a',
            'phone'    => '01700000004',
            'email'    => 'shipa@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $customerB = Customer::create([
            'name'     => 'Ship Cust B',
            'slug'     => 'ship-cust-b',
            'phone'    => '01700000005',
            'email'    => 'shipb@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $orderB = Order::create([
            'invoice_id'      => 'INV-SHIP-404',
            'amount'          => 3000,
            'discount'        => 0,
            'shipping_charge' => 100,
            'customer_id'     => $customerB->id,
            'order_status'    => 'Shipped',
        ]);

        $this->actingAs($customerA, 'customer');

        $result = $this->tool->execute($orderB->id);

        $this->assertFalse($result['success']);
        $this->assertFalse($result['found']);
        $this->assertEquals('Order not found or access denied.', $result['error']);
    }

    /** @test */
    public function unauthenticated_guest_cannot_access_shipping_status()
    {
        $customer = Customer::create([
            'name'     => 'Guest Protected Customer',
            'slug'     => 'guest-prot-cust',
            'phone'    => '01700000006',
            'email'    => 'guestprot@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $order = Order::create([
            'invoice_id'      => 'INV-SHIP-505',
            'amount'          => 500,
            'discount'        => 0,
            'shipping_charge' => 50,
            'customer_id'     => $customer->id,
            'order_status'    => 'Processing',
        ]);

        auth('customer')->logout();

        $result = $this->tool->execute($order->id);

        $this->assertFalse($result['success']);
        $this->assertTrue($result['requires_authentication']);
    }
}
