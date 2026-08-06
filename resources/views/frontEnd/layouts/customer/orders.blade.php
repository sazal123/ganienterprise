@extends('frontEnd.layouts.master')
@section('title','Customer Account')
@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="customer-sidebar">
                    @include('frontEnd.layouts.customer.sidebar')
                </div>
            </div>
            <div class="col-sm-9">
                <div class="customer-content">
                    <h5 class="account-title">My Order</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $key=>$value)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$value->created_at->format('d-m-y')}}</td>
                                    <td>৳{{$value->amount}}</td>
                                    <td>৳{{$value->discount}}</td>
                                    <td>{{$value->status?$value->status->name:''}}</td>
                                    <td>
                                        <button type="button" class="invoice_btn btn-download-invoice border-0" data-target="#hidden-invoice-{{ $value->id }}" data-filename="Invoice-{{ $value->invoice_id }}.pdf" title="Download Invoice">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    @if($value->admin_note)
                                    <a href="{{route('customer.order_note',['id'=>$value->id])}}" class="invoice_btn bg-primary"><i class="fa-solid fa-pencil"></i></a>
                                    @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Hidden Invoice HTML Containers for Direct PDF Download --}}
@foreach($orders as $order)
@php
    $subTotal = $order->orderdetails->sum(fn($d) => $d->sale_price * $d->qty);
    $categoryNames = $order->orderdetails->map(fn($d) => $d->product->category->name ?? null)->filter()->unique()->implode(', ');
    $paymentHistories = $order->paymentHistories ?? [];
    $totalPaid = $paymentHistories ? $paymentHistories->sum('amount') : 0;
    $dueAmount = max($order->amount - $totalPaid, 0);
@endphp

<div style="position: absolute; left: -9999px; top: 0; width: 700px; opacity: 1; pointer-events: none; z-index: -9999;">
    <div id="hidden-invoice-{{ $order->id }}" style="width: 700px; background: #ffffff; padding: 25px 30px; color: #111; font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;">
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

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; margin-top: 16px; font-size:13px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; background: #111; color: #fff; font-weight: bold;">SL</th>
                    <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; background: #111; color: #fff; font-weight: bold;">Product Code</th>
                    <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; background: #111; color: #fff; font-weight: bold;">Colour</th>
                    <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; background: #111; color: #fff; font-weight: bold;">Price</th>
                    <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; background: #111; color: #fff; font-weight: bold;">Order Qty</th>
                    <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; background: #111; color: #fff; font-weight: bold;">Amount (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderdetails as $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">{{ $item->product->product_code ?? $item->product_id }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">{{ $item->product_color ?? ($item->product_size ?? '—') }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">{{ number_format($item->sale_price, 0) }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">{{ $item->qty }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">{{ number_format($item->sale_price * $item->qty, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="width: 280px; margin-left: auto; margin-top: 16px; border: 1px solid #000; font-size:13px;">
            <div style="display: flex; border-bottom: 1px solid #000;"><span style="flex: 1; padding: 6px 10px; border-right: 1px solid #000;">Sub Total</span><span style="flex: 1; padding: 6px 10px; text-align: right;">{{ number_format($subTotal, 0) }}</span></div>
            <div style="display: flex; border-bottom: 1px solid #000;"><span style="flex: 1; padding: 6px 10px; border-right: 1px solid #000;">Discount</span><span style="flex: 1; padding: 6px 10px; text-align: right;">{{ number_format($order->discount, 0) }}</span></div>
            <div style="display: flex; border-bottom: 1px solid #000;"><span style="flex: 1; padding: 6px 10px; border-right: 1px solid #000;">Shipping Charge</span><span style="flex: 1; padding: 6px 10px; text-align: right;">{{ number_format($order->shipping_charge, 0) }}</span></div>
            <div style="display: flex;"><span style="flex: 1; padding: 6px 10px; border-right: 1px solid #000; font-weight: bold;">Total</span><span style="flex: 1; padding: 6px 10px; text-align: right; font-weight: bold;">{{ number_format($order->amount, 0) }}</span></div>
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
@endforeach

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-download-invoice').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var originalHtml = $btn.html();
            var targetSelector = $btn.attr('data-target');
            var filename = $btn.attr('data-filename');

            $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

            var element = document.querySelector(targetSelector);
            if (!element) {
                $btn.html(originalHtml).prop('disabled', false);
                alert('Invoice template not found.');
                return;
            }

            var opt = {
                margin:       [8, 8, 8, 8],
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false, scrollX: 0, scrollY: 0 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                $btn.html(originalHtml).prop('disabled', false);
            }).catch(function(err) {
                $btn.html(originalHtml).prop('disabled', false);
            });
        });
    });
</script>
@endpush
@endsection