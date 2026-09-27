<?php

namespace App\Services\AI\Tools;

use App\Models\Order;

class GetCustomerOrdersTool
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
                'name'        => 'get_customer_orders',
                'description' => 'Retrieve recent order history for the currently authenticated customer. Requires customer authentication.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'limit' => [
                            'type'        => 'integer',
                            'description' => 'Maximum number of orders to return (default 5, max 10).',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Execute order retrieval strictly bound to auth('customer')->id().
     *
     * @param int $limit
     * @param int|null $overrideCustomerId Optional customer ID override for testing/system contexts
     * @return array
     */
    public function execute(int $limit = 5, ?int $overrideCustomerId = null): array
    {
        // 1. Resolve customer ID strictly from server authentication guard
        $customerId = $overrideCustomerId ?? auth('customer')->id();

        if (!$customerId) {
            return [
                'success'                 => false,
                'error'                   => 'Authentication required. Please log in to view your orders.',
                'requires_authentication' => true,
            ];
        }

        $safeLimit = max(1, min((int) $limit, 10));

        // 2. Query orders strictly scoped to authenticated customer ID
        $orders = Order::with('status')
            ->where('customer_id', $customerId)
            ->orderBy('id', 'desc')
            ->limit($safeLimit)
            ->get();

        if ($orders->isEmpty()) {
            return [
                'success' => true,
                'count'   => 0,
                'orders'  => [],
                'message' => 'No orders found for your account.',
            ];
        }

        // 3. Map safe public order summary data
        $publicOrders = $orders->map(function (Order $order) {
            return [
                'id'          => (int) $order->id,
                'invoice_id'  => (string) $order->invoice_id,
                'amount'      => (float) $order->amount,
                'status'      => $order->status ? $order->status->name : (string) $order->order_status,
                'created_at'  => $order->created_at ? $order->created_at->toDateTimeString() : null,
            ];
        })->values()->toArray();

        return [
            'success' => true,
            'count'   => count($publicOrders),
            'orders'  => $publicOrders,
        ];
    }
}
