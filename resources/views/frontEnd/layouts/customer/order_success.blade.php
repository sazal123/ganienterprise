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

            $.get("{{ route('customer.invoice', ['id' => $order->id]) }}", function(html) {
                var $temp = $('<div>').html(html);
                var $invoice = $temp.find('.invoice-page');
                
                if ($invoice.length === 0) {
                    $btn.html(originalText).prop('disabled', false);
                    alert('Invoice could not be generated.');
                    return;
                }

                $invoice.css({
                    position: 'absolute',
                    left: '-9999px',
                    top: '0',
                    width: '210mm',
                    background: '#ffffff'
                }).appendTo('body');

                var opt = {
                    margin:       [5, 5, 5, 5],
                    filename:     'Invoice-{{ $order->invoice_id }}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true, logging: false },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from($invoice[0]).save().then(function() {
                    $invoice.remove();
                    $btn.html(originalText).prop('disabled', false);
                }).catch(function(err) {
                    $invoice.remove();
                    $btn.html(originalText).prop('disabled', false);
                });
            }).fail(function() {
                $btn.html(originalText).prop('disabled', false);
                alert('Failed to download invoice. Please try again.');
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