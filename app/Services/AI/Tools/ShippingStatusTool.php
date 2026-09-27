<?php

namespace App\Services\AI\Tools;

use App\Models\Order;
use App\Models\Courierapi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShippingStatusTool
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
                'name'        => 'get_shipping_status',
                'description' => 'Retrieve normalized shipping and courier tracking status for an order by order ID or invoice ID. Logged-in customers are checked automatically. Guests must provide their mobile phone number for verification.',
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
     * Execute shipping status lookup strictly bound to auth('customer')->id() or verified guest phone.
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
                'error'                   => 'Authentication required. Guests must log in or complete verification to check shipping status.',
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

        // 3. Check for configured Courier API integration
        $courierData = $this->queryCourierApi($order);

        if ($courierData) {
            return [
                'success'            => true,
                'found'              => true,
                'courier'            => $courierData['courier'],
                'tracking_number'    => $courierData['tracking_number'],
                'status'             => $courierData['status'],
                'last_update'        => $courierData['last_update'],
                'estimated_delivery' => $courierData['estimated_delivery'],
            ];
        }

        // 4. Fallback to order internal shipment status if valid
        $rawStatus = (string) $order->order_status;
        if (!empty($rawStatus) && $rawStatus !== '0' && $rawStatus !== 'none') {
            $statusName = $order->status ? $order->status->name : $rawStatus;

            return [
                'success'            => true,
                'found'              => true,
                'courier'            => 'Standard Delivery',
                'tracking_number'    => (string) $order->invoice_id,
                'status'             => $statusName,
                'last_update'        => $order->updated_at ? $order->updated_at->toDateTimeString() : null,
                'estimated_delivery' => $order->delivery_date ? $order->delivery_date->toDateString() : null,
            ];
        }

        // 5. If no courier or shipment tracking data exists, return unavailable
        return [
            'success'            => true,
            'found'              => true,
            'courier'            => 'unavailable',
            'tracking_number'    => null,
            'status'             => 'unavailable',
            'last_update'        => null,
            'estimated_delivery' => null,
            'message'            => 'No courier tracking data is currently available for this order.',
        ];
    }

    /**
     * Query existing project courier API integration if configured in database.
     */
    protected function queryCourierApi(Order $order): ?array
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('courierapis')) {
                return null;
            }

            $courierInfo = Courierapi::whereNotNull('api_key')->first();
            if (!$courierInfo || empty($courierInfo->url)) {
                return null;
            }

            $url  = rtrim($courierInfo->url, '/');
            $type = strtolower($courierInfo->type ?? 'courier');

            // Handle Steadfast courier API format
            if (str_contains($type, 'steadfast')) {
                $endpoint = "{$url}/status_by_cid/{$order->invoice_id}";
                $headers  = [
                    'Api-Key'    => $courierInfo->api_key,
                    'Secret-Key' => $courierInfo->secret_key,
                    'Accept'     => 'application/json',
                ];

                $response = Http::withHeaders($headers)->timeout(5)->get($endpoint);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data) && (isset($data['status']) || isset($data['delivery_status']))) {
                        return [
                            'courier'            => 'Steadfast Courier',
                            'tracking_number'    => (string) ($data['consignment_id'] ?? $data['tracking_code'] ?? $order->invoice_id),
                            'status'             => (string) ($data['delivery_status'] ?? $data['status'] ?? 'In Transit'),
                            'last_update'        => $data['updated_at'] ?? now()->toDateTimeString(),
                            'estimated_delivery' => $data['estimated_delivery'] ?? null,
                        ];
                    }
                }
            }

            // Generic courier HTTP check fallback
            $response = Http::withHeaders([
                'Api-Key' => $courierInfo->api_key,
                'Accept'  => 'application/json',
            ])->timeout(5)->get("{$url}/tracking/{$order->invoice_id}");

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data['status'])) {
                    return [
                        'courier'            => ucfirst($type) . ' Courier',
                        'tracking_number'    => (string) ($data['tracking_number'] ?? $order->invoice_id),
                        'status'             => (string) $data['status'],
                        'last_update'        => $data['updated_at'] ?? null,
                        'estimated_delivery' => $data['estimated_delivery'] ?? null,
                    ];
                }
            }
        } catch (Throwable $e) {
            Log::info("[ShippingStatusTool] Courier API check failed: " . $e->getMessage());
        }

        return null;
    }
}
