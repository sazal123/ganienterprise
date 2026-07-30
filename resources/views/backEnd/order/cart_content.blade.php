@php $product_discount = 0; @endphp
@foreach($cartinfo as $key=>$value)
<tr>
  <td><img height="35" src="{{asset($value->options->image)}}" style="border-radius:4px;object-fit:cover;width:45px;" /></td>
  <td class="fw-semibold">{{$value->name}}</td>
  <td>
    @if($value->options->product_color || $value->options->product_size)
      <span class="badge bg-info" style="font-size:11px;">
        {{ $value->options->product_color ?: '' }}{{ $value->options->product_color && $value->options->product_size ? ' / ' : '' }}{{ $value->options->product_size ?: '' }}
      </span>
    @else
      <span class="text-muted">—</span>
    @endif
  </td>
  <td>
    <div class="input-group input-group-sm" style="max-width:110px;">
      <button class="btn btn-outline-secondary cart_decrement" data-id="{{$value->rowId}}">-</button>
      <input type="text" class="form-control text-center" value="{{$value->qty}}" readonly />
      <button class="btn btn-outline-secondary cart_increment" data-id="{{$value->rowId}}">+</button>
    </div>
  </td>
  <td>৳{{$value->price}}</td>
  <td><input type="number" class="form-control form-control-sm product_discount" style="width:65px !important;" value="{{$value->options->product_discount}}" placeholder="0" data-id="{{$value->rowId}}" /></td>
  <td class="fw-semibold">৳{{($value->price - $value->options->product_discount)*$value->qty}}</td>
  <td><button type="button" class="btn btn-danger btn-sm cart_remove" data-id="{{$value->rowId}}"><i class="fa fa-times"></i></button></td>
</tr>
@php
  $product_discount += $value->options->product_discount*$value->qty;
  Session::put('product_discount',$product_discount);
@endphp
@endforeach
