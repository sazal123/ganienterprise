@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal=str_replace(',','',$subtotal);
    $subtotal=str_replace('.00', '',$subtotal);
    $shipping = Session::get('shipping')?Session::get('shipping'):0;
    $discount = Session::get('discount')?Session::get('discount'):0;
@endphp
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
         @foreach(Cart::instance('shopping')->content() as $value)
         <tr>
          <td>
           <a class="cart_remove" data-id="{{$value->rowId}}"><i class="fas fa-trash text-danger"></i></a>
          </td>
          <td class="text-left">
           <div class="d-flex align-items-center justify-content-between gap-2">
            <div>
             <a href="{{route('product',$value->options->slug)}}" class="fw-semibold text-dark text-decoration-none">{{Str::limit($value->name,25)}}</a>
            </div>
            <div class="flex-shrink-0">
             <a href="{{route('product',$value->options->slug)}}">
              <img src="{{asset($value->options->image)}}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #eee;" alt="" />
             </a>
            </div>
           </div>
            @php
                $product = App\Models\Product::find($value->id);
            @endphp
         
           @if($product && ($product->sizes->isNotEmpty() || $product->colors->isNotEmpty()))
            <div class="row g-1 mt-2">
                <!-- Size Selector -->
                @if($product->sizes->isNotEmpty())
                <div class="col-6">
                    <select id="size-selector-{{ $value->rowId }}" class="form-select form-select-sm cart-size-selector" data-id="{{ $value->rowId }}">
                       <option>Select Size</option>
                        @foreach($product->sizes as $size)
                        <option value="{{ $size->sizeName }}" {{ $size->sizeName == $value->options->product_size ? 'selected' : '' }}>
                            {{ $size->sizeName }}
                        </option>
                        @endforeach
                    </select>
                    <label for="size-selector-{{ $value->rowId }}" class="form-label text-muted text-start" style="font-size: 0.875rem;">Size:
                    @if($value->options->product_size)
                      {{$value->options->product_size}}
                    @endif
                    </label>
                </div>
                @endif
            
                <!-- Color Selector -->
                @if($product->colors->isNotEmpty())
                <div class="col-6">
                    <select id="color-selector-{{ $value->rowId }}" class="form-select form-select-sm cart-color-selector" data-id="{{ $value->rowId }}">
                       <option>Select Color</option>
                        @foreach($product->colors as $color)
                        <option value="{{ $color->colorName }}" {{ $color->colorName == $value->options->product_color ? 'selected' : '' }}>
                            {{ $color->colorName }}
                        </option>
                        @endforeach
                    </select>
                    <label for="color-selector-{{ $value->rowId }}" class="form-label text-muted text-start" style="font-size: 0.875rem;">Color:
                    @if($value->options->product_color)
                       {{ $value->options->product_color }}
                    @endif
                    </label>
                </div>
                @endif
            </div>
            @endif
          </td>
          <td class="cart_qty">
           <div class="qty-cart vcart-qty">
            <div class="quantity">
             <button class="minus cart_decrement" data-id="{{$value->rowId}}">-</button>
             <input type="text" value="{{$value->qty}}" readonly />
             <button class="plus cart_increment" data-id="{{$value->rowId}}">+</button>
            </div>
           </div>
          </td>
          <td><span class="alinur">৳ </span><strong>{{$value->price}}</strong></td>
         </tr>
         @endforeach
        </tbody>
        <tfoot>
         <tr>
          <th colspan="3" class="text-end px-4">Subtotal</th>
          <td>
           <span id="net_total"><span class="alinur">৳ </span><strong>{{$subtotal}}</strong></span>
          </td>
         </tr>
         <tr>
          <th colspan="3" class="text-end px-4">Shipping Charge</th>
          <td>
           <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong>{{$shipping}}</strong></span>
          </td>
         </tr>
         <tr>
          <th colspan="3" class="text-end px-4">Grand Total</th>
          <td>
           <span id="grand_total"><span class="alinur">৳ </span><strong>{{$subtotal+$shipping}}</strong></span>
          </td>
         </tr>
        </tfoot>
       </table>

<script src="{{asset('frontEnd/js/jquery-3.6.3.min.js')}}"></script>
<!-- cart js start -->
<script>
    $('.cart_store').on('click',function(){
    var id = $(this).data('id'); 
    var qty = $(this).parent().find('input').val();
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id,'qty':qty?qty:1},
           url:"{{route('cart.store')}}",
           success:function(data){               
            if(data){
                return cart_count();
            }
           }
        });
     }  
   });

    $('.cart_remove').on('click',function(){
    var id = $(this).data('id');   
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.remove')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                return cart_count();
            }
           }
        });
     }  
   });

    $('.cart_increment').on('click',function(){
    var id = $(this).data('id');  
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.increment')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                return cart_count();
            }
           }
        });
     }  
   });

    $('.cart_decrement').on('click',function(){
    var id = $(this).data('id');  
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.decrement')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                return cart_count();
            }
           }
        });
     }  
   });

$('.cart-size-selector').on('change', function() {
    var rowId = $(this).data('id');
    var selectedSize = $(this).val();

    if (rowId) {
        $.ajax({
            type: "GET",
            data: {
                'id': rowId,
                'product_size': selectedSize
            },
            url: "{{ route('cart.update') }}",
            success: function(data) {
                if (data) {
                    $(".cartlist").html(data);
                    return cart_count();
                }
            },
            error: function() {
                alert('An error occurred while updating the size. Please try again.');
            }
        });
    }
});

$('.cart-color-selector').on('change', function() {
    var rowId = $(this).data('id');
    var selectedColor = $(this).val();

    if (rowId) {
        $.ajax({
            type: "GET",
            data: {
                'id': rowId,
                'product_color': selectedColor
            },
            url: "{{ route('cart.update') }}",
            success: function(data) {
                if (data) {
                    $(".cartlist").html(data);
                    return cart_count();
                }
            },
            error: function() {
                alert('An error occurred while updating the color. Please try again.');
            }
        });
    }
});

    function cart_count(){
        $.ajax({
           type:"GET",
           url:"{{route('cart.count')}}",
           success:function(data){               
            if(data){
                $("#cart-qty").html(data);
            }else{
               $("#cart-qty").empty();
            }
           }
        }); 
   };
</script>