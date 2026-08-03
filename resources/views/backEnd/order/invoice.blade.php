@extends('backEnd.layouts.master')
@section('title','Order Invoice')
@section('css')
<style>
.invoice-page{width:210mm;min-height:297mm;background:#fff;margin:20px auto;padding:18px;color:#111;font-family:Arial,Helvetica,sans-serif;box-sizing:border-box}
.invoice-page .header{text-align:center;border-bottom:2px solid #000;padding-bottom:10px}
.invoice-page .brand h1{margin:0;font-size:28px;font-weight:bold}
.invoice-page .brand p{margin:4px 0 0;font-style:italic;font-weight:bold}
.invoice-page .contact{margin-top:10px;font-size:14px;line-height:1.6}
.invoice-page .grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:18px}
.invoice-page .label{display:inline-block;background:#000;color:#fff;padding:4px 12px;font-weight:bold;margin-bottom:8px}
.invoice-page .inv-table{width:100%;border-collapse:collapse;margin-top:20px}
.invoice-page .inv-table th,.invoice-page .inv-table td{border:2px solid #222;padding:6px;text-align:center}
.invoice-page .inv-table th{background:#111;color:#fff;font-weight:bold}
.invoice-page .summary{width:310px;margin-left:auto;margin-top:20px;border:2px solid #222}
.invoice-page .summary div{display:flex;border-bottom:2px solid #222}
.invoice-page .summary div:last-child{border-bottom:none;font-weight:bold}
.invoice-page .summary span{flex:1;padding:8px}
.invoice-page .summary span:last-child{text-align:right}
.invoice-page .bottom{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:35px}
.invoice-page .box-title{display:inline-block;background:#111;color:#fff;padding:4px 10px;font-weight:bold}
.invoice-page .signature{margin-top:60px;text-align: right; position: relative;}
.invoice-page .signature .line{width: 50%;border-top: 2px solid black;position: absolute; right: 0;}
.invoice-page p{margin-bottom:0px}
.sub-total{border-right:2px solid #222;}
@media print{
 body{padding:0!important}
 .invoice-page{margin:0;width:auto;min-height:auto}
 .no-print, header, footer, .left-side-menu, .navbar-custom{display:none!important}
 .content-page{margin-left:0!important}
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
</style>
@endsection

@section('content')
@php
  $subTotal = $order->orderdetails->sum(fn($d) => $d->sale_price * $d->qty);
  $categoryNames = $order->orderdetails->map(fn($d) => $d->product->category->name ?? null)->filter()->unique()->implode(', ');
@endphp

<div class="no-print" style="text-align:center;margin:20px;">
  <a href="{{ route('admin.orders', 'all') }}" class="btn btn-outline-secondary btn-sm"><i class="fe-arrow-left"></i> Back</a>
  <a href="{{ route('admin.order.edit', $order->invoice_id) }}" class="btn btn-primary btn-sm"><i class="fe-edit"></i> Edit</a>
  <button onclick="window.print()" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Print</button>
  <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal"><i class="fe-dollar-sign"></i> Payment</button>
</div>

<div class="invoice-page">
<div class="header">
<img src="{{ asset($generalsetting->dark_logo ?? $generalsetting->white_logo) }}" style="max-width:100%;height:auto;max-height:100px;object-fit:contain;margin-bottom:10px;" alt="{{ $generalsetting->name }}">
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
<p><b>Name:</b> {{ $order->shipping->name ?? $order->customer->name ?? 'N/A' }}</p>
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
@forelse($order->paymentHistories as $ph)
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
 <div class="modal-dialog modal-sm modal-dialog-centered">
  <form method="post" action="{{ route('admin.order.payment_store') }}" class="modal-content">
   @csrf
   <input type="hidden" name="order_id" value="{{ $order->id }}">
   <div class="modal-header bg-primary text-white py-2">
    <h5 class="modal-title"><i class="fe-dollar-sign"></i> Record Payment</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
   </div>
   <div class="modal-body">
    <div class="mb-2">
     <label class="form-label small">Amount (Due: ৳{{ number_format($dueAmount, 0) }})</label>
     <input type="number" step="0.01" name="amount" class="form-control form-control-sm" required max="{{ $dueAmount }}">
    </div>
    <div class="mb-2">
     <label class="form-label small">Payment Method</label>
     <select name="payment_method" class="form-control form-control-sm">
      <option value="Cash">Cash</option>
      <option value="bkash">bKash</option>
      <option value="Nagad">Nagad</option>
      <option value="Bank">Bank</option>
     </select>
    </div>
    <div class="mb-2">
     <label class="form-label small">Trx ID</label>
     <input type="text" name="trx_id" class="form-control form-control-sm">
    </div>
    <div class="mb-2">
     <label class="form-label small">Sender Number</label>
     <input type="text" name="sender_number" class="form-control form-control-sm">
    </div>
    <div class="mb-2">
     <label class="form-label small">Payment Date</label>
     <input type="date" name="payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
    </div>
    <div class="mb-2">
     <label class="form-label small">Note</label>
     <input type="text" name="note" class="form-control form-control-sm" placeholder="Optional note">
    </div>
   </div>
   <div class="modal-footer py-2">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fe-check"></i> Save Payment</button>
   </div>
  </form>
 </div>
 </div>
</div>
@endsection
