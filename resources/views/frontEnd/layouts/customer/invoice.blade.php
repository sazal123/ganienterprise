@extends('frontEnd.layouts.master')
@section('title', 'Customer Invoice')
@push('css')
<style>
.invoice-page {
    width: 210mm;
    min-height: 297mm;
    background: #fff;
    margin: 30px auto;
    padding: 24px;
    color: #111;
    font-family: Arial, Helvetica, sans-serif;
    box-sizing: border-box;
    border: 1px solid #ddd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.invoice-page .header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
}
.invoice-page .brand h1 { margin: 0; font-size: 28px; font-weight: bold; }
.invoice-page .brand p { margin: 4px 0 0; font-style: italic; font-weight: bold; }
.invoice-page .contact { margin-top: 10px; font-size: 14px; line-height: 1.6; }
.invoice-page .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 18px;
}
.invoice-page .label {
    display: inline-block;
    background: #000;
    color: #fff;
    padding: 4px 12px;
    font-weight: bold;
    margin-bottom: 8px;
}
.invoice-page .inv-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
.invoice-page .inv-table th, .invoice-page .inv-table td {
    border: 2px solid #222;
    padding: 8px;
    text-align: center;
}
.invoice-page .inv-table th {
    background: #111;
    color: #fff;
    font-weight: bold;
}
.invoice-page .summary {
    width: 310px;
    margin-left: auto;
    margin-top: 20px;
    border: 2px solid #222;
}
.invoice-page .summary div {
    display: flex;
    border-bottom: 2px solid #222;
}
.invoice-page .summary div:last-child {
    border-bottom: none;
    font-weight: bold;
}
.invoice-page .summary span {
    flex: 1;
    padding: 8px;
}
.invoice-page .summary span:last-child {
    text-align: right;
}
.invoice-page .bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 35px;
}
.invoice-page .box-title {
    display: inline-block;
    background: #111;
    color: #fff;
    padding: 4px 10px;
    font-weight: bold;
}
.invoice-page .signature {
    margin-top: 60px;
    text-align: right;
    position: relative;
}
.invoice-page .signature .line {
    width: 50%;
    border-top: 2px solid black;
    position: absolute;
    right: 0;
}
.invoice-page p { margin-bottom: 4px; }
.sub-total { border-right: 2px solid #222; }

@media print {
    @page {
        size: auto;
        margin: 0;
    }
    html, body {
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    body * {
        visibility: hidden !important;
    }
    .invoice-page, .invoice-page * {
        visibility: visible !important;
    }
    .invoice-page {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 15px !important;
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
    }
    .no-print, header, footer, nav, .whatsapp-float, .footer_nav, .gani-desktop-header, .gani-mobile-header, .header-top, .gani-mobile-sidebar, .gani-mobile-overlay, .gani-mobile-search, .mobile-menu {
        display: none !important;
    }
    .invoice-page .inv-table th {
        background: #111 !important;
        color: #fff !important;
        font-weight: bold !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .invoice-page .label,
    .invoice-page .box-title {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
.signature p{text-align:right;}
</style>
@endpush

@section('content')
@php
    $subTotal = $order->orderdetails->sum(fn($d) => $d->sale_price * $d->qty);
    $categoryNames = $order->orderdetails->map(fn($d) => $d->product->category->name ?? null)->filter()->unique()->implode(', ');
@endphp

<section class="py-4">
    <div class="container">
        {{-- Navigation bar for screen view --}}
        <div class="no-print d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('customer.orders') }}" class="btn btn-outline-dark btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back To Orders
            </a>
            <button onclick="window.print()" class="btn btn-success btn-sm fw-bold">
                <i class="fa-solid fa-print me-1"></i> Print Invoice
            </button>
        </div>

        {{-- Invoice Printable Container --}}
        <div class="invoice-page">
            <div class="header">
                <img src="{{ asset('backEnd/assets/images/invoices/invoice_logo.jpeg') }}" style="max-width:100%;height:auto;max-height:100px;object-fit:contain;margin-bottom:10px;" alt="GANI ENTERPRISE">
                <div class="contact">
                    <div><i class="fa fa-map-marker"></i> <b>Head Office:</b> Rahman Mansion (3rd Floor), Tamakmundi Lane, Reazuddin Bazar, Chittagong</div>
                    <div><i class="fa fa-map-marker"></i> <b>Feni Office:</b> Gazi Cross Road, Gudham Quarter, Railgate, Feni Sadar, Feni</div>
                    <div><i class="fa fa-phone"></i> <b>Call:</b> 01878763643, 01301681418 (WhatsApp), 01830350738</div>
                </div>
            </div>

            <div class="grid">
                <div>
                    <div class="label"><b>Invoice Info</b></div>
                    <p><b>Invoice No:</b> #{{ $order->invoice_id }}</p>
                    <p><b>Invoice Date:</b> {{ $order->created_at->format('j F, Y') }}</p>

                    <div class="label" style="margin-top:20px"><b>Bill To</b></div>
                    <p><b>Shop Name:</b> {{ $order->shipping->name ?? $order->customer->name ?? 'N/A' }}</p>
                    <p><b>Address:</b> {{ $order->shipping->address ?? '' }}</p>
                    <p><b>Contact:</b> {{ $order->shipping->phone ?? $order->customer->phone ?? '' }}</p>
                </div>

                <div style="text-align: right">
                    <p><b>Category:</b> {{ $categoryNames ?: '—' }}</p>
                    <p><b>Total Bill:</b> <b>{{ number_format($order->amount, 0) }} BDT</b></p>

                    <div style="margin-top:45px">
                        <p><b>Order Date:</b> {{ $order->order_date ? $order->order_date->format('j F, Y') : $order->created_at->format('j F, Y') }}</p>
                        <p><b>Delivery Date:</b> {{ $order->delivery_date ? $order->delivery_date->format('j F, Y') : 'N/A' }}</p>
                        <p><b>Paid Amount:</b> {{ number_format($totalPaid, 0) }}</p>
                        <p><b>Due Amount:</b> {{ number_format($dueAmount, 0) }}</p>
                    </div>
                </div>
            </div>

            <table class="inv-table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Product Code</th>
                        <th>Colour</th>
                        <th>Price</th>
                        <th>Order Qty</th>
                        <th>Amount (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderdetails as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product->product_code ?? $item->product_id }}</td>
                        <td>{{ $item->product_color ?? ($item->product_size ?? '—') }}</td>
                        <td>{{ number_format($item->sale_price, 0) }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ number_format($item->sale_price * $item->qty, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div><span class="sub-total">Sub Total</span><span>{{ number_format($subTotal, 0) }}</span></div>
                <div><span class="sub-total">Discount</span><span>{{ number_format($order->discount, 0) }}</span></div>
                <div><span class="sub-total">Shipping Charge</span><span>{{ number_format($order->shipping_charge, 0) }}</span></div>
                <div><span class="sub-total">Total</span><span>{{ number_format($order->amount, 0) }}</span></div>
            </div>

            <div class="bottom">
                <div>
                    <div class="box-title"><b>Payment Summary</b></div>
                    <p><b>Total Bill:</b> {{ number_format($order->amount, 0) }}</p>
                    <p><b>Paid Amount:</b> {{ number_format($totalPaid, 0) }}</p>
                    <p><b>Due Amount:</b> {{ number_format($dueAmount, 0) }}</p>
                    <p><b>Next Due Date:</b> N/A</p>

                    <div style="margin-top:8px;">
                        @forelse($order->paymentHistories ?? [] as $ph)
                        <div style="font-size:11px;padding:1px 0;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;">
                            <span>{{ $ph->payment_date->format('d/m/y') }} — {{ $ph->payment_method }} @if($ph->trx_id)({{ $ph->trx_id }})@endif</span>
                            <span>{{ number_format($ph->amount, 0) }}</span>
                        </div>
                        @empty
                        <div style="font-size:11px;color:#888;">No payments recorded yet</div>
                        @endforelse
                    </div>
                </div>

                <div class="signature">
                    <div class="line"></div>
                    <p><b>Authorized By:</b><br>Rahatul Goni (Rahat)<br>Co-Founder &amp; CMO<br>Call: 01878763643</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
