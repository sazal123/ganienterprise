<?php

namespace App\Services\AI\Tools;

use App\Models\Order;

class GetOrderStatusTool
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
                'name'        => 'get_order_status',
                'description' => 'Check the status and shipping progress of an order by order ID or invoice ID. Logged-in customers are authenticated automatically. Guests must provide their mobile phone number for secure verification.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'order_id' => [
                            'type'        => 'string',
                            'description' => 'The order ID or invoice ID to check (e.g. 582914 or INV-1001).',
                        ],
                        'phone' => [
                            'type'        => 'string',
                            'description' => 'Customer mobile phone number for guest verification if guest.',
                        ],
                    ],
                    'required'   => ['order_id'],
                ],
            ],
        ];
    }

    /**
     * Execute order status lookup strictly bound to auth('customer')->id() or verified guest phone number.
     *
     * @param string|int $orderId
     * @param string|null $phone
     * @param int|null $overrideCustomerId Optional customer ID override for testing/system contexts
     * @return array
     */
    public function execute($orderId, ?string $phone = null, ?int $overrideCustomerId = null): array
    {
        $customerId   = $overrideCustomerId ?? auth('customer')->id();
        $cleanOrderId = trim((string) $orderId);
        $cleanPhone   = trim((string) $phone);

        if (empty($cleanOrderId)) {
            return [
                'success' => false,
                'found'   => false,
                'error'   => 'Order ID or Invoice ID is required.',
            ];
        }

        // Build order query matching order_id/invoice_id
        $query = Order::with(['status', 'shipping', 'customer'])
            ->where(function ($q) use ($cleanOrderId) {
                $q->where('id', $cleanOrderId)
                  ->orWhere('invoice_id', $cleanOrderId);
            });

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } elseif (!empty($cleanPhone)) {
            $query->where(function ($q) use ($cleanPhone) {
                $q->whereHas('shipping', function ($sq) use ($cleanPhone) {
                    $sq->where('phone', 'LIKE', "%{$cleanPhone}%");
                })->orWhereHas('customer', function ($cq) use ($cleanPhone) {
                    $cq->where('phone', 'LIKE', "%{$cleanPhone}%");
                });
            });
        } else {
            return [
                'success'                 => false,
                'error'                   => 'Authentication required. Guests must log in or complete verification to check order status.',
                'requires_authentication' => true,
                'requires_verification'   => true,
            ];
        }

        $order = $query->first();

        // If order belongs to another customer, wrong phone, or does not exist, reject
        if (!$order) {
            return [
                'success' => false,
                'found'   => false,
                'error'   => 'Order not found or access denied.',
            ];
        }

        return [
            'success'      => true,
            'found'        => true,
            'id'           => (int) $order->id,
            'invoice_id'   => (string) $order->invoice_id,
            'order_status' => $order->status ? $order->status->name : (string) $order->order_status,
            'created_at'   => $order->created_at ? $order->created_at->toDateTimeString() : null,
        ];
    }
}
