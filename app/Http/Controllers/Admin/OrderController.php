<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\District;
use App\Models\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Courierapi;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\PaymentHistory;

use Session;
use Cart;
use Toastr;
use Mail;

class OrderController extends Controller
{
    public function index($slug,Request $request){
        if($slug == 'all'){
            $order_status = (object) [
                'name' => 'All',
                'orders_count'=> Order::count(),
            ];
            $show_data = Order::latest()->with('shipping','status');
            if($request->keyword){
                $show_data = $show_data->where(function ($query) use ($request) {
                    $query->orWhere('invoice_id', 'LIKE', '%' . $request->keyword . '%')
                          ->orWhereHas('shipping', function ($subQuery) use ($request) {
                              $subQuery->where('phone', $request->keyword);
                          });
                });
            }
           $show_data = $show_data->paginate(10);
        }else{
            $order_status = OrderStatus::where('slug', $slug)->withCount('orders')->first();
            if (!$order_status) {
                $order_status = (object) [
                    'id' => 0,
                    'name' => ucfirst($slug),
                    'orders_count' => 0,
                ];
                $show_data = Order::where('id', 0)->paginate(10);
            } else {
                $show_data = Order::where(['order_status' => $order_status->id])->latest()->with('shipping', 'status')->paginate(10);
            }
        }
        $users = User::get();
        $steadfast = Courierapi::where(['status'=>1, 'type'=>'steadfast'])->first();
        $pathao_info = Courierapi::where(['status'=>1, 'type'=>'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        // pathao courier
        if($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/countries/1/city-list');
            $pathaocities = $response->json();
            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Content-Type' => 'application/json',
                ])->get($pathao_info->url . '/api/v1/stores');
            $pathaostore = $response2->json();
        } else {
            $pathaocities = [];
            $pathaostore = [];
        }
        return view('backEnd.order.index',compact('show_data','order_status','users', 'steadfast','pathaostore','pathaocities'));
    }

    public function pathaocity(Request $request)
    {
        $pathao_info = Courierapi::where(['status'=>1, 'type'=>'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/cities/'.$request->city_id.'/zone-list');
            $pathaozones = $response->json();
            return response()->json($pathaozones);
        } else {
            return response()->json([]);
        }
    }
    public function pathaozone(Request $request)
    {
        $pathao_info = Courierapi::where(['status'=>1, 'type'=>'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/zones/'.$request->zone_id.'/area-list');
            $pathaoareas = $response->json();
            return response()->json($pathaoareas);
        } else {
             return response()->json([]);
        }
    }

