<?php

namespace App\Services\AI\Tools;

use App\Models\Order;
use App\Models\OrderDetails;

class GetOrderDetailsTool
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
                'name'        => 'get_order_details',
                'description' => 'Retrieve full line item details for an order by order ID or invoice ID. Logged-in customers are checked automatically. Guests must provide their mobile phone number for verification.',
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
     * Execute order details lookup strictly bound to auth('customer')->id() or verified guest phone.
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
        $query = Order::with(['status', 'orderdetails', 'shipping', 'payment', 'customer'])
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
                'error'                   => 'Authentication required. Guests must log in or complete verification to view order details.',
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

        // 3. Map safe itemized line items (Never include purchase_price!)
        $items = $order->orderdetails->map(function (OrderDetails $item) {
            return [
                'product_name' => (string) $item->product_name,
                'unit_price'   => (float) $item->sale_price,
                'quantity'     => (int) $item->qty,
                'total_price'  => (float) ($item->sale_price * $item->qty),
            ];
        })->values()->toArray();

        return [
            'success'         => true,
            'found'           => true,
            'id'              => (int) $order->id,
            'invoice_id'      => (string) $order->invoice_id,
            'total_amount'    => (float) $order->amount,
            'discount'        => (float) $order->discount,
            'shipping_charge' => (float) $order->shipping_charge,
            'order_status'    => $order->status ? $order->status->name : (string) $order->order_status,
            'created_at'      => $order->created_at ? $order->created_at->toDateTimeString() : null,
            'items'           => $items,
        ];
    }
}
