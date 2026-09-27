<?php

namespace App\Services\AI\Tools;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\Payment;
use App\Jobs\SendTelegramOrderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateOrderTool
{
    /**
     * Get OpenRouter / OpenAI compatible tool definition schema.
     *
     * @return array
     */
    public static function getDefinition(): array
    {
        return [
            'type'     => 'function',
            'function' => [
                'name'        => 'create_order',
                'description' => 'Place a new product order directly for the customer. Required parameters: product_query, customer_name, customer_phone, customer_address. Ask the customer for missing details before placing the order.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'product_query' => [
                            'type'        => 'string',
                            'description' => 'The name, model, code, or description of the product the customer wants to buy.',
                        ],
                        'customer_name' => [
                            'type'        => 'string',
                            'description' => 'Full name of the customer receiving the delivery.',
                        ],
                        'customer_phone' => [
                            'type'        => 'string',
                            'description' => 'Customer contact mobile phone number (11 digits e.g. 01712345678).',
                        ],
                        'customer_address' => [
                            'type'        => 'string',
                            'description' => 'Full delivery street address including area or district.',
                        ],
                        'quantity' => [
                            'type'        => 'integer',
                            'description' => 'Quantity of items to purchase (default 1).',
                        ],
                        'area' => [
                            'type'        => 'string',
                            'description' => 'Delivery location area: "Inside Dhaka" (৳60 delivery fee) or "Outside Dhaka" (৳120 delivery fee). Default "Inside Dhaka".',
                        ],
                        'payment_method' => [
                            'type'        => 'string',
                            'description' => 'Selected payment method, e.g. "Cash on Delivery" or "bKash". Default "Cash on Delivery".',
                        ],
                    ],
                    'required'   => ['product_query', 'customer_name', 'customer_phone', 'customer_address'],
                ],
            ],
        ];
    }

    /**
     * Execute order creation in database.
     *
     * @param array $args
     * @return array
     */
    public function create(array $args): array
    {
        $productQuery    = trim($args['product_query'] ?? '');
        $customerName    = trim($args['customer_name'] ?? '');
        $customerPhone   = trim($args['customer_phone'] ?? '');
        $customerAddress = trim($args['customer_address'] ?? '');
        $quantity        = max(1, (int) ($args['quantity'] ?? 1));
        $area            = trim($args['area'] ?? 'Inside Dhaka');
        $paymentMethod   = trim($args['payment_method'] ?? 'Cash on Delivery');

        if (empty($productQuery)) {
            return ['success' => false, 'error' => 'Product name or search term is required.'];
        }
        if (empty($customerName)) {
            return ['success' => false, 'error' => 'Customer name is required.'];
        }
        if (empty($customerPhone)) {
            return ['success' => false, 'error' => 'Customer phone number is required.'];
        }
        if (empty($customerAddress)) {
            return ['success' => false, 'error' => 'Delivery address is required.'];
        }

        // 1. Locate product in database
        $cleanQuery = Str::limit(strip_tags($productQuery), 100, '');
        $product = Product::where('status', 1)
            ->where(function ($q) use ($cleanQuery) {
                $q->where('name', 'LIKE', "%{$cleanQuery}%")
                  ->orWhere('slug', 'LIKE', "%{$cleanQuery}%")
                  ->orWhere('product_code', $cleanQuery);
            })
            ->first();

        if (!$product) {
            return [
                'success' => false,
                'error'   => "Product matching '{$productQuery}' could not be found in our store catalog.",
            ];
        }

        if ($product->stock < $quantity) {
            return [
                'success' => false,
                'error'   => "Requested quantity ({$quantity}) exceeds available stock ({$product->stock} units left) for '{$product->name}'.",
            ];
        }

        // 2. Determine pricing & delivery charges
        $salePrice      = (float) $product->new_price;
        $purchasePrice  = (float) ($product->purchase_price ?? 0);
        $shippingCharge = (str_contains(strtolower($area), 'outside')) ? 120.0 : 60.0;
        $subtotal       = $salePrice * $quantity;
        $totalAmount    = $subtotal + $shippingCharge;

        // 3. Resolve customer ID (authenticated or create/find customer by phone)
        $customerId = auth('customer')->id();

        if (!$customerId) {
            $existingCustomer = \App\Models\Customer::where('phone', $customerPhone)->first();
            if ($existingCustomer) {
                $customerId = $existingCustomer->id;
            } else {
                $email = !empty($args['customer_email']) ? trim($args['customer_email']) : ($customerPhone . '@customer.local');
                $newCustomer = \App\Models\Customer::create([
                    'name'     => $customerName,
                    'phone'    => $customerPhone,
                    'email'    => $email,
                    'address'  => $customerAddress,
                    'slug'     => Str::slug($customerName) . '-' . Str::random(5),
                    'password' => bcrypt(Str::random(12)),
                    'status'   => 1,
                ]);
                $customerId = $newCustomer->id;
            }
        }

        // 3. Perform database insertion within a transaction
        try {
            DB::beginTransaction();

            $invoiceId = (string) rand(100000, 999999);

            $order = Order::create([
                'invoice_id'      => $invoiceId,
                'amount'          => $totalAmount,
                'discount'        => 0,
                'shipping_charge' => $shippingCharge,
                'customer_id'     => $customerId,
                'order_status'    => 1, // 1 = Pending
                'order_date'      => now()->toDateString(),
            ]);

            OrderDetails::create([
                'order_id'       => $order->id,
                'product_id'     => $product->id,
                'product_name'   => $product->name,
                'purchase_price' => $purchasePrice,
                'sale_price'     => $salePrice,
                'qty'            => $quantity,
            ]);

            Shipping::create([
                'order_id'    => $order->id,
                'customer_id' => $customerId,
                'name'        => $customerName,
                'phone'       => $customerPhone,
                'address'     => $customerAddress,
                'area'        => $area,
            ]);

            Payment::create([
                'order_id'       => $order->id,
                'customer_id'    => $customerId,
                'amount'         => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => 'Pending',
            ]);

            // Update inventory stock
            $product->decrement('stock', $quantity);
            $product->increment('sold', $quantity);

            DB::commit();

            // Dispatch Telegram Notification
            try {
                SendTelegramOrderNotification::dispatch($order->fresh());
            } catch (\Throwable $e) {
                Log::info('[CreateOrderTool] Telegram order notification skipped: ' . $e->getMessage());
            }

            return [
                'success'          => true,
                'order_id'         => $order->id,
                'invoice_id'       => (string) $invoiceId,
                'product_name'     => $product->name,
                'quantity'         => $quantity,
                'unit_price'       => $salePrice,
                'shipping_charge'  => $shippingCharge,
                'total_amount'     => $totalAmount,
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'delivery_address' => $customerAddress,
                'payment_method'   => $paymentMethod,
                'order_status'     => 'Pending',
                'message'          => "Order #{$invoiceId} for {$product->name} has been successfully placed! Total: ৳{$totalAmount} (including ৳{$shippingCharge} delivery fee).",
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[CreateOrderTool] Order creation failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error'   => 'An internal database error occurred while creating your order. Please try again.',
            ];
        }
    }
}