    public function order_pathao(Request $request)
    {
        $orders_id = $request->order_ids;

        foreach ($orders_id as $order_id) {
            $order = Order::with('shipping')->find($order_id);
            $order_count = OrderDetails::select('order_id')->where('order_id', $order->id)->count();
            // return $request->all();
            // pathao
            $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
            if ($pathao_info) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $pathao_info->token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($pathao_info->url . '/api/v1/orders', [
                    'store_id' => $request->pathaostore,
                    'merchant_order_id' => $order->invoice_id,
                    'sender_name' => 'Test',
                    'sender_phone' => $order->shipping ? $order->shipping->phone : '',
                    'recipient_name' => $order->shipping ? $order->shipping->name : '',
                    'recipient_phone' => $order->shipping ? $order->shipping->phone : '',
                    'recipient_address' => $order->shipping ? $order->shipping->address : '',
                    'recipient_city' => $request->pathaocity,
                    'recipient_zone' => $request->pathaozone,
                    'recipient_area' => $request->pathaoarea,
                    'delivery_type' => 48,
                    'item_type' => 2,
                    'special_instruction' => 'Special note- product must be check after delivery',
                    'item_quantity' => 1,
                    'item_weight' => 0.5,
                    'amount_to_collect' => round($order->amount),
                    'item_description' => 'Special note- product must be check after delivery',
                ]);
            }
            if ($response->status() == '200') {
                Toastr::success($response['data']['consignment_id'], 'Courier Tracking ID');
                return response()->json(['status' => 'success', 'message' => $response['data']['consignment_id'], 'Courier Tracking ID']);
            } else {
                Toastr::error($response['message'], 'Courier Order Faild');
                return response()->json(['status' => 'failed', 'message' => $response['message'], 'Courier Order Faild']);
            }
            return redirect()->back();


        }
    }

    public function invoice($invoice_id){
        $order = Order::where(['invoice_id'=>$invoice_id])
            ->with('orderdetails.product.category', 'payment', 'shipping', 'customer', 'paymentHistories')
            ->firstOrFail();

        $totalPaid = $order->paymentHistories->sum('amount');
        $dueAmount = max($order->amount - $totalPaid, 0);

        return view('backEnd.order.invoice', compact('order', 'totalPaid', 'dueAmount'));
    }

    public function paymentStore(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $order = Order::findOrFail($request->order_id);

        PaymentHistory::create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method ?? 'Cash',
            'trx_id' => $request->trx_id,
            'sender_number' => $request->sender_number,
            'note' => $request->note,
            'payment_date' => $request->payment_date,
            'received_by' => auth()->user()->name ?? 'Admin',
        ]);

        // Update the main payment record amount
        $totalPaid = PaymentHistory::where('order_id', $order->id)->sum('amount');
        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment) {
            $payment->amount = $totalPaid;
            $payment->payment_status = $totalPaid >= $order->amount ? 'paid' : 'partial';
            $payment->save();
        }

        Toastr::success('Payment recorded successfully');
        return redirect()->back();
    }

    public function paymentDelete(Request $request)
    {
        $payment = PaymentHistory::findOrFail($request->id);
        $orderId = $payment->order_id;
        $order = Order::find($orderId);
        $payment->delete();

        // Update main payment record
        $totalPaid = PaymentHistory::where('order_id', $orderId)->sum('amount');
        $paymentRec = Payment::where('order_id', $orderId)->first();
        if ($paymentRec) {
            $paymentRec->amount = $totalPaid;
            $paymentRec->payment_status = $totalPaid >= ($order ? $order->amount : 0) ? 'paid' : 'partial';
            $paymentRec->save();
        }

        Toastr::success('Payment record deleted');
        return redirect()->back();
    }

    public function process($invoice_id){
        $data = Order::where(['invoice_id'=>$invoice_id])
            ->select('id','invoice_id','order_status','amount','customer_id')
            ->with('orderdetails.product', 'paymentHistories')
            ->firstOrFail();
        $shippingcharge = ShippingCharge::where('status',1)->get();
        $orderstatus = \App\Models\OrderStatus::get();
        $totalPaid = $data->paymentHistories->sum('amount');
        $dueAmount = max($data->amount - $totalPaid, 0);
        return view('backEnd.order.process', compact('data', 'shippingcharge', 'orderstatus', 'totalPaid', 'dueAmount'));
    }

    public function order_process(Request $request)
    {

        $link = OrderStatus::find($request->status)->slug;
        $order = Order::find($request->id);
        $courier = $order->order_status;
        $new_status = $request->status;
        $order->order_status = $new_status;
        $order->admin_note = $request->admin_note;
        $order->save();

        $was_completed = self::isPaidOrCompletedStatus($courier);
        $is_completed = self::isPaidOrCompletedStatus($new_status);

        if ($is_completed && !$was_completed) {
            self::deductOrderStock($order->id);
        } elseif ($was_completed && !$is_completed) {
            self::restoreOrderStock($order->id);
        }

        $shipping_update = Shipping::where('order_id', $order->id)->first();
        $shippingfee = ShippingCharge::find($request->area);
        if ($shippingfee->name != $request->area) {
            if ($order->shipping_charge > $shippingfee->amount) {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            } else {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            }
        }

        $shipping_update->name = $request->name;
        $shipping_update->phone = $request->phone;
        $shipping_update->address = $request->address;
        $shipping_update->area = $shippingfee->name;
        $shipping_update->save();

        if ($request->status == 5 && $courier != 5) {
            $courier_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
            if ($courier_info) {
                $consignmentData = [
                    'invoice' => $order->invoice_id,
                    'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
                    'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
                    'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
                    'cod_amount' => $order->amount
                ];
                $client = new Client();
                $response = $client->post('$courier_info->url', [
                    'json' => $consignmentData,
                    'headers' => [
                        'Api-Key' => '$courier_info->api_key',
                        'Secret-Key' => '$courier_info->secret_key',
                        'Accept' => 'application/json',
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);
            } else {
                return "ok";
            }
            Toastr::success('Success', 'Order status change successfully');
            return redirect('admin/order/' . $link);
        }
		        Toastr::success('Success', 'Order status change successfully');
        return redirect('admin/order/' . $link);
    }

    public function destroy(Request $request){
        $order = Order::where('id',$request->id)->delete();
        $order_details = OrderDetails::where('order_id',$request->id)->delete();
        $shipping = Shipping::where('order_id',$request->id)->delete();
        $payment = Payment::where('order_id',$request->id)->delete();
        Toastr::success('Success','Order delete success successfully');
        return redirect()->back();
    }

    public function order_assign(Request $request){
        $products = Order::whereIn('id', $request->input('order_ids'))->update(['user_id' => $request->user_id]);
        return response()->json(['status'=>'success','message'=>'Order user id assign']);
    }

    public function order_status(Request $request){

        $sms_gateway = SmsGateway::where('status', 1)->first();
        $site_setting = GeneralSetting::where('status', 1)->first();
        // Update order statuses
        $new_status = $request->order_status;
        $orders = Order::whereIn('id', $request->input('order_ids'))->get();

        foreach ($orders as $order) {
            $prev_status = $order->order_status;
            $order->order_status = $new_status;
            $order->update();

            $was_completed = self::isPaidOrCompletedStatus($prev_status);
            $is_completed = self::isPaidOrCompletedStatus($new_status);

            if ($is_completed && !$was_completed) {
                self::deductOrderStock($order->id);
            } elseif ($was_completed && !$is_completed) {
                self::restoreOrderStock($order->id);
            }

            $orderStatus = OrderStatus::find($new_status);

            //Send SMS to the customer
            if ($sms_gateway) {
                $customer_info = Customer::find($order->customer_id); // Retrieve customer information
                if ($customer_info) {
                    $url = $sms_gateway->url;
                    $data = [
                        "api_key" => $sms_gateway->api_key,
                        "number" => $customer_info->phone,
                        "type" => 'text',
                        "senderid" => $sms_gateway->serderid,
                        "message" => "Dear {$customer_info->name},\r\n"
                                  . "Your order (Order ID: {$order->invoice_id}) status has been updated to: "
                                  . "{$orderStatus->name}.\r\n"
                                  . "Thank you for using {$site_setting->name}!",
                    ];

                    // cURL request to send SMS
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
            }
        }

        return response()->json(['status'=>'success','message'=>'Order status change successfully']);
    }

    public function bulk_destroy(Request $request){
        $orders_id = $request->order_ids;
        foreach($orders_id as $order_id){
            $order = Order::where('id',$order_id)->delete();
            $order_details = OrderDetails::where('order_id',$order_id)->delete();
            $shipping = Shipping::where('order_id',$order_id)->delete();
            $payment = Payment::where('order_id',$order_id)->delete();
        }
        return response()->json(['status'=>'success','message'=>'Order delete successfully']);
    }
    public function order_print(Request $request){
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('orderdetails','payment','shipping','customer')->get();
        $view = view('backEnd.order.print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }
    // public function bulk_courier($slug, Request $request)
    // {
    //     $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();

    //     if ($courier_info) {
    //         $orders_ids = $request->order_ids;

    //         foreach ($orders_ids as $order_id) {
    //             $order = Order::find($order_id);

    //             $courier = $order->order_status;
    //             if ($request->status == 5 && $courier != 5) {
    //                 $consignmentData = [
    //                     'invoice' => $order->invoice_id,
    //                     'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
    //                     'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
    //                     'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
    //                     'cod_amount' => $order->amount
    //                 ];
    //                 $client = new Client();
    //                 $response = $client->post('$courier_info->url', [
    //                     'json' => $consignmentData,
    //                     'headers' => [
    //                         'Api-Key' => '$courier_info->api_key',
    //                         'Secret-Key' => '$courier_info->secret_key',
    //                         'Accept' => 'application/json',
    //                     ],
    //                 ]);

    //                 $responseData = json_decode($response->getBody(), true);
    //                 if ($responseData['status'] == 200) {
    //                     $message = 'Your order place to courier successfully';
    //                     $status = 'success';
    //                     $order->order_status = 4;
    //                     $order->save();
    //                 } else {
    //                     $message = 'Your order place to courier failed';
    //                     $status = 'failed';
    //                 }
    //                 return response()->json(['status' => $status, 'message' => $message]);
    //             }

    //         }
    //     } else {
    //         return "stop";
    //     }
    // }
    public function bulk_courier($slug, Request $request)
    {
        $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();

        if ($courier_info) {
            $orders_ids = $request->order_ids;
            $successOrders = [];
            $failedOrders = [];

            foreach ($orders_ids as $order_id) {
                $order = Order::find($order_id);

                if ($order && $request->status == 5 && $order->order_status != 5) {
                    $consignmentData = [
                        'invoice' => $order->invoice_id,
                        'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
                        'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
                        'recipient_address' => $order->shipping ? $order->shipping->address : 'Address not provided',
                        'cod_amount' => $order->amount
                    ];

                    $client = new Client();
                    try {
                        $response = $client->post($courier_info->url, [
                            'json' => $consignmentData,
                            'headers' => [
                                'Api-Key' => $courier_info->api_key,
                                'Secret-Key' => $courier_info->secret_key,
                                'Accept' => 'application/json',
                            ],
                        ]);

                        $responseData = json_decode($response->getBody(), true);

                        if ($responseData['status'] == 200) {
                            $order->order_status = 5; // Update order status to successful placement
                            $order->save();
                            $successOrders[] = [
                                'order_id' => $order_id,
                                'message' => $responseData['message'] ?? 'Order placed successfully'
                            ];
                        } else {
                            $failedOrders[] = [
                                'order_id' => $order_id,
                                'message' => $responseData['message'] ?? 'Failed to place order'
                            ];
                        }
                    } catch (\Exception $e) {
                        // Add to failed orders if there's an exception
                        $failedOrders[] = [
                            'order_id' => $order_id,
                            'message' => $e->getMessage()
                        ];
                    }
                }
            }

            // Return summary of success and failure
            return response()->json([
                'status' => 'success',
                'message' => 'Your order place to courier successfully',
                'success' => json_encode($successOrders),
                'failed' => json_encode($failedOrders)
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Courier information not found.'
            ]);
        }
    }
    public function stock_report(Request $request){
        $query = Product::with('category', 'colors', 'procolors', 'image')
            ->where('status', 1)
            ->select('id', 'name', 'product_code', 'new_price', 'purchase_price', 'stock', 'category_id');

        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('product_code', 'LIKE', "%{$keyword}%");
            });
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name', 'ASC')->paginate(50);

        // Pre-compute total sales qty and net sales revenue per product from completed/paid orders
        $productIds = $products->pluck('id');
        $completedStatusIds = \App\Models\OrderStatus::all()->filter(function($s) {
            $name = strtolower(trim($s->name));
            $slug = strtolower(trim($s->slug));
            return in_array($name, ['completed', 'paid', 'paid/completed', 'paid / completed']) ||
                   in_array($slug, ['completed', 'paid', 'paid-completed']);
        })->pluck('id')->toArray();

        $completedOrders = \App\Models\Order::whereIn('order_status', $completedStatusIds)
            ->with(['orderdetails'])
            ->get();

        $salesData = [];
        $revenueData = [];

        foreach ($completedOrders as $ord) {
            // Calculate item subtotal after item-level product discounts
            $itemSubtotal = 0;
            foreach ($ord->orderdetails as $d) {
                $unitPrice = (float)($d->sale_price ?? 0);
                $unitDiscount = (float)($d->product_discount ?? 0);
                $qty = (int)($d->qty ?? 0);
                $itemSubtotal += max(0, ($unitPrice - $unitDiscount) * $qty);
            }

            // Total amount for this order (includes delivery charge and accounts for discounts)
            $orderTotalAmount = (float)($ord->amount ?? 0);

            foreach ($ord->orderdetails as $d) {
                $pid = $d->product_id;
                if (!isset($salesData[$pid])) {
                    $salesData[$pid] = 0;
                    $revenueData[$pid] = 0;
                }
                $qty = (int)($d->qty ?? 0);
                $salesData[$pid] += $qty;

                $unitPrice = (float)($d->sale_price ?? 0);
                $unitDiscount = (float)($d->product_discount ?? 0);
                $lineNet = max(0, ($unitPrice - $unitDiscount) * $qty);

                if ($itemSubtotal > 0) {
                    $lineFinal = ($lineNet / $itemSubtotal) * $orderTotalAmount;
                } else {
                    $lineFinal = 0;
                }
                $revenueData[$pid] += $lineFinal;
            }
        }

        $categories = \App\Models\Category::where('status', 1)->get();

        // Compute summary totals
        $totalStockQty = 0;
        $totalSoldQty = 0;
        $totalBuyingCost = 0;
        $totalRemainingPrice = 0;

        foreach ($products as $p) {
            $sold = $salesData[$p->id] ?? 0;
            $remaining = max($p->stock - $sold, 0);
            $totalStockQty += $p->stock;
            $totalSoldQty += $sold;
            $totalBuyingCost += $p->purchase_price * $p->stock;
            $totalRemainingPrice += $p->new_price * $remaining;
        }

        return view('backEnd.reports.stock', compact(
            'products', 'categories', 'salesData', 'revenueData',
            'totalStockQty', 'totalSoldQty', 'totalBuyingCost', 'totalRemainingPrice'
        ));
    }

    public function stockProductVariants(Request $request)
    {
        $product = Product::with([
            'procolors.color',
            'prosizes.size',
            'image',
            'images.color',
        ])->findOrFail($request->id);

        // Build a map of color_id => image url
        $colorImageMap = $product->images->mapWithKeys(function ($img) {
            return $img->color_id ? [$img->color_id => asset($img->image)] : [];
        });

        $colors = $product->procolors->map(function ($pc) use ($colorImageMap) {
            return [
                'name' => $pc->color->colorName ?? 'N/A',
                'price' => $pc->price,
                'stock' => $pc->stock,
                'image' => $colorImageMap->get($pc->color_id) ?: ($product->image ? asset($product->image->image) : ''),
            ];
        });

        $sizes = $product->prosizes->map(function ($ps) {
            return [
                'name' => $ps->size->sizeName ?? 'N/A',
                'price' => $ps->price,
                'stock' => $ps->stock,
            ];
        });

        return response()->json([
            'name' => $product->name,
            'image' => $product->image ? asset($product->image->image) : '',
            'colors' => $colors,
            'sizes' => $sizes,
        ]);
    }

    public function order_report(Request $request){
        $users = User::where('status',1)->get();
        $order_statuses = \App\Models\OrderStatus::where('status',1)->get();

        $completedStatusIds = \App\Models\OrderStatus::all()->filter(function($s) {
            $name = strtolower(trim($s->name));
            $slug = strtolower(trim($s->slug));
            return in_array($name, ['completed', 'paid', 'paid/completed', 'paid / completed']) ||
                   in_array($slug, ['completed', 'paid', 'paid-completed']);
        })->pluck('id')->toArray();

        $orders = OrderDetails::with(['shipping', 'order.status']);

        // Filter by specific order status if selected by user
        if ($request->order_status) {
            $orders = $orders->whereHas('order', function ($q) use ($request) {
                $q->where('order_status', $request->order_status);
            });
        } elseif (!$request->keyword && !$request->user_id && !$request->start_date && !$request->end_date) {
            // Default filter to Paid/Completed orders if no filters set, fallback to all if none exist
            $hasCompleted = OrderDetails::whereHas('order', function($q) use ($completedStatusIds) {
                $q->whereIn('order_status', $completedStatusIds);
            })->exists();

            if ($hasCompleted) {
                $orders = $orders->whereHas('order', function ($q) use ($completedStatusIds) {
                    $q->whereIn('order_status', $completedStatusIds);
                });
            }
        }

        // Keyword search - search product name, invoice ID, customer name, customer phone
        if ($request->keyword) {
            $keyword = $request->keyword;
            $orders = $orders->where(function ($q) use ($keyword) {
                $q->where('product_name', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('order', function ($oq) use ($keyword) {
                      $oq->where('invoice_id', 'LIKE', "%{$keyword}%");
                  })
                  ->orWhereHas('shipping', function ($sq) use ($keyword) {
                      $sq->where('name', 'LIKE', "%{$keyword}%")
                         ->orWhere('phone', 'LIKE', "%{$keyword}%");
                  });
            });
        }

        // Filter by assigned user
        if ($request->user_id) {
            $orders = $orders->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        // Date range filter (independent start/end)
        if ($request->start_date) {
            $orders = $orders->whereHas('order', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            });
        }
        if ($request->end_date) {
            $orders = $orders->whereHas('order', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            });
        }

        $total_purchase = $orders->sum(\DB::raw('purchase_price * qty'));
        $total_item = $orders->sum('qty');
        $total_sales = $orders->sum(\DB::raw('(sale_price - product_discount) * qty'));
        $orders = $orders->orderBy('id', 'DESC')->paginate(20);

        return view('backEnd.reports.order', compact('orders', 'users', 'order_statuses', 'total_purchase', 'total_item', 'total_sales'));
    }

    public function order_create(){
        $cartinfo  = Cart::instance('pos_shopping')->destroy();
        $products = Product::select('id','name','new_price','product_code')->where(['status'=>1])->get();
        $cartinfo  = Cart::instance('pos_shopping')->content();
        $shippingcharge = ShippingCharge::where('status',1)->get();
        $customers = Customer::select('id', 'name', 'phone', 'address', 'area')
            ->where('status', '!=', 'Inactive')
            ->orderBy('name', 'ASC')
            ->get();
        return view('backEnd.order.create',compact('products','cartinfo','shippingcharge','customers'));
    }

    public function order_store(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'area'=>'required',
        ]);

        if(Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',','',$subtotal);
        $subtotal = str_replace('.00', '',$subtotal);
        $discount = Session::get('pos_discount')+Session::get('product_discount');
        $shippingfee  = ShippingCharge::find($request->area);

        $exits_customer = Customer::where('phone',$request->phone)->select('phone','id')->first();
        if($exits_customer){
            $customer_id = $exits_customer->id;
        }else{
            $password = rand(111111,999999);
            $store              = new Customer();
            $store->name        = $request->name;
            $store->slug        = $request->name;
            $store->phone       = $request->phone;
            $store->password    = bcrypt($password);
            $store->verify      = 1;
            $store->status      = 'active';
            $store->save();
            $customer_id = $store->id;
        }

         // order data save
        $order                   = new Order();
        $order->invoice_id       = rand(11111,99999);
        $order->amount           = ($subtotal + $shippingfee->amount) - $discount;
        $order->discount         = $discount ? $discount : 0;
        $order->shipping_charge  = $shippingfee->amount;
        $order->customer_id      =  $customer_id;
        $order->order_status     = 1;
        $order->note             = $request->note;
        $order->order_date       = $request->order_date ?? now();
        $order->delivery_date    = $request->delivery_date;
        $order->save();

        // shipping data save
        $shipping              =   new Shipping();
        $shipping->order_id    =   $order->id;
        $shipping->customer_id =   $customer_id;
        $shipping->name        =   $request->name;
        $shipping->phone       =   $request->phone;
        $shipping->address     =   $request->address;
        $shipping->area        =   $shippingfee->name;
        $shipping->save();

        // payment data save
        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = 'Cash On Delivery';
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

       // order details data save
        foreach(Cart::instance('pos_shopping')->content() as $cart){
            $order_details                   =   new OrderDetails();
            $order_details->order_id         =   $order->id;
            $order_details->product_id       =   $cart->id;
            $order_details->product_name     =   $cart->name;
            $order_details->product_color    =   $cart->options->product_color ?? null;
            $order_details->product_size     =   $cart->options->product_size ?? null;
            $order_details->purchase_price   =   $cart->options->purchase_price;
            $order_details->product_discount =   $cart->options->product_discount;
            $order_details->sale_price       =   $cart->price;
            $order_details->qty              =   $cart->qty;
            $order_details->save();
        }

        if (self::isPaidOrCompletedStatus($order->order_status)) {
            self::deductOrderStock($order->id);
        }

        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Toastr::success('Thanks, Your order placed successfully', 'Success!');
        return redirect('admin/order/all');
    }
    public function cart_add(Request $request){
        $product = Product::select('id','name','stock','new_price','old_price','purchase_price','slug')->where(['id' => $request->id])->first();
        $qty = (int) ($request->qty ?? $request->quantity ?? 1);
        if ($qty < 1) $qty = 1;

        $price = $product->new_price;
        if ($request->product_size) {
            $size = \App\Models\Size::where('sizeName', $request->product_size)->first();
            if ($size) {
                $proSize = \App\Models\Productsize::where('product_id', $product->id)->where('size_id', $size->id)->first();
                if ($proSize && $proSize->price > 0) {
                    $price = $proSize->price;
                }
            }
        } elseif ($request->product_color) {
            $color = \App\Models\Color::where('colorName', $request->product_color)->first();
            if ($color) {
                $proColor = \App\Models\Productcolor::where('product_id', $product->id)->where('color_id', $color->id)->first();
                if ($proColor && $proColor->price > 0) {
                    $price = $proColor->price;
                }
            }
        }

        $cartinfo = Cart::instance('pos_shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $qty,
            'price' => $price,
            'options' => [
                'slug' => $product->slug,
                'image' => $product->image ? $product->image->image : '',
                'old_price' => $product->old_price,
                'purchase_price' => $product->purchase_price,
                'product_discount' => 0,
                'product_size' => $request->product_size ?? '',
                'product_color' => $request->product_color ?? '',
            ],
        ]);
        return response()->json(compact('cartinfo'));
    }

    public function getProductVariants(Request $request)
    {
        $product = Product::with('colors', 'sizes', 'procolors', 'prosizes')->find($request->id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $variantPrice = $product->new_price;

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->new_price,
            'purchase_price' => $product->purchase_price ?? 0,
            'stock' => $product->stock,
            'image' => $product->image ? asset($product->image->image) : '',
            'colors' => $product->colors->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->colorName ?? $c->name ?? '',
                    'code' => $c->color ?? '',
                    'price' => $c->pivot->price ?? null,
                    'stock' => $c->pivot->stock ?? null,
                ];
            }),
            'sizes' => $product->sizes->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->sizeName ?? $s->name ?? '',
                    'price' => $s->pivot->price ?? null,
                    'stock' => $s->pivot->stock ?? null,
                ];
            }),
        ]);
    }

    public function cart_content(){
        $cartinfo = Cart::instance('pos_shopping')->content();
        return view('backEnd.order.cart_content',compact('cartinfo'));
    }
    public function cart_details(){
        $cartinfo = Cart::instance('pos_shopping')->content();
        $discount = 0;
        foreach($cartinfo as $cart){
            $discount += $cart->options->product_discount*$cart->qty;
        }
        Session::put('product_discount',$discount);
        return view('backEnd.order.cart_details',compact('cartinfo'));
    }
    public function cart_increment(Request $request){
        $qty = $request->qty + 1;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'qty' => $qty,
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $cart->options->product_discount,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function cart_decrement(Request $request){
        $qty = $request->qty - 1;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'qty' => $qty,
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $cart->options->product_discount,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);

        return response()->json($cartinfo);
    }
    public function cart_remove(Request $request){
        $remove = Cart::instance('pos_shopping')->remove($request->id);
        $cartinfo = Cart::instance('pos_shopping')->content();
        return response()->json($cartinfo);
    }
    public function product_discount(Request $request){
        $discount = $request->discount;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $request->discount,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function cart_update(Request $request)
    {
        // Get the row ID of the cart item
        $rowId = $request->id;
        // Fetch the current cart item using the row ID
        $cartItem = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        if ($cartItem) {
            // Update the options for the cart item
            Cart::instance('pos_shopping')->update($rowId, [
                'options' => [
                    'product_size' => $request->product_size ?: $cartItem->options->product_size, // Use new size or keep existing
                    'product_color' => $request->product_color ?: $cartItem->options->product_color, // Use new color or keep existing
                    'slug' => $cartItem->options->slug,
                    'image' => $cartItem->options->image,
                    'old_price' => $cartItem->options->old_price,
                    'purchase_price' => $cartItem->options->purchase_price,
                    'product_discount' => $cartItem->options->product_discount,
                ],
            ]);
        }

        return response()->json($cartItem);
    }
    public function cart_shipping(Request $request){
         $shipping = ShippingCharge::where(['status'=>1,'id'=>$request->id])->first()->amount;
        Session::put('pos_shipping', $shipping);
        return response()->json($shipping);
    }

    public function cart_clear(Request $request){
        $cartinfo = Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        return redirect()->back();
    }
    public function order_edit($invoice_id){
        $products = Product::select('id','name','new_price','product_code')->where(['status'=>1])->get();
        $shippingcharge = ShippingCharge::where('status',1)->get();
        $order = Order::where('invoice_id', $invoice_id)->first() ?? Order::find($invoice_id);
        if (!$order) {
            Toastr::error('Order not found', 'Failed!');
            return redirect()->route('admin.orders', ['slug' => 'all']);
        }
        $cartinfo  = Cart::instance('pos_shopping')->destroy();
        $shippinginfo  = Shipping::where('order_id', $order->id)->first() ?? new Shipping();
        Session::put('product_discount', $order->discount);
        Session::put('pos_shipping', $order->shipping_charge);
        $orderdetails = OrderDetails::where('order_id', $order->id)->get();
        foreach($orderdetails as $ordetails){
            $cartinfo = Cart::instance('pos_shopping')->add([
                'id' => $ordetails->product_id,
                'name' => $ordetails->product_name,
                'qty' => $ordetails->qty,
                'price' => $ordetails->sale_price,
                'options' => [
                    'image' => $ordetails->image ? $ordetails->image->image : '',
                    'purchase_price' => $ordetails->purchase_price,
                    'product_discount' => $ordetails->product_discount,
                    'details_id' => $ordetails->id,
                    'product_size' => $ordetails->product_size,
                    'product_color' => $ordetails->product_color,
                ],
            ]);
        }
        $cartinfo  = Cart::instance('pos_shopping')->content();
        return view('backEnd.order.edit',compact('products','cartinfo','shippingcharge','shippinginfo','order'));
    }

    public function order_update(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'area'=>'required',
        ]);

        if(Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',','',$subtotal);
        $subtotal = str_replace('.00', '',$subtotal);
        $discount = Session::get('pos_discount')+Session::get('product_discount');
        $shippingfee  = ShippingCharge::find($request->area);

        $exits_customer = Customer::where('phone',$request->phone)->select('phone','id')->first();
        if($exits_customer){
            $customer_id = $exits_customer->id;
        }else{
            $password = rand(111111,999999);
            $store              = new Customer();
            $store->name        = $request->name;
            $store->slug        = $request->name;
            $store->phone       = $request->phone;
            $store->password    = bcrypt($password);
            $store->verify      = 1;
            $store->status      = 'active';
            $store->save();
            $customer_id = $store->id;
        }

        // order data save
        $orderId = $request->order_id ?? $request->id ?? $request->hidden_id;
        $order = Order::find($orderId);
        if (!$order) {
            Toastr::error('Order not found', 'Failed!');
            return redirect()->back();
        }

        $prev_status = $order->order_status;
        $new_status  = $request->order_status ?? $request->status ?? $prev_status;

        // If order was in Paid/Completed status, temporarily restore variant stock before applying item updates
        if (self::isPaidOrCompletedStatus($prev_status)) {
            self::restoreOrderStock($order->id);
        }

        if (!$order->invoice_id) {
            $order->invoice_id = rand(11111, 99999);
        }
        $order->amount           = ($subtotal + ($shippingfee ? $shippingfee->amount : 0)) - $discount;
        $order->discount         = $discount ? $discount : 0;
        $order->shipping_charge  = $shippingfee ? $shippingfee->amount : 0;
        $order->customer_id      = $customer_id;
        $order->order_status     = $new_status;
        $order->note             = $request->note;
        $order->order_date       = $request->order_date;
        $order->delivery_date    = $request->delivery_date;
        $order->save();

        // shipping data save
        $shipping = Shipping::where('order_id', $order->id)->first();
        if (!$shipping) {
            $shipping = new Shipping();
        }
        $shipping->order_id    = $order->id;
        $shipping->customer_id = $customer_id;
        $shipping->name        = $request->name;
        $shipping->phone       = $request->phone;
        $shipping->address     = $request->address;
        $shipping->area        = $shippingfee ? $shippingfee->name : 'N/A';
        $shipping->save();

        // payment data save
        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) {
            $payment = new Payment();
        }
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = $request->payment_method ?? 'Cash On Delivery';
        $payment->amount         = $order->amount;
        $payment->payment_status = $payment->payment_status ?? 'pending';
        $payment->save();

       // order details data save
        $cartDetailsIds = collect();
        foreach(Cart::instance('pos_shopping')->content() as $cart){
            $cartDetailsIds->push($cart->options->details_id);
        }
        // Delete order details that were removed from the cart
        OrderDetails::where('order_id', $order->id)
            ->whereNotIn('id', $cartDetailsIds->filter())
            ->delete();

        foreach(Cart::instance('pos_shopping')->content() as $cart){
            $exits = OrderDetails::where('id',$cart->options->details_id)->first();
            if($exits){
                $order_details                   =   OrderDetails::find($exits->id);
                $order_details->product_discount =   $cart->options->product_discount;
                $order_details->product_color =   $cart->options->product_color;
                $order_details->product_size =   $cart->options->product_size;
                $order_details->sale_price       =   $cart->price;
                $order_details->qty              =   $cart->qty;
                $order_details->save();
            }else{
                $order_details                   =   new OrderDetails();
                $order_details->order_id         =   $order->id;
                $order_details->product_id       =   $cart->id;
                $order_details->product_name     =   $cart->name;
                $order_details->purchase_price   =   $cart->options->purchase_price;
                $order_details->product_discount =   $cart->options->product_discount;
                $order_details->product_color    =   $cart->options->product_color;
                $order_details->product_size     =   $cart->options->product_size;
                $order_details->sale_price       =   $cart->price;
                $order_details->qty              =   $cart->qty;
                $order_details->save();
            }
        }

        // If order status is Paid/Completed after update, deduct variant stock for current items
        if (self::isPaidOrCompletedStatus($new_status)) {
            self::deductOrderStock($order->id);
        }

        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Toastr::success('Thanks, Your order updated successfully', 'Success!');
        return redirect('admin/order/all');
    }

    public static function deductOrderStock($order_id) {
        $orderDetails = OrderDetails::where('order_id', $order_id)->get();
        foreach ($orderDetails as $detail) {
            $qty = (int)$detail->qty;
            if ($qty <= 0) continue;

            $product = Product::find($detail->product_id);
            if ($product) {
                // Keep main product page stock untouched ($product->stock is NOT modified)

                // Deduct color variant stock if present
                if ($detail->product_color) {
                    $color = \App\Models\Color::where('colorName', $detail->product_color)
                        ->orWhere('id', $detail->product_color)
                        ->first();
                    if ($color) {
                        $proColor = \App\Models\Productcolor::where('product_id', $product->id)
                            ->where('color_id', $color->id)
                            ->first();
                        if ($proColor && $proColor->stock !== null) {
                            $proColor->stock = max(0, (int)$proColor->stock - $qty);
                            $proColor->save();
                        }
                    }
                }

                // Deduct size variant stock if present
                if ($detail->product_size) {
                    $size = \App\Models\Size::where('sizeName', trim($detail->product_size))
                        ->orWhere('sizeName', 'LIKE', '%' . trim($detail->product_size) . '%')
                        ->orWhere('id', $detail->product_size)
                        ->first();
                    if (!$size) {
                        $productSize = \App\Models\Productsize::where('product_id', $product->id)->first();
                        if ($productSize) {
                            $size = \App\Models\Size::find($productSize->size_id);
                        }
                    }
                    if ($size) {
                        $proSize = \App\Models\Productsize::where('product_id', $product->id)
                            ->where('size_id', $size->id)
                            ->first();
                        if ($proSize && $proSize->stock !== null) {
                            $proSize->stock = max(0, (int)$proSize->stock - $qty);
                            $proSize->save();
                        }
                    }
                }
            }
        }
    }

    public static function restoreOrderStock($order_id) {
        $orderDetails = OrderDetails::where('order_id', $order_id)->get();
        foreach ($orderDetails as $detail) {
            $qty = (int)$detail->qty;
            if ($qty <= 0) continue;

            $product = Product::find($detail->product_id);
            if ($product) {
                // Keep main product page stock untouched ($product->stock is NOT modified)

                // Restore color variant stock if present
                if ($detail->product_color) {
                    $color = \App\Models\Color::where('colorName', $detail->product_color)
                        ->orWhere('id', $detail->product_color)
                        ->first();
                    if ($color) {
                        $proColor = \App\Models\Productcolor::where('product_id', $product->id)
                            ->where('color_id', $color->id)
                            ->first();
                        if ($proColor && $proColor->stock !== null) {
                            $proColor->stock = (int)$proColor->stock + $qty;
                            $proColor->save();
                        }
                    }
                }

                // Restore size variant stock if present
                if ($detail->product_size) {
                    $size = \App\Models\Size::where('sizeName', trim($detail->product_size))
                        ->orWhere('sizeName', 'LIKE', '%' . trim($detail->product_size) . '%')
                        ->orWhere('id', $detail->product_size)
                        ->first();
                    if (!$size) {
                        $productSize = \App\Models\Productsize::where('product_id', $product->id)->first();
                        if ($productSize) {
                            $size = \App\Models\Size::find($productSize->size_id);
                        }
                    }
                    if ($size) {
                        $proSize = \App\Models\Productsize::where('product_id', $product->id)
                            ->where('size_id', $size->id)
                            ->first();
                        if ($proSize && $proSize->stock !== null) {
                            $proSize->stock = (int)$proSize->stock + $qty;
                            $proSize->save();
                        }
                    }
                }
            }
        }
    }

    public static function isPaidOrCompletedStatus($status_id) {
        if (!$status_id) return false;
        $status = \App\Models\OrderStatus::find($status_id);
        if (!$status) return false;
        $name = strtolower(trim($status->name));
        $slug = strtolower(trim($status->slug));
        
        return in_array($name, ['completed', 'paid', 'paid/completed', 'paid / completed']) ||
               in_array($slug, ['completed', 'paid', 'paid-completed']);
    }

}
