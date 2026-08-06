@extends('frontEnd.layouts.master') 
@section('title', 'Customer Checkout') 
@push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/select2.min.css') }}" />
@endpush 
@section('content')
<section class="chheckout-section">
    @php
        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
    @endphp
    <div class="container">
        <div class="row">
            <!-- Order Summary Column (First on Mobile) -->
            <div class="col-sm-7 cust-order-1">
                <div class="cart_details table-responsive-sm">
                    <div class="card">
                        <div class="card-header">
                            <h5>Order Summary</h5>
                        </div>
                        <div class="card-body cartlist">
                            <table class="cart_table table table-bordered table-striped text-center mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Action</th>
                                        <th style="width: 45%;">Product</th>
                                        <th style="width: 20%;">Quantity</th>
                                        <th style="width: 20%;">Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach (Cart::instance('shopping')->content() as $value)
                                        <tr>
                                            <td>
                                                <a class="cart_remove" data-id="{{ $value->rowId }}"><i
                                                        class="fas fa-trash text-danger"></i></a>
                                            </td>
                                            <td class="text-left">
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <a href="{{ route('product', $value->options->slug) }}" class="fw-semibold text-dark text-decoration-none">
                                                            {{ Str::limit($value->name, 25) }}
                                                        </a>
                                                        @if ($value->options->product_size)
                                                            <p class="mb-0 text-muted small">Size: {{ $value->options->product_size }}</p>
                                                        @endif
                                                        @if ($value->options->product_color)
                                                            <p class="mb-0 text-muted small">Color: {{ $value->options->product_color }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <a href="{{ route('product', $value->options->slug) }}">
                                                            @php
                                                                $itemImg = $value->options->image;
                                                                if ($value->options->product_color) {
                                                                    $colorModel = \App\Models\Color::where('colorName', $value->options->product_color)->first();
                                                                    if ($colorModel) {
                                                                        $cImg = \App\Models\Productimage::where('product_id', $value->id)->where('color_id', $colorModel->id)->first();
                                                                        if (!$cImg) {
                                                                            $pcs = \App\Models\Productcolor::where('product_id', $value->id)->get();
                                                                            $idx = $pcs->pluck('color_id')->search($colorModel->id);
                                                                            if ($idx !== false) {
                                                                                $cImg = \App\Models\Productimage::where('product_id', $value->id)->get()->get($idx);
                                                                            }
                                                                        }
                                                                        if ($cImg && !empty($cImg->image)) {
                                                                            $itemImg = $cImg->image;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <img src="{{ asset($itemImg) }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #eee;" alt="" />
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="cart_qty">
                                                <div class="qty-cart vcart-qty">
                                                    <div class="quantity">
                                                        <button class="minus cart_decrement"
                                                            data-id="{{ $value->rowId }}">-</button>
                                                        <input type="text" value="{{ $value->qty }}" readonly />
                                                        <button class="plus cart_increment"
                                                            data-id="{{ $value->rowId }}">+</button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="alinur">৳ </span><strong>{{ $value->price }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end px-4">Subtotal</th>
                                        <td class="px-4">
                                            <span id="net_total"><span class="alinur">৳
                                                </span><strong>{{ $subtotal }}</strong></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end px-4">Shipping Charge</th>
                                        <td class="px-4">
                                            <span id="cart_shipping_cost"><span class="alinur">৳
                                                </span><strong>{{ $shipping }}</strong></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end px-4">Grand Total</th>
                                        <td class="px-4">
                                            <span id="grand_total"><span class="alinur">৳
                                                </span><strong>{{ $subtotal + $shipping }}</strong></span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-footer text-danger">
                            {!! $generalsetting->checkout_note !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- col end -->

            <!-- Place Order Form Column (Second on Mobile) -->
            <div class="col-sm-5 cus-order-2">
                <div class="checkout-shipping">
                    <form action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate="">
                        @csrf
                        <div class="card">
                           <div class="card-header">
                                <h6>To confirm your order, fill in the information below and click the <span style="color:#fe5200;">"Place Order"</span> button
                                @if(isset($contact) && $contact && $contact->hotline)
                                    , or call us directly at <a href="tel:{{ $contact->hotline }}">{{ $contact->hotline }}</a>.
                                @else
                                    .
                                @endif
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="name">Your Name *</label>
                                            <input type="text" id="name"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}"
                                                placeholder="Enter your full name"
                                                required/>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="phone">Your Phone Number *</label>
                                            <input type="text" minlength="11" id="phone" maxlength="11"
                                                pattern="0[0-9]+"
                                                title="Please enter an 11-digit number starting with 0."
                                                class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                value="{{ old('phone') }}"
                                                placeholder="Enter 11-digit phone number"
                                                required/>
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="address">Full Address *</label>
                                            <input type="text" id="address"
                                                class="form-control @error('address') is-invalid @enderror"
                                                name="address"
                                                value="{{ old('address') }}"
                                                placeholder="House, Road, Thana, District..."
                                                required/>
                                            @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="area">Select Delivery Area *</label>
                                            <select id="area"
                                                class="form-control @error('area') is-invalid @enderror" name="area"
                                                required>
                                                @foreach ($shippingcharge as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('area')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->

                                    <div class="col-sm-12">
                                        <div class="radio_payment">
                                            <label id="payment_method">Payment Method</label>
                                            <div class="payment_option"></div>
                                        </div>
                                        <div class="payment-methods">
                                            <div class="form-check p_cash">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                id="inlineRadio1" value="Cash On Delivery" checked required />
                                                <label class="form-check-label" for="inlineRadio1">
                                                    Cash On Delivery
                                                </label>
                                            </div>
                                            @if($bkash_gateway)
                                            <div class="form-check p_bkash">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                id="inlineRadio2" value="bkash" required/>
                                                <label class="form-check-label" for="inlineRadio2">
                                                    Bkash
                                                </label>
                                            </div>
                                            @endif
                                            
                                            @if($shurjopay_gateway)
                                            <div class="form-check p_shurjo">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                id="inlineRadio3" value="shurjopay" required/>
                                                <label class="form-check-label" for="inlineRadio3">
                                                    Shurjopay
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group mt-3">
                                            <button class="order_place" type="submit">Place Order</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- card end -->
                    </form>
                </div>
            </div>
            <!-- col end -->
        </div>
    </div>
</section>
@endsection 

@push('script')
<script src="{{ asset('frontEnd/') }}/js/parsley.min.js"></script>
<script src="{{ asset('frontEnd/') }}/js/form-validation.init.js"></script>
<script src="{{ asset('frontEnd/') }}/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $(".select2").select2();
    });
</script>
<script>
    $("#area").on("change", function() {
        var id = $(this).val();
        $.ajax({
            type: "GET",
            data: {
                id: id
            },
            url: "{{ route('shipping.charge') }}",
            dataType: "html",
            success: function(response) {
                $(".cartlist").html(response);
            },
        });
    });
</script>
<script type="text/javascript">
    dataLayer.push({ ecommerce: null }); 
    dataLayer.push({
        event    : "view_cart",
        ecommerce: {
            items: [@foreach (Cart::instance('shopping')->content() as $cartInfo){
                item_name     : "{{$cartInfo->name}}",
                item_id       : "{{$cartInfo->id}}",
                price         : "{{$cartInfo->price}}",
                item_brand    : "{{$cartInfo->options->brand}}",
                item_category : "{{$cartInfo->options->category}}",
                item_size     : "{{$cartInfo->options->size}}",
                item_color    : "{{$cartInfo->options->color}}",
                currency      : "BDT",
                quantity      : {{$cartInfo->qty ?? 0}}
            },@endforeach]
        }
    });
</script>
<script type="text/javascript">
    dataLayer.push({ ecommerce: null });
    dataLayer.push({
        event: "begin_checkout",
        ecommerce: {
            items: [@foreach (Cart::instance('shopping')->content() as $cartInfo)
                {
                    item_name: "{{$cartInfo->name}}",
                    item_id: "{{$cartInfo->id}}",
                    price: "{{$cartInfo->price}}",
                    item_brand: "{{$cartInfo->options->brands}}",
                    item_category: "{{$cartInfo->options->category}}",
                    item_size: "{{$cartInfo->options->size}}",
                    item_color: "{{$cartInfo->options->color}}",
                    currency: "BDT",
                    quantity: {{$cartInfo->qty ?? 0}}
                },
            @endforeach]
        }
    });
</script>
@endpush
