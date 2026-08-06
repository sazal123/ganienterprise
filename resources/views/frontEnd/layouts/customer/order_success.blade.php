@extends('frontEnd.layouts.master')
@section('title','Order Success')
@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                <div class="success-img">
                    <img src="{{asset('frontEnd/images/order-success.png')}}" alt="">
                </div>
                <div class="success-title">
                    <h2>We have received you order successfully, within short time we will call you </h2>
                </div>

                <h5 class="my-3">Your Order Details</h5>
                <div class="success-table">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <p>Invoice ID</p>
                                    <p><strong>{{$order->invoice_id}}</strong></p>
                                </td>
                                <td>
                                    <p>Date</p>
                                    <p><strong>{{$order->created_at->format('d-m-y')}}</strong></p>
                                </td>
                                <td>
                                    <p>Phone</p>
                                    <p><strong>{{$order->shipping?$order->shipping->phone:''}}</strong></p>
                                </td>
                                <td>
                                    <p>Total</p>
                                    <p><strong>৳ {{$order->amount}}</strong></p>
                                </td>
                            </tr>
                            <tr>
                                @php 
                                    $payments = App\Models\Payment::where('order_id',$order->id)->first();
                                @endphp
                                <td colspan="4">
                                    <p>Payment Method</p>
                                    <p><strong>{{$payments->payment_method}}</strong></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- success table -->
                <h5 class="my-4">Pay with cash upon delivery</h5>
                <div class="success-table">
                    <h6 class="mb-3">Order Delivery</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderdetails as $key=>$value)
                            <tr>
                                <td>
                                    <p>{{$value->product_name}} x {{$value->qty}} <br> @if($value->product_size) <small>Size: {{$value->product_size}}</small> @endif   @if($value->product_color) <small>Color: {{$value->product_color}}</small> @endif</p>
                                    
                                </td>
                                <td><p><strong>৳ {{$value->sale_price}}</strong></p></td>
                            </tr>
                            @endforeach
                            <tr>
                                <th  class="text-end px-4">Net Total</th>
                                <td><strong id="net_total">৳{{$order->amount-$order->shipping_charge}}</strong></td>
                            </tr>
                            <tr>
                                <th  class="text-end px-4">Shipping Cost</th>
                                <td>
                                    <strong id="cart_shipping_cost">৳{{$order->shipping_charge}}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th  class="text-end px-4">Grand Total</th>
                                <td>
                                    <strong id="grand_total">৳{{$order->amount}}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <h5 class="my-4">Billing Address</h5>
                                    <p>{{$order->shipping?$order->shipping->name:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->phone:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->address:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->area:''}}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- success table -->
                <div class="d-flex align-items-center justify-content-center gap-3 my-4 flex-wrap">
                    <button type="button" id="btn-download-invoice" class="btn btn-success fw-bold px-4 py-2" style="background: #000 !important;">
                        <i class="fa fa-download me-1"></i> Download Invoice
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-primary fw-bold px-4 py-2" style="background: #000 !important;">
                        <i class="fa fa-home me-1"></i> Go To Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Hidden Invoice HTML Container for Direct PDF Download --}}
@php
    $subTotal = $order->orderdetails->sum(fn($d) => $d->sale_price * $d->qty);
    $categoryNames = $order->orderdetails->map(fn($d) => $d->product->category->name ?? null)->filter()->unique()->implode(', ');
    $paymentHistories = $order->paymentHistories ?? [];
    $totalPaid = $paymentHistories ? $paymentHistories->sum('amount') : 0;
    $dueAmount = max($order->amount - $totalPaid, 0);
@endphp

