@extends('backEnd.layouts.master')
@section('title','Order Create')
@section('css')
<style>
    .increment_btn, .remove_btn { margin-top: -17px; margin-bottom: 10px; }
    .variant-btn { border-radius: 6px; cursor: pointer; transition: all .15s ease; }
    .variant-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,.12); }
    .variant-btn.active { border-color: #0056b3 !important; background: #e8f0fe !important; box-shadow: 0 0 0 2px rgba(0,86,179,.25); }
    .color-swatch { display: inline-block; width: 28px; height: 28px; border-radius: 50%; border: 2px solid #dee2e6; margin-right: 6px; vertical-align: middle; }
    .cart-scroll { max-height: 70vh; overflow-y: auto; }
    .cart-scroll table { margin-bottom: 0; }
    .row.align-items-start { display: flex; align-items: flex-start !important; }
    #wrapper{overflow-y: auto}
</style>
<link href="{{asset('backEnd/assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/summernote/summernote-lite.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <form method="get" action="{{route('admin.order.cart_clear')}}" class="d-inline">
                        @csrf
                    <button type="submit" class="btn btn-danger rounded-pill delete-confirm" title="Delete"><i class="fas fa-trash-alt"></i> Cart Clear</button></form>
                </div>
                <h4 class="page-title">Order Create</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.order.update')}}" method="POST" class="pos_form" data-parsley-validate="" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{$order->id}}" name="order_id">

                        <div class="row align-items-start">
                        <!-- Left column: Product select + Cart -->
                        <div class="col-sm-7">
                            <div class="form-group mb-3">
                                <label for="cart_add" class="form-label">Products *</label>
                                <select id="cart_add" class="form-control select2 @error('product_id') is-invalid @enderror">
                                    <option value="">Select..</option>
                                    @foreach($products as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                          <div class="cart-scroll">
                          <table class="table table-bordered" style="margin-bottom:0;">
                            <thead>
                              <tr>
                                <th style="width:8%">Image</th>
                                <th style="width:22%">Product</th>
                                <th style="width:10%">Variant</th>
                                <th style="width:14%">Quantity</th>
                                <th style="width:12%">Price</th>
                                <th style="width:11%">Disc.</th>
                                <th style="width:13%">Subtotal</th>
                                <th style="width:10%">Action</th>
                              </tr>
                            </thead>
                            <tbody id="cartTable">
                              @php $product_discount = 0; @endphp
                              @foreach($cartinfo as $key=>$value)
                              <tr>
                                <td><img height="30" src="{{asset($value->options->image)}}" style="border-radius:4px;object-fit:cover;width:40px;"></td>
                                <td style="font-size:12px;">{{$value->name}}</td>
                                <td style="font-size:11px;">
                                  @if($value->options->product_color || $value->options->product_size)
                                    <span class="badge bg-info" style="font-size:10px;">{{ $value->options->product_color ?: '' }}{{ $value->options->product_color && $value->options->product_size ? ' / ' : '' }}{{ $value->options->product_size ?: '' }}</span>
                                  @else
                                    <span class="text-muted">—</span>
                                  @endif
                                </td>
                                <td>
                                  <div class="input-group input-group-sm" style="max-width:105px;">
                                    <button class="btn btn-outline-secondary cart_decrement" data-id="{{$value->rowId}}">-</button>
                                    <input type="text" class="form-control text-center" value="{{$value->qty}}" readonly />
                                    <button class="btn btn-outline-secondary cart_increment" data-id="{{$value->rowId}}">+</button>
                                  </div>
                                </td>
                                <td style="font-size:12px;">{{$value->price}}</td>
                                <td><input type="number" class="form-control form-control-sm product_discount" style="width:55px;" value="{{$value->options->product_discount}}" placeholder="0" data-id="{{$value->rowId}}"></td>
                                <td style="font-size:12px;">{{($value->price - $value->options->product_discount)*$value->qty}}</td>
                                <td><button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{$value->rowId}}"><i class="fa fa-times"></i></button></td>
                              </tr>
                              @php
                              $product_discount += $value->options->product_discount*$value->qty;
                              Session::put('product_discount',$product_discount);
                              @endphp
                              @endforeach
                            </tbody>
                          </table>
                          </div>
                        </div>
                        <!-- Right column: Customer Info + Summary + Save -->
                        <div class="col-sm-5">
                          <div class="card bg-light mb-3" style="border:1px solid #dee2e6;">
                           <div class="card-body py-3 px-3">
                            <h6 class="fw-bold mb-2"><i class="fe-user"></i> Customer Information</h6>
                            <div class="mb-2"><input type="text" class="form-control form-control-sm" placeholder="Customer Name" name="name" value="{{$shippinginfo->name}}" required></div>
                            <div class="mb-2"><input type="number" class="form-control form-control-sm" placeholder="Phone Number" name="phone" value="{{$shippinginfo->phone}}" required></div>
                            <div class="mb-2"><input type="text" placeholder="Address" class="form-control form-control-sm" name="address" value="{{$shippinginfo->address}}" required></div>
                            <div class="mb-2">
                              <select class="form-control form-control-sm" name="area" required>
                               <option value="">Delivery Area</option>
                               @foreach($shippingcharge as $sc)
                               <option value="{{$sc->id}}" @if($shippinginfo->area == $sc->name) selected @endif>{{$sc->name}}</option>
                              @endforeach
                              </select>
                            </div>
                            <div class="row g-1">
                             <div class="col-6"><label style="font-size:10px;color:#888;">Order Date</label><input type="date" name="order_date" class="form-control form-control-sm" value="{{ $order->order_date ? $order->order_date->format('Y-m-d') : date('Y-m-d') }}"></div>
                             <div class="col-6"><label style="font-size:10px;color:#888;">Delivery Date</label><input type="date" name="delivery_date" class="form-control form-control-sm" value="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}"></div>
                            </div>
                           </div>
                          </div>
                          <div class="card bg-light" style="border:1px solid #dee2e6;">
                           <div class="card-body py-3 px-3">
                            <h6 class="fw-bold mb-2"><i class="fe-shopping-cart"></i> Order Summary</h6>
                            <table class="table table-sm table-borderless mb-2" style="font-size:13px;">
                             <tbody id="cart_details">
                              @php
                                  $subtotal = Cart::instance('pos_shopping')->subtotal();
                                  $subtotal = str_replace(',','',$subtotal);
                                  $subtotal = str_replace('.00', '',$subtotal);
                                  $shipping = Session::get('pos_shipping');
                                  $total_discount = Session::get('pos_discount')+Session::get('product_discount');
                              @endphp
                              <tr><td>Sub Total</td><td class="text-end">{{$subtotal}}</td></tr>
                              <tr><td>Shipping Fee</td><td class="text-end">{{$shipping}}</td></tr>
                              <tr><td>Discount</td><td class="text-end">-{{$total_discount}}</td></tr>
                              <tr class="fw-bold" style="border-top:2px solid #dee2e6;"><td>Total</td><td class="text-end">{{($subtotal + $shipping)- $total_discount}}</td></tr>
                             </tbody>
                            </table>
                            <div class="text-end">
                             <input type="submit" class="btn btn-success px-4" value="Update Order" />
                            </div>
                           </div>
                          </div>
                        </div>
                        </div>
                    </form>
                </div>
                <!-- end card-body-->
            </div>
            <!-- end card-->
        </div>
        <!-- end col-->
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('backEnd/assets/libs/parsleyjs/parsley.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-validation.init.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-advanced.init.js')}}"></script>
<!-- Plugins js -->
<script src="{{asset('backEnd/assets/libs//summernote/summernote-lite.min.js')}}"></script>
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
    });
</script>
<script>
    function cart_content(){
           $.ajax({
             type:"GET",
             url:"{{route('admin.order.cart_content')}}",
             dataType: "html",
             success: function(cartinfo){
               $('#cartTable').html(cartinfo)
             }
          });
      }
      function cart_details(){
           $.ajax({
             type:"GET",
             url:"{{route('admin.order.cart_details')}}",
             dataType: "html",
             success: function(cartinfo){
               $('#cart_details').html(cartinfo)
             }
          });
      }
      function refreshCart() { cart_content(); cart_details(); }

      // ---- Variant-aware product select ----
      let selectedProductData = null;
      $('#cart_add').on('change', function() {
        var id = $(this).val();
        if (!id) return;
        var $btn = $(this).prop('disabled', true);

        $.ajax({
          cache: false, type:"GET", data:{ id: id },
          url: "{{route('admin.order.product_variants')}}",
          dataType:"json",
          success: function(p) {
            selectedProductData = p;
            var hasColor = p.colors && p.colors.length > 0;
            var hasSize  = p.sizes && p.sizes.length > 0;

            if (hasColor || hasSize) {
              $('#variantProductId').val(p.id);
              $('#variantModalTitle').text(p.name);
              $('#variantProductName').text(p.name);
              $('#variantProductImage').attr('src', p.image || '{{asset("backEnd/assets/images/default-product.png")}}');
              $('#variantBasePrice').text(p.price);

              if (hasColor) {
                $('#variantColorSection').show();
                var cHtml = '';
                $.each(p.colors, function(i, c) {
                  var swatch = c.code ? '<span class="color-swatch" style="background:'+c.code+';"></span>' : '';
                  cHtml += '<button type="button" class="btn btn-outline-dark btn-sm variant-btn color-btn" data-id="'+c.id+'" data-price="'+(c.price||'')+'">'+ swatch + c.name + (c.price ? ' <small>(৳'+c.price+')</small>' : '') + '</button> ';
                });
                $('#variantColors').html(cHtml);
              } else { $('#variantColorSection').hide(); }

              if (hasSize) {
                $('#variantSizeSection').show();
                var sHtml = '';
                $.each(p.sizes, function(i, s) {
                  sHtml += '<button type="button" class="btn btn-outline-dark btn-sm variant-btn size-btn" data-id="'+s.id+'" data-price="'+(s.price||'')+'">'+ s.name + (s.price ? ' <small>(৳'+s.price+')</small>' : '') + '</button> ';
                });
                $('#variantSizes').html(sHtml);
              } else { $('#variantSizeSection').hide(); }

              $('.variant-btn').removeClass('active');
              $('#variantQty').val(1);
              updateVariantTotal();
              $('#variantModal').modal('show');
            } else {
              addToCart(id, '', '');
            }
          },
          complete: function() { $btn.prop('disabled', false); }
        });
      });

      $(document).on('click', '.color-btn', function() { $('.color-btn').removeClass('active'); $(this).addClass('active'); updateVariantTotal(); });
      $(document).on('click', '.size-btn', function() { $('.size-btn').removeClass('active'); $(this).addClass('active'); updateVariantTotal(); });

      $('#variantQtyMinus').click(function() { var q = parseInt($('#variantQty').val())||1; if (q>1) { $('#variantQty').val(q-1); updateVariantTotal(); } });
      $('#variantQtyPlus').click(function() { var q = parseInt($('#variantQty').val())||1; if (q<999) { $('#variantQty').val(q+1); updateVariantTotal(); } });
      $('#variantQty').on('input', function() { var v=parseInt($(this).val())||1; if(v<1)v=1; if(v>999)v=999; $(this).val(v); updateVariantTotal(); });

      function updateVariantTotal() {
        if (!selectedProductData) return;
        var base = parseFloat(selectedProductData.price) || 0;
        var colorExtra = parseFloat($('.color-btn.active').data('price')) || 0;
        var sizeExtra  = parseFloat($('.size-btn.active').data('price')) || 0;
        var unitPrice  = (colorExtra > 0 ? colorExtra : base) + (sizeExtra > 0 ? sizeExtra : 0);
        if (unitPrice <= 0) unitPrice = base;
        var qty = parseInt($('#variantQty').val()) || 1;
        $('#variantTotalPrice').text(unitPrice.toFixed(2));
        $('#variantBasePrice').text(unitPrice.toFixed(2));
      }

      $('#variantAddToCartBtn').click(function() {
        var productId = $('#variantProductId').val();
        var colorName = $('.color-btn.active').text().trim().replace(/\(.*\)/,'').replace(/[0-9]/g,'').trim() || '';
        var sizeName  = $('.size-btn.active').text().trim().replace(/\(.*\)/,'').replace(/[0-9]/g,'').trim() || '';
        addToCart(productId, colorName, sizeName);
        $('#variantModal').modal('hide');
      });

      function addToCart(productId, color, size) {
        // Check for duplicate: same product + same color + same size already in cart
        var duplicate = false;
        $('#cartTable tr').each(function() {
          var existingName = $(this).find('td:eq(1)').text().trim();
          var existingVariant = $(this).find('td:eq(2)').text().trim();
          var currentVariant = (color || '—') + (color && size ? ' / ' : '') + (size || '—');
          // If same product exists in cart with same variant, flag as duplicate
          if (existingVariant === currentVariant || (!color && !size && existingName)) {
            // For non-variant products just check if same product exists
            if (!color && !size && existingName) {
              duplicate = true;
            } else if (existingVariant === currentVariant) {
              duplicate = true;
            }
          }
        });

        if (duplicate) {
          alert('This product ' + (color || size ? 'with same color/size ' : '') + 'is already in the cart. Please adjust quantity instead.');
          resetProductSelect();
          return;
        }

        $.ajax({
          cache: false, type:"GET",
          data: { id: productId, product_color: color, product_size: size },
          url: "{{route('admin.order.cart_add')}}",
          dataType:"json",
          success: function() { refreshCart(); resetProductSelect(); },
          error: function() { refreshCart(); resetProductSelect(); }
        });
      }
      function resetProductSelect() { $('#cart_add').val('').trigger('change'); }
      $('#variantModal').on('hidden.bs.modal', function () { resetProductSelect(); });

      // ---- Cart controls ----
      $(document).on('click', '.cart_increment', function(e) {
        e.preventDefault();
        var id = $(this).data('id'), qty = $(this).closest('tr').find('input').val();
        $.ajax({ cache:false, data:{id:id, qty:qty}, type:"GET", url:"{{route('admin.order.cart_increment')}}", dataType:"json", success: function(){ refreshCart(); } });
      });
      $(document).on('click', '.cart_decrement', function(e) {
        e.preventDefault();
        var id = $(this).data('id'), qty = $(this).closest('tr').find('input').val();
        $.ajax({ cache:false, data:{id:id, qty:qty}, type:"GET", url:"{{route('admin.order.cart_decrement')}}", dataType:"json", success: function(){ refreshCart(); } });
      });
      $(document).on('click', '.cart_remove', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({ cache:false, data:{id:id}, type:"GET", url:"{{route('admin.order.cart_remove')}}", dataType:"json", success: function(){ refreshCart(); } });
      });
      $(document).on('change', '.product_discount', function() {
        var id = $(this).data('id'), discount = $(this).val();
        $.ajax({ cache:false, data:{id:id, discount:discount}, type:"GET", url:"{{route('admin.order.product_discount')}}", dataType:"json", success: function(){ refreshCart(); } });
      });
      $(".cartclear").click(function(e) {
        $.ajax({ cache:false, type:"GET", url:"{{route('admin.order.cart_clear')}}", dataType:"json", success: function(){ refreshCart(); } });
      });
      $("#area").on("change", function () {
        var id = $(this).val();
        $.ajax({ type:"GET", data:{ id: id }, url:"{{route('admin.order.cart_shipping')}}", dataType:"html", success: function(){ refreshCart(); } });
      });
</script>
@endsection

<!-- Variant Modal -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
 <div class="modal-dialog modal-lg modal-dialog-centered">
  <div class="modal-content">
   <div class="modal-header bg-primary text-white py-2">
    <h5 class="modal-title"><i class="fe-layers"></i> <span id="variantModalTitle">Select Variants</span></h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
   </div>
   <div class="modal-body">
    <input type="hidden" id="variantProductId" value="" />
    <div class="row">
     <div class="col-md-4 text-center">
      <img id="variantProductImage" src="" class="img-fluid mb-2" style="max-height:160px;object-fit:contain;border-radius:8px;" alt="" />
      <h6 id="variantProductName" class="fw-bold mb-1"></h6>
      <p class="mb-0"><strong>Price:</strong> ৳<span id="variantBasePrice"></span></p>
     </div>
     <div class="col-md-8">
      <div id="variantColorSection" class="p-3 mb-2 rounded" style="background:#f8f9fa;display:none;">
       <h6 class="fw-bold" style="font-size:13px;"><i class="fe-droplet"></i> Select Color</h6>
       <div id="variantColors" class="d-flex flex-wrap gap-2"></div>
      </div>
      <div id="variantSizeSection" class="p-3 mb-2 rounded" style="background:#f8f9fa;display:none;">
       <h6 class="fw-bold" style="font-size:13px;"><i class="fe-maximize"></i> Select Size</h6>
       <div id="variantSizes" class="d-flex flex-wrap gap-2"></div>
      </div>
      <div class="p-3 rounded" style="background:#f8f9fa;">
       <h6 class="fw-bold" style="font-size:13px;"><i class="fe-plus-circle"></i> Quantity</h6>
       <div class="input-group" style="max-width:140px;">
        <button class="btn btn-outline-secondary" type="button" id="variantQtyMinus">−</button>
        <input type="number" class="form-control text-center fw-bold" id="variantQty" value="1" min="1" max="999" />
        <button class="btn btn-outline-secondary" type="button" id="variantQtyPlus">+</button>
       </div>
      </div>
      <div class="d-flex justify-content-between align-items-center p-3 mt-2 rounded" style="background:#e8f0fe;">
       <span class="fw-bold">Item Total:</span>
       <span class="h5 mb-0 text-primary fw-bold">৳ <span id="variantTotalPrice">0</span></span>
      </div>
     </div>
    </div>
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary" id="variantAddToCartBtn"><i class="fe-plus"></i> Add to Order</button>
   </div>
  </div>
 </div>
</div>
