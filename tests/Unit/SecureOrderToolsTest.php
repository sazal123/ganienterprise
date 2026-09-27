<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Services\AI\Tools\GetCustomerOrdersTool;
use App\Services\AI\Tools\GetOrderStatusTool;
use App\Services\AI\Tools\GetOrderDetailsTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SecureOrderToolsTest extends TestCase
{
    use DatabaseTransactions;

    protected GetCustomerOrdersTool $customerOrdersTool;
    protected GetOrderStatusTool $orderStatusTool;
    protected GetOrderDetailsTool $orderDetailsTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerOrdersTool = new GetCustomerOrdersTool();
        $this->orderStatusTool    = new GetOrderStatusTool();
        $this->orderDetailsTool   = new GetOrderDetailsTool();
    }

    /** @test */
    public function customer_a_cannot_access_customer_b_order_status_or_details_idor_prevention()
    {
        $customerA = Customer::create([
            'name' => 'Customer Alpha',
            'slug' => 'cust-alpha',
            'phone' => '01800000001',
            'email' => 'alpha@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $customerB = Customer::create([
            'name' => 'Customer Beta',
            'slug' => 'cust-beta',
            'phone' => '01800000002',
            'email' => 'beta@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $orderB = Order::create([
            'invoice_id' => 'INV-B-999',
            'amount' => 1200,
            'discount' => 100,
            'shipping_charge' => 60,
            'customer_id' => $customerB->id,
            'order_status' => 'Processing',
        ]);

        OrderDetails::create([
            'order_id' => $orderB->id,
            'product_id' => 1,
            'product_name' => 'Customer B Private Item',
            'purchase_price' => 500, // CONFIDENTIAL COST
            'sale_price' => 1200,
            'qty' => 1,
        ]);

        // Customer A acts on the system
        $this->actingAs($customerA, 'customer');

        // Customer A tries to look up Customer B's order ID
        $statusResult = $this->orderStatusTool->execute($orderB->id);
        $detailsResult = $this->orderDetailsTool->execute($orderB->id);

        // IDOR Protection Assertions
        $this->assertFalse($statusResult['success']);
        $this->assertFalse($statusResult['found']);
        $this->assertEquals('Order not found or access denied.', $statusResult['error']);

        $this->assertFalse($detailsResult['success']);
        $this->assertFalse($detailsResult['found']);
        $this->assertEquals('Order not found or access denied.', $detailsResult['error']);
    }

    /** @test */
    public function guest_cannot_access_customer_orders_arbitrarily()
    {
        $customer = Customer::create([
            'name' => 'Store Customer',
            'slug' => 'store-customer',
            'phone' => '01800000003',
            'email' => 'storecust@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $order = Order::create([
            'invoice_id' => 'INV-C-888',
            'amount' => 500,
            'discount' => 0,
            'shipping_charge' => 50,
            'customer_id' => $customer->id,
            'order_status' => 'Pending',
        ]);

        // Unauthenticated Guest session
        auth('customer')->logout();

        $ordersResult  = $this->customerOrdersTool->execute();
        $statusResult  = $this->orderStatusTool->execute($order->id);
        $detailsResult = $this->orderDetailsTool->execute($order->id);

        $this->assertFalse($ordersResult['success']);
        $this->assertTrue($ordersResult['requires_authentication']);

        $this->assertFalse($statusResult['success']);
        $this->assertTrue($statusResult['requires_authentication']);

        $this->assertFalse($detailsResult['success']);
        $this->assertTrue($detailsResult['requires_authentication']);
    }

    /** @test */
    public function guest_can_track_order_using_valid_phone_verification()
    {
        $customer = Customer::create([
            'name'     => 'Guest Buyer',
            'slug'     => 'guest-buyer',
            'phone'    => '01799887766',
            'email'    => 'guestbuyer@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $order = Order::create([
            'invoice_id'      => '889900',
            'customer_id'     => $customer->id,
            'amount'          => 1500,
            'discount'        => 0,
            'shipping_charge' => 60,
            'order_status'    => 1,
            'order_date'      => now()->toDateString(),
        ]);

        \App\Models\Shipping::create([
            'order_id'    => $order->id,
            'customer_id' => $customer->id,
            'name'        => 'Guest Buyer',
            'phone'       => '01799887766',
            'address'     => 'Dhaka, Bangladesh',
            'area'        => 'Inside Dhaka',
        ]);

        // Unverified guest (wrong phone) -> Access Denied
        $failedResult = (new \App\Services\AI\Tools\GetOrderStatusTool())->execute('889900', '01800000000');
        $this->assertFalse($failedResult['success']);

        // Verified guest (correct phone) -> Returns Order Status
        $successResult = (new \App\Services\AI\Tools\GetOrderStatusTool())->execute('889900', '01799887766');
        $this->assertTrue($successResult['success']);
        $this->assertEquals('889900', $successResult['invoice_id']);
    }

    /** @test */
    public function customer_a_changing_order_id_in_parameters_cannot_access_customer_b_data()
    {
        $customerA = Customer::create([
            'name' => 'User One',
            'slug' => 'user-one',
            'phone' => '01800000004',
            'email' => 'userone@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $customerB = Customer::create([
            'name' => 'User Two',
            'slug' => 'user-two',
            'phone' => '01800000005',
            'email' => 'usertwo@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $orderA = Order::create([
            'invoice_id' => 'INV-A-101',
            'amount' => 300,
            'discount' => 0,
            'shipping_charge' => 50,
            'customer_id' => $customerA->id,
            'order_status' => 'Delivered',
        ]);

        $orderB = Order::create([
            'invoice_id' => 'INV-B-202',
            'amount' => 4500,
            'discount' => 500,
            'shipping_charge' => 100,
            'customer_id' => $customerB->id,
            'order_status' => 'Processing',
        ]);

        $this->actingAs($customerA, 'customer');

        // Customer A legitimately checks their own order
        $ownStatus = $this->orderStatusTool->execute($orderA->id);
        $this->assertTrue($ownStatus['success']);
        $this->assertTrue($ownStatus['found']);

        // Customer A changes parameter to Customer B's order ID
        $tamperedStatus = $this->orderStatusTool->execute($orderB->id);
        $tamperedDetails = $this->orderDetailsTool->execute($orderB->invoice_id);

        $this->assertFalse($tamperedStatus['found']);
        $this->assertFalse($tamperedDetails['found']);
    }

    /** @test */
    public function invalid_or_non_existent_order_id_returns_not_found()
    {
        $customer = Customer::create([
            'name' => 'Valid User',
            'slug' => 'valid-user',
            'phone' => '01800000006',
            'email' => 'validuser@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->actingAs($customer, 'customer');

        $statusResult = $this->orderStatusTool->execute(99999999);
        $detailsResult = $this->orderDetailsTool->execute('NON_EXISTENT_INV');

        $this->assertFalse($statusResult['found']);
        $this->assertFalse($detailsResult['found']);
    }

    /** @test */
    public function authenticated_customer_can_access_their_own_orders_and_purchase_price_is_hidden()
    {
        $customer = Customer::create([
            'name' => 'Owner Customer',
            'slug' => 'owner-customer',
            'phone' => '01800000007',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $order = Order::create([
            'invoice_id' => 'INV-OWN-777',
            'amount' => 1500,
            'discount' => 100,
            'shipping_charge' => 60,
            'customer_id' => $customer->id,
            'order_status' => 'Shipped',
        ]);

        OrderDetails::create([
            'order_id' => $order->id,
            'product_id' => 1,
            'product_name' => 'Whitelisted Product Item',
            'purchase_price' => 700, // CONFIDENTIAL SUPPLIER COST - MUST BE EXCLUDED!
            'sale_price' => 1500,
            'qty' => 1,
        ]);

        $this->actingAs($customer, 'customer');

        $ordersList = $this->customerOrdersTool->execute();
        $this->assertTrue($ordersList['success']);
        $this->assertEquals(1, $ordersList['count']);
        $this->assertEquals('INV-OWN-777', $ordersList['orders'][0]['invoice_id']);

        $details = $this->orderDetailsTool->execute($order->id);
        $this->assertTrue($details['success']);
        $this->assertEquals('INV-OWN-777', $details['invoice_id']);
        $this->assertCount(1, $details['items']);

        $item = $details['items'][0];
        $this->assertEquals('Whitelisted Product Item', $item['product_name']);
        $this->assertEquals(1500.0, $item['unit_price']);

        // Assert purchase_price is NEVER present
        $this->assertArrayNotHasKey('purchase_price', $item);
        $this->assertStringNotContainsString('purchase_price', json_encode($details));
        $this->assertStringNotContainsString('700', json_encode($details));
    }
}