<div style="position: absolute; left: -9999px; top: 0; width: 700px; opacity: 1; pointer-events: none; z-index: -9999;">
    <div id="hidden-invoice-target" style="width: 700px; background: #ffffff; padding: 25px 30px; color: #111; font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px;">
            <img src="{{ asset($generalsetting->dark_logo ?? $generalsetting->white_logo) }}" style="max-width:100%;height:auto;max-height:85px;object-fit:contain;" alt="{{ $generalsetting->name }}">
            <div style="font-size: 12px; line-height: 1.6; margin-top: 6px;">
                <div><b>Head Office:</b> Rahman Mansion (3rd Floor), Tamakmundi Lane, Reazuddin Bazar, Chittagong</div>
                <div><b>Feni Office:</b> Gazi Cross Road, Gudham Quarter, Railgate, Feni Sadar, Feni</div>
                <div><b>Call:</b> 01878763643, 01301681418 (WhatsApp), 01830350738</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 20px; margin-top: 16px;">
            <div style="flex: 1;">
                <div style="display: inline-block; background: #000; color: #fff; padding: 3px 10px; font-weight: bold; margin-bottom: 6px; font-size: 13px;">Invoice Info</div>
                <p style="margin-bottom:3px; font-size:13px;"><b>Invoice No:</b> #{{ $order->invoice_id }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Invoice Date:</b> {{ $order->created_at->format('j F, Y') }}</p>

                <div style="display: inline-block; background: #000; color: #fff; padding: 3px 10px; font-weight: bold; margin-top: 14px; margin-bottom: 6px; font-size: 13px;">Bill To</div>
                <p style="margin-bottom:3px; font-size:13px;"><b>Name:</b> {{ $order->shipping->name ?? $order->customer->name ?? 'N/A' }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Address:</b> {{ $order->shipping->address ?? '' }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Contact:</b> {{ $order->shipping->phone ?? $order->customer->phone ?? '' }}</p>
            </div>

            <div style="flex: 1; text-align: right; padding-right: 10px;">
                <p style="margin-bottom:3px; font-size:13px;"><b>Category:</b> {{ $categoryNames ?: '—' }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Total Bill:</b> <b>{{ number_format($order->amount, 0) }} BDT</b></p>

                <div style="margin-top: 35px">
                    <p style="margin-bottom:3px; font-size:13px;"><b>Order Date:</b> {{ $order->order_date ? $order->order_date->format('j F, Y') : $order->created_at->format('j F, Y') }}</p>
                    <p style="margin-bottom:3px; font-size:13px;"><b>Delivery Date:</b> {{ $order->delivery_date ? $order->delivery_date->format('j F, Y') : 'N/A' }}</p>
                    <p style="margin-bottom:3px; font-size:13px;"><b>Paid Amount:</b> {{ number_format($totalPaid, 0) }}</p>
                    <p style="margin-bottom:3px; font-size:13px;"><b>Due Amount:</b> {{ number_format($dueAmount, 0) }}</p>
                </div>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px; font-size:13px;">
            <thead>
                <tr>
                    <th style="border: 2px solid #222; padding: 7px; text-align: center; background: #111; color: #fff; font-weight: bold;">SL</th>
                    <th style="border: 2px solid #222; padding: 7px; text-align: center; background: #111; color: #fff; font-weight: bold;">Product Code</th>
                    <th style="border: 2px solid #222; padding: 7px; text-align: center; background: #111; color: #fff; font-weight: bold;">Colour</th>
                    <th style="border: 2px solid #222; padding: 7px; text-align: center; background: #111; color: #fff; font-weight: bold;">Price</th>
                    <th style="border: 2px solid #222; padding: 7px; text-align: center; background: #111; color: #fff; font-weight: bold;">Order Qty</th>
                    <th style="border: 2px solid #222; padding: 7px; text-align: center; background: #111; color: #fff; font-weight: bold;">Amount (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderdetails as $item)
                <tr>
                    <td style="border: 2px solid #222; padding: 7px; text-align: center;">{{ $loop->iteration }}</td>
                    <td style="border: 2px solid #222; padding: 7px; text-align: center;">{{ $item->product->product_code ?? $item->product_id }}</td>
                    <td style="border: 2px solid #222; padding: 7px; text-align: center;">{{ $item->product_color ?? ($item->product_size ?? '—') }}</td>
                    <td style="border: 2px solid #222; padding: 7px; text-align: center;">{{ number_format($item->sale_price, 0) }}</td>
                    <td style="border: 2px solid #222; padding: 7px; text-align: center;">{{ $item->qty }}</td>
                    <td style="border: 2px solid #222; padding: 7px; text-align: center;">{{ number_format($item->sale_price * $item->qty, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="width: 280px; margin-left: auto; margin-right: 5px; margin-top: 16px; border: 2px solid #222; font-size:13px;">
            <div style="display: flex; border-bottom: 2px solid #222;"><span style="flex: 1; padding: 6px; border-right: 2px solid #222;">Sub Total</span><span style="flex: 1; padding: 6px; text-align: right;">{{ number_format($subTotal, 0) }}</span></div>
            <div style="display: flex; border-bottom: 2px solid #222;"><span style="flex: 1; padding: 6px; border-right: 2px solid #222;">Discount</span><span style="flex: 1; padding: 6px; text-align: right;">{{ number_format($order->discount, 0) }}</span></div>
            <div style="display: flex; border-bottom: 2px solid #222;"><span style="flex: 1; padding: 6px; border-right: 2px solid #222;">Shipping Charge</span><span style="flex: 1; padding: 6px; text-align: right;">{{ number_format($order->shipping_charge, 0) }}</span></div>
            <div style="display: flex;"><span style="flex: 1; padding: 6px; border-right: 2px solid #222; font-weight: bold;">Total</span><span style="flex: 1; padding: 6px; text-align: right; font-weight: bold;">{{ number_format($order->amount, 0) }}</span></div>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 20px; margin-top: 30px;">
            <div style="flex: 1;">
                <div style="display: inline-block; background: #111; color: #fff; padding: 3px 10px; font-weight: bold; font-size:13px;">Payment Summary</div>
                <p style="margin-bottom:3px; font-size:13px;"><b>Total Bill:</b> {{ number_format($order->amount, 0) }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Paid Amount:</b> {{ number_format($totalPaid, 0) }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Due Amount:</b> {{ number_format($dueAmount, 0) }}</p>
                <p style="margin-bottom:3px; font-size:13px;"><b>Next Due Date:</b> N/A</p>

                <div style="margin-top: 6px;">
                    @forelse($order->paymentHistories ?? [] as $ph)
                    <div style="font-size: 11px; padding: 1px 0; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between;">
                        <span>{{ $ph->payment_date->format('d/m/y') }} — {{ $ph->payment_method }} @if($ph->trx_id)({{ $ph->trx_id }})@endif</span>
                        <span>{{ number_format($ph->amount, 0) }}</span>
                    </div>
                    @empty
                    <div style="font-size: 11px; color: #888;">No payments recorded yet</div>
                    @endforelse
                </div>
            </div>

            <div style="flex: 1; margin-top: 45px; text-align: right; padding-right: 10px;">
                <div style="width: 140px; border-top: 2px solid black; margin-left: auto; margin-bottom: 4px;"></div>
                <p style="text-align: right; margin-bottom: 0; font-size:12px; line-height:1.4;"><b>Authorized By:</b><br>Rahatul Goni (Rahat)<br>Co-Founder &amp; CMO<br>Call: 01878763643</p>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script src="{{asset('frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('frontEnd/')}}/js/form-validation.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#btn-download-invoice').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var originalText = $btn.html();
            $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Downloading...').prop('disabled', true);

            var element = document.getElementById('hidden-invoice-target');
            var opt = {
                margin:       [8, 8, 8, 8],
                filename:     'Invoice-{{ $order->invoice_id }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false, scrollX: 0, scrollY: 0 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                $btn.html(originalText).prop('disabled', false);
            }).catch(function(err) {
                $btn.html(originalText).prop('disabled', false);
            });
        });
    });
</script>
<!-- Data Layer Script for Order Success Event -->
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'event': 'purchase',
        'transactionId': '{{ $order->invoice_id }}',
        'transactionTotal': {{ $order->amount }},
        'transactionProducts': [
            @foreach($order->orderdetails as $detail)
                {
                    'name': '{{ $detail->product_name }}',
                    'id': '{{ $detail->product_id }}',  // Assuming you have a product_id
                    'price': {{ $detail->sale_price }},
                    'quantity': {{ $detail->qty }},
                    'size': '{{ $detail->product_size }}',
                    'color': '{{ $detail->product_color }}'
                } @if (!$loop->last),@endif
            @endforeach
        ],
        'paymentMethod': '{{ $payments->payment_method }}',
        'shippingCost': {{ $order->shipping_charge }}
    });
</script>
@endpush