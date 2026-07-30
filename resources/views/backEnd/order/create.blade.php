@extends('backEnd.layouts.master') @section('title','Order Create') @section('css')
<style>
 .increment_btn,
 .remove_btn {
  margin-top: -17px;
  margin-bottom: 10px;
 }
 .variant-btn { border-radius: 6px; cursor: pointer; transition: all .15s ease; }
 .variant-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,.12); }
 .variant-btn.active { border-color: #0056b3 !important; background: #e8f0fe !important; box-shadow: 0 0 0 2px rgba(0,86,179,.25); }
 .color-swatch { display: inline-block; width: 28px; height: 28px; border-radius: 50%; border: 2px solid #dee2e6; margin-right: 6px; vertical-align: middle; }
 .modal-product-img { max-height: 160px; object-fit: contain; border-radius: 8px; }
 .variant-section { background: #f8f9fa; border-radius: 8px; padding: 14px; margin-bottom: 12px; }
 .variant-section h6 { font-size: 13px; font-weight: 600; color: #495057; margin-bottom: 8px; }
 .qty-input-group { max-width: 140px; }
 .qty-input-group .form-control { text-align: center; font-weight: 600; }
 .badge-variant { font-size: 11px; padding: 2px 8px; margin-left: 4px; }
</style>
<link href="{{asset('backEnd/assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/summernote/summernote-lite.min.css')}}" rel="stylesheet" type="text/css" />
@endsection @section('content')

<div class="container-fluid">
 <div class="row">
  <div class="col-12">
   <div class="page-title-box">
    <div class="page-title-right">
     <form method="get" action="{{route('admin.order.cart_clear')}}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-danger rounded-pill delete-confirm"><i class="fas fa-trash-alt"></i> Cart Clear</button>
     </form>
    </div>
    <h4 class="page-title">Order Create</h4>
   </div>
  </div>
 </div>
 <div class="row justify-content-center">
  <div class="col-lg-12">
   <div class="card">
    <div class="card-body">
     <form action="{{route('admin.order.store')}}" method="POST" class="row pos_form" data-parsley-validate="" enctype="multipart/form-data">
      @csrf
      <div class="col-sm-12">
       <div class="form-group mb-3">
        <label for="cart_add" class="form-label">Select Product *</label>
        <div class="input-group">
         <select id="cart_add" class="form-control select2 @error('product_id') is-invalid @enderror">
          <option value="">Search & select a product...</option>
          @foreach($products as $value)
          <option value="{{$value->id}}">{{$value->name}} ({{$value->product_code}})</option>
          @endforeach
         </select>
        </div>
       </div>
      </div>
      <!-- Cart Table -->
      <div class="col-sm-12">
       <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0" id="orderTable">
         <thead class="table-light">
          <tr>
           <th style="width:8%;">Image</th>
           <th style="width:22%;">Product</th>
           <th style="width:13%;">Variant</th>
           <th style="width:12%;">Quantity</th>
           <th style="width:12%;">Price</th>
           <th style="width:12%;">Discount</th>
           <th style="width:13%;">Subtotal</th>
           <th style="width:8%;">Action</th>
          </tr>
         </thead>
         <tbody id="cartTable">
          @php $product_discount = 0; @endphp @foreach($cartinfo as $key=>$value)
          <tr>
           <td><img height="35" src="{{asset($value->options->image)}}" style="border-radius:4px;object-fit:cover;width:45px;" /></td>
           <td class="fw-semibold">{{$value->name}}</td>
           <td>
            @if($value->options->product_color || $value->options->product_size)
             <span class="badge bg-info badge-variant">{{$value->options->product_color ?: ''}}{{ $value->options->product_color && $value->options->product_size ? ' / ' : '' }}{{$value->options->product_size ?: ''}}</span>
            @else
             <span class="text-muted">—</span>
            @endif
           </td>
           <td>
            <div class="qty-cart vcart-qty">
             <div class="quantity input-group input-group-sm" style="max-width:110px;">
              <button class="btn btn-outline-secondary cart_decrement" data-id="{{$value->rowId}}">-</button>
              <input type="text" class="form-control text-center" value="{{$value->qty}}" readonly />
              <button class="btn btn-outline-secondary cart_increment" data-id="{{$value->rowId}}">+</button>
             </div>
            </div>
           </td>
           <td>৳{{$value->price}}</td>
           <td><input type="number" class="form-control form-control-sm product_discount" style="width:70px;" value="{{$value->options->product_discount}}" placeholder="0" data-id="{{$value->rowId}}" /></td>
           <td class="fw-semibold">৳{{($value->price - $value->options->product_discount)*$value->qty}}</td>
           <td><button type="button" class="btn btn-danger btn-sm cart_remove" data-id="{{$value->rowId}}"><i class="fa fa-times"></i></button></td>
          </tr>
          @php $product_discount += $value->options->product_discount*$value->qty; Session::put('product_discount',$product_discount); @endphp @endforeach
         </tbody>
        </table>
       </div>
      </div>
      <!-- Customer & Shipping -->
      <div class="col-sm-6 mt-3">
       <div class="card bg-light border-0">
        <div class="card-body py-3">
         <h6 class="fw-bold mb-3"><i class="fe-user"></i> Customer Information</h6>
         <div class="row g-2">
          <div class="col-sm-12">
           <label class="form-label text-muted small mb-1">Select Existing Customer</label>
           <select id="customer_select" class="form-control form-control-sm select2">
            <option value="">— Walk-in / New Customer —</option>
            @foreach($customers as $customer)
            <option value="{{$customer->id}}" data-phone="{{$customer->phone}}" data-address="{{$customer->address}}" data-area="{{$customer->area}}">
              {{$customer->name}} ({{$customer->phone}})
            </option>
            @endforeach
           </select>
          </div>
          <div class="col-sm-12"><hr class="my-1"></div>
          <div class="col-sm-12">
           <label class="form-label text-muted small mb-1">Name <span class="text-danger">*</span></label>
           <input type="text" id="name" class="form-control form-control-sm" placeholder="Customer Name" name="name" value="" required />
          </div>
          <div class="col-sm-12">
           <label class="form-label text-muted small mb-1">Phone <span class="text-danger">*</span></label>
           <input type="number" id="phone" class="form-control form-control-sm" placeholder="Phone Number" name="phone" value="" required />
          </div>
          <div class="col-sm-12">
           <label class="form-label text-muted small mb-1">Address <span class="text-danger">*</span></label>
           <input type="text" placeholder="Delivery Address" id="address" class="form-control form-control-sm" name="address" value="" required />
          </div>
          <div class="col-sm-12">
           <label class="form-label text-muted small mb-1">Delivery Area <span class="text-danger">*</span></label>
           <select id="area" class="form-control form-control-sm" name="area" required>
            <option value="">Select Delivery Area...</option>
            @foreach($shippingcharge as $sc)
            <option value="{{$sc->id}}">{{$sc->name}}</option>
            @endforeach
           </select>
          </div>
          <div class="col-sm-6">
           <label class="form-label text-muted small mb-1">Order Date</label>
           <input type="date" name="order_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
          </div>
          <div class="col-sm-6">
           <label class="form-label text-muted small mb-1">Delivery Date</label>
           <input type="date" name="delivery_date" class="form-control form-control-sm">
          </div>
         </div>
        </div>
       </div>
      </div>
      <!-- Order Summary -->
      <div class="col-sm-6 mt-3">
       <div class="card bg-light border-0">
        <div class="card-body py-3">
         <h6 class="fw-bold mb-3"><i class="fe-shopping-cart"></i> Order Summary</h6>
         <table class="table table-sm table-borderless mb-0">
          <tbody id="cart_details">
           @php $subtotal = Cart::instance('pos_shopping')->subtotal(); $subtotal = str_replace(',','',$subtotal); $subtotal = str_replace('.00', '',$subtotal); $shipping = Session::get('pos_shipping'); $total_discount = Session::get('pos_discount')+Session::get('product_discount'); @endphp
           <tr><td>Subtotal</td><td class="text-end">৳{{$subtotal}}</td></tr>
           <tr><td>Shipping</td><td class="text-end">৳{{$shipping ?? '0'}}</td></tr>
           <tr><td>Discount</td><td class="text-end">-৳{{$total_discount}}</td></tr>
           <tr class="fw-bold"><td>Total</td><td class="text-end">৳{{($subtotal + ($shipping ?? 0)) - $total_discount}}</td></tr>
          </tbody>
         </table>
        </div>
       </div>
      </div>
      <div class="col-sm-12 mt-3 text-end">
       <button type="submit" class="btn btn-success btn-lg px-4"><i class="fe-check-circle"></i> Place Order</button>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</div>

<!-- ========== Variant Selection Modal ========== -->
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
      <img id="variantProductImage" src="" class="modal-product-img img-fluid mb-2" alt="" />
      <h6 id="variantProductName" class="fw-bold mb-1"></h6>
      <p class="mb-0"><strong>Price:</strong> ৳<span id="variantBasePrice"></span></p>
     </div>
     <div class="col-md-8">
      <!-- Color Selection -->
      <div id="variantColorSection" class="variant-section" style="display:none;">
       <h6><i class="fe-droplet"></i> Select Color</h6>
       <div id="variantColors" class="d-flex flex-wrap gap-2"></div>
      </div>
      <!-- Size Selection -->
      <div id="variantSizeSection" class="variant-section" style="display:none;">
       <h6><i class="fe-maximize"></i> Select Size</h6>
       <div id="variantSizes" class="d-flex flex-wrap gap-2"></div>
      </div>
      <!-- Quantity -->
      <div class="variant-section">
       <h6><i class="fe-plus-circle"></i> Quantity</h6>
       <div class="input-group qty-input-group">
        <button class="btn btn-outline-secondary" type="button" id="variantQtyMinus">−</button>
        <input type="number" class="form-control" id="variantQty" value="1" min="1" max="999" />
        <button class="btn btn-outline-secondary" type="button" id="variantQtyPlus">+</button>
       </div>
      </div>
      <!-- Price Preview -->
      <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
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
@endsection

@section('script')
<script src="{{asset('backEnd/assets/libs/parsleyjs/parsley.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-validation.init.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/summernote/summernote-lite.min.js')}}"></script>
<script>
$(document).ready(function () {
 $(".select2").select2();
 $(".summernote").summernote({ placeholder: "Enter Your Text Here" });

 // ---- Customer select auto-fill ----
 $('#customer_select').on('change', function() {
  var $opt = $(this).find('option:selected');
  var phone = $opt.data('phone');
  var address = $opt.data('address');
  var area = $opt.data('area');
  var name = $opt.text().replace(/\(.*\)/, '').trim();

  if ($(this).val()) {
   $('#name').val(name);
   $('#phone').val(phone);
   $('#address').val(address);
   if (area) $('#area').val(area);
  } else {
   // Clear fields for new customer
   $('#name').val('');
   $('#phone').val('');
   $('#address').val('');
   $('#area').val('');
  }
 });

 // ---- Shared helpers ----
 function cart_content() {
  $.ajax({ type:"GET", url:"{{route('admin.order.cart_content')}}", dataType:"html",
   success: function(r){ $("#cartTable").html(r); }
  });
 }
 function cart_details() {
  $.ajax({ type:"GET", url:"{{route('admin.order.cart_details')}}", dataType:"html",
   success: function(r){ $("#cart_details").html(r); }
  });
 }
 function refreshCart() { cart_content(); cart_details(); }

 // ---- Product select → show variant modal or add directly ----
 let selectedProductData = null;

 $('#cart_add').on('change', function() {
  var id = $(this).val();
  if (!id) return;
  var $btn = $(this);
  $btn.prop('disabled', true);

  $.ajax({
   cache: false, type:"GET", data:{ id: id },
   url: "{{route('admin.order.product_variants')}}",
   dataType:"json",
   success: function(p) {
    selectedProductData = p;
    var hasColor = p.colors && p.colors.length > 0;
    var hasSize  = p.sizes && p.sizes.length > 0;

    if (hasColor || hasSize) {
     // Show modal for variant selection
     $('#variantProductId').val(p.id);
     $('#variantModalTitle').text(p.name);
     $('#variantProductName').text(p.name);
     $('#variantProductImage').attr('src', p.image || '{{asset("backEnd/assets/images/default-product.png")}}');
     $('#variantBasePrice').text(p.price);

     // Colors
     if (hasColor) {
      $('#variantColorSection').show();
      var cHtml = '';
      $.each(p.colors, function(i, c) {
       var swatch = c.code ? '<span class="color-swatch" style="background:'+c.code+';"></span>' : '';
       cHtml += '<button type="button" class="btn btn-outline-dark btn-sm variant-btn color-btn" data-id="'+c.id+'" data-name="'+c.name+'" data-price="'+(c.price||'')+'" data-stock="'+(c.stock||'')+'">'
             + swatch + c.name + (c.price ? ' <small class="text-muted">(৳'+c.price+')</small>' : '')
             + '</button> ';
      });
      $('#variantColors').html(cHtml);
     } else {
      $('#variantColorSection').hide();
     }

     // Sizes
     if (hasSize) {
      $('#variantSizeSection').show();
      var sHtml = '';
      $.each(p.sizes, function(i, s) {
       sHtml += '<button type="button" class="btn btn-outline-dark btn-sm variant-btn size-btn" data-id="'+s.id+'" data-name="'+s.name+'" data-price="'+(s.price||'')+'" data-stock="'+(s.stock||'')+'">'
             + s.name + (s.price ? ' <small class="text-muted">(৳'+s.price+')</small>' : '')
             + '</button> ';
      });
      $('#variantSizes').html(sHtml);
     } else {
      $('#variantSizeSection').hide();
     }

     // Reset selections and qty
     $('.variant-btn').removeClass('active');
     $('#variantQty').val(1);
     updateVariantTotal();

     $('#variantModal').modal('show');
    } else {
     // No variants → add directly
     addToCart(id, '', '');
    }
   },
   complete: function() { $btn.prop('disabled', false); }
  });
 });

 // ---- Variant selection toggle ----
 $(document).on('click', '.color-btn', function() {
  $('.color-btn').removeClass('active');
  $(this).addClass('active');
  updateVariantTotal();
 });
 $(document).on('click', '.size-btn', function() {
  $('.size-btn').removeClass('active');
  $(this).addClass('active');
  updateVariantTotal();
 });

 // ---- Quantity controls ----
 $('#variantQtyMinus').click(function() {
  var q = parseInt($('#variantQty').val()) || 1;
  if (q > 1) { $('#variantQty').val(q - 1); updateVariantTotal(); }
 });
 $('#variantQtyPlus').click(function() {
  var q = parseInt($('#variantQty').val()) || 1;
  if (q < 999) { $('#variantQty').val(q + 1); updateVariantTotal(); }
 });
 $('#variantQty').on('input', function() {
  var v = parseInt($(this).val()) || 1;
  if (v < 1) v = 1; if (v > 999) v = 999;
  $(this).val(v);
  updateVariantTotal();
 });

 function updateVariantTotal() {
  if (!selectedProductData) return;
  var base = parseFloat(selectedProductData.price) || 0;
  var colorExtra = parseFloat($('.color-btn.active').data('price')) || 0;
  var sizeExtra  = parseFloat($('.size-btn.active').data('price')) || 0;

  var unitPrice = base;
  if (sizeExtra > 0) {
   unitPrice = sizeExtra;
  } else if (colorExtra > 0) {
   unitPrice = colorExtra;
  }
  if (unitPrice <= 0) unitPrice = base;

  var qty = parseInt($('#variantQty').val()) || 1;
  var total = unitPrice * qty;

  $('#variantTotalPrice').text(total.toFixed(2));
  $('#variantBasePrice').text(unitPrice.toFixed(2));
 }

 // ---- Add variant to cart ----
 $('#variantAddToCartBtn').click(function() {
  var productId = $('#variantProductId').val();
  var colorName = $('.color-btn.active').data('name') || '';
  var sizeName  = $('.size-btn.active').data('name') || '';
  var qty       = parseInt($('#variantQty').val()) || 1;
  addToCart(productId, colorName, sizeName, qty);
  $('#variantModal').modal('hide');
 });

 function addToCart(productId, color, size, qty) {
  qty = qty || 1;
  // Check for duplicate variant in cart
  var duplicate = false;
  var currentVariant = (color || '—') + (color && size ? ' / ' : '') + (size || '—');
  $('#cartTable tr').each(function() {
   var existingVariant = $(this).find('td:eq(2)').text().trim();
   if (!color && !size) {
    // Non-variant: just check if product already exists
    if ($(this).find('td:eq(1)').text().trim()) duplicate = true;
   } else if (existingVariant === currentVariant) {
    duplicate = true;
   }
  });
  if (duplicate) {
   alert('This product ' + (color||size ? 'with same color/size ' : '') + 'is already in the cart.');
   resetProductSelect();
   return;
  }
  $.ajax({
   cache: false, type:"GET",
   data: { id: productId, product_color: color, product_size: size, qty: qty },
   url: "{{route('admin.order.cart_add')}}",
   dataType:"json",
   success: function() {
    refreshCart();
    resetProductSelect();
   },
   error: function() {
    refreshCart();
    resetProductSelect();
   }
  });
 }
 function resetProductSelect() {
  $('#cart_add').val('').trigger('change');
 }

 // Reset product select when modal is closed/cancelled
 $('#variantModal').on('hidden.bs.modal', function () {
  resetProductSelect();
 });

 // ---- Existing cart controls (delegated) ----
 $(document).on('click', '.cart_increment', function(e) {
  e.preventDefault();
  var id = $(this).data('id'), qty = $(this).closest('tr').find('input').val();
  $.ajax({ cache:false, data:{id:id, qty:qty}, type:"GET", url:"{{route('admin.order.cart_increment')}}", dataType:"json",
   success: function(){ refreshCart(); }
  });
 });
 $(document).on('click', '.cart_decrement', function(e) {
  e.preventDefault();
  var id = $(this).data('id'), qty = $(this).closest('tr').find('input').val();
  $.ajax({ cache:false, data:{id:id, qty:qty}, type:"GET", url:"{{route('admin.order.cart_decrement')}}", dataType:"json",
   success: function(){ refreshCart(); }
  });
 });
 $(document).on('click', '.cart_remove', function(e) {
  e.preventDefault();
  var id = $(this).data('id');
  $.ajax({ cache:false, data:{id:id}, type:"GET", url:"{{route('admin.order.cart_remove')}}", dataType:"json",
   success: function(){ refreshCart(); }
  });
 });
 $(document).on('change', '.product_discount', function() {
  var id = $(this).data('id'), discount = $(this).val();
  $.ajax({ cache:false, data:{id:id, discount:discount}, type:"GET", url:"{{route('admin.order.product_discount')}}", dataType:"json",
   success: function(){ refreshCart(); }
  });
 });
 $("#area").on("change", function () {
  var id = $(this).val();
  $.ajax({ type:"GET", data:{ id: id }, url:"{{route('admin.order.cart_shipping')}}", dataType:"html",
   success: function(){ refreshCart(); }
  });
 });
 $(".cartclear").click(function(e) {
  $.ajax({ cache:false, type:"GET", url:"{{route('admin.order.cart_clear')}}", dataType:"json",
   success: function(){ refreshCart(); }
  });
 });
});
</script>
@endsection
