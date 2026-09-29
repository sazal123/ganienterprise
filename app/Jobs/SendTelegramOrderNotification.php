<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramOrderNotification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    /**
     * Create a new job instance.
     *
     * @param int $orderId
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $order = Order::with(['orderdetails.product', 'shipping', 'customer', 'payment', 'status'])->find($this->orderId);

            if (!$order) {
                Log::warning("SendTelegramOrderNotification: Order #{$this->orderId} not found.");
                return;
            }

            $rawToken = trim(config('services.telegram.bot_token') ?: (env('TELEGRAM_BOT_TOKEN') ?: '8758560868:AAHSkhWa4l4bW9qJJxz1tI8CRubE94X3swE'));
            $chatId   = trim(config('services.telegram.chat_id') ?: (env('TELEGRAM_CHAT_ID') ?: '-5360314363'));

            if (!$rawToken || !$chatId) {
                Log::warning("SendTelegramOrderNotification: Telegram bot token or chat ID missing.");
                return;
            }

            // Clean token formatting
            $botToken = str_starts_with($rawToken, 'bot') ? $rawToken : 'bot' . $rawToken;

            $shipping = $order->shipping;
            $customerName = $shipping->name ?? ($order->customer->name ?? 'N/A');
            $customerPhone = $shipping->phone ?? ($order->customer->phone ?? 'N/A');
            $customerAddress = $shipping->address ?? 'N/A';
            $area = $shipping->area ?? '';
            $paymentMethod = $order->payment->payment_method ?? 'Cash On Delivery';
            $orderStatus = optional($order->status)->name ?? (is_string($order->order_status) ? $order->order_status : 'Ordered');

            // Build HTML Message for Telegram
            $msg  = "<b>🛍️ NEW ORDER RECEIVED!</b>\n";
            $msg .= "--------------------------------------\n";
            $msg .= "<b>Invoice ID:</b> #{$order->invoice_id}\n";
            $msg .= "<b>Date:</b> " . ($order->created_at ? $order->created_at->format('d M Y, h:i A') : date('d M Y, h:i A')) . "\n\n";

            $msg .= "<b>👤 Customer Details:</b>\n";
            $msg .= "• <b>Name:</b> " . htmlspecialchars($customerName) . "\n";
            $msg .= "• <b>Phone:</b> " . htmlspecialchars($customerPhone) . "\n";
            $msg .= "• <b>Address:</b> " . htmlspecialchars($customerAddress) . ($area ? " ({$area})" : "") . "\n\n";

            $msg .= "<b>🛒 Ordered Items:</b>\n";
            if ($order->orderdetails && count($order->orderdetails) > 0) {
                foreach ($order->orderdetails as $index => $item) {
                    $pName = htmlspecialchars($item->product_name ?? ($item->product->name ?? 'Product'));
                    $qty   = $item->qty;
                    $price = number_format($item->sale_price, 0);
                    $total = number_format($item->sale_price * $item->qty, 0);
                    $meta  = [];
                    if ($item->product_color) { $meta[] = "Color: " . htmlspecialchars($item->product_color); }
                    if ($item->product_size) { $meta[] = "Size: " . htmlspecialchars($item->product_size); }
                    $metaStr = !empty($meta) ? " (" . implode(', ', $meta) . ")" : "";

                    $msg .= ($index + 1) . ". {$pName}{$metaStr}\n";
                    $msg .= "   {$qty} x ৳{$price} = <b>৳{$total}</b>\n";
                }
            } else {
                $msg .= "• No item details recorded.\n";
            }

            $msg .= "\n<b>💵 Financial Summary:</b>\n";
            if ($order->shipping_charge > 0) {
                $msg .= "• <b>Shipping Charge:</b> ৳" . number_format($order->shipping_charge, 0) . "\n";
            }
            if ($order->discount > 0) {
                $msg .= "• <b>Discount:</b> ৳" . number_format($order->discount, 0) . "\n";
            }
            $msg .= "• <b>Total Bill:</b> <b>৳" . number_format($order->amount, 0) . " BDT</b>\n\n";

            $msg .= "💳 <b>Payment:</b> " . htmlspecialchars($paymentMethod) . "\n";
            $msg .= "📌 <b>Status:</b> " . htmlspecialchars($orderStatus) . "\n";

            if ($order->note) {
                $msg .= "📝 <b>Note:</b> " . htmlspecialchars($order->note) . "\n";
            }

            $url = "https://api.telegram.org/{$botToken}/sendMessage";

            $response = Http::timeout(4)->connectTimeout(2)->post($url, [
                'chat_id'    => $chatId,
                'text'       => $msg,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if (!$response->successful()) {
                Log::error("SendTelegramOrderNotification Error: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error("SendTelegramOrderNotification Exception: " . $e->getMessage());
        }
    }
}

