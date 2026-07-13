@extends('backEnd.layouts.master')
@section('title','Order Process')
@section('css')
<style>
    .increment_btn,.remove_btn {
    margin-top: -17px;
    margin-bottom: 10px;
}
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
                <h4 class="page-title">Order Process [Invoice : #{{$data->invoice_id}}]</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
   <div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Image</th>
                            <th>Product</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->orderdetails as $key=>$product)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td><img src="{{asset($product->image?$product->image->image:'')}}" height="50" width="50" alt=""></td>
                            <td>{{$product->product_name}}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
               <form action="{{route('admin.order_change')}}" method="POST" class=row data-parsley-validate="" name="editForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$data->id}}">

                            <div class="col-sm-6">
                              <div class="form-group mb-3">
                                   <label for="name" class="form-label">Customer name </label>
                                   <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$data->shipping?$data->shipping->name:''}}" placeholder="Name">
                                     @error('name')
                                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                      </span>
                                     @enderror
                              </div>
                            </div>

                             <div class="col-sm-6">
                              <div class="form-group mb-3">
                                   <label for="phone" class="form-label">Customer Phone </label>
                                   <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{$data->shipping?$data->shipping->phone:''}}" placeholder="Phone Number">
                                     @error('phone')
                                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                      </span>
                                     @enderror
                              </div>
                            </div>
                             <div class="col-sm-12">
                              <div class="form-group mb-3">
                                   <label for="address" class="form-label">Customer Address </label>
                                   <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{$data->shipping?$data->shipping->address:''}}</textarea>
                                    @error('address')
                                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                      </span>
                                     @enderror
                              </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="area">Delivery Area *</label>
                                    <select type="area" id="area" class="form-control @error('area') is-invalid @enderror" name="area"   required>
                                        @foreach($shippingcharge as $key=>$value)
                                        <option @if($data->shipping?$data->shipping->area:'' == $value->name) selected @endif value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                <label for="category_id" class="form-label">Order Status</label>
                                 <select class="form-control select2-multiple @error('status') is-invalid @enderror" value="{{ old('status') }}" name="status" data-toggle="select2"  data-placeholder="Choose ..." required>
                                    <optgroup >
                                        <option value="">Select..</option>
                                        @foreach($orderstatus as $value)
                                        <option value="{{$value->id}}"  @if($data->order_status==$value->id) selected @endif>{{$value->name}}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col end -->

                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>
                </form>

                <!-- Payment History Card -->
                <div class="card mt-3 border">
                 <div class="card-header bg-light py-2">
                  <h6 class="mb-0"><i class="fe-dollar-sign"></i> Payment History</h6>
                 </div>
                 <div class="card-body p-2">
                  <div class="table-responsive">
                   <table class="table table-sm table-bordered mb-0" style="font-size:12px;">
                    <thead class="table-secondary">
                     <tr>
                      <th>Date</th><th>Method</th><th>TrxID</th><th class="text-end">Amount</th><th>Received By</th><th>Action</th>
                     </tr>
                    </thead>
                    <tbody>
                     @forelse($data->paymentHistories as $ph)
                     <tr>
                      <td>{{ $ph->payment_date->format('d/m/y') }}</td>
                      <td>{{ $ph->payment_method }}</td>
                      <td>{{ $ph->trx_id ?? '—' }}</td>
                      <td class="text-end fw-bold">৳{{ number_format($ph->amount, 0) }}</td>
                      <td>{{ $ph->received_by ?? '—' }}</td>
                      <td>
                       <form method="post" action="{{ route('admin.order.payment_delete') }}" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                        @csrf
                        <input type="hidden" name="id" value="{{ $ph->id }}">
                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                       </form>
                      </td>
                     </tr>
                     @empty
                     <tr><td colspan="6" class="text-center text-muted">No payments yet</td></tr>
                     @endforelse
                    </tbody>
                    <tfoot class="fw-bold">
                     <tr>
                      <td colspan="3" class="text-end">Total Paid</td>
                      <td class="text-end">৳{{ number_format($totalPaid ?? 0, 0) }}</td>
                      <td colspan="2"></td>
                     </tr>
                     <tr>
                      <td colspan="3" class="text-end text-danger">Due</td>
                      <td class="text-end text-danger">৳{{ number_format($dueAmount ?? 0, 0) }}</td>
                      <td colspan="2"></td>
                     </tr>
                    </tfoot>
                   </table>
                  </div>
                  <div class="text-end mt-2">
                   <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal"><i class="fe-plus"></i> Record Payment</button>
                  </div>
                 </div>
                </div>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>

<!-- Payment Modal (same as invoice) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
 <div class="modal-dialog modal-sm modal-dialog-centered">
  <form method="post" action="{{ route('admin.order.payment_store') }}" class="modal-content">
   @csrf
   <input type="hidden" name="order_id" value="{{ $data->id }}">
   <div class="modal-header bg-primary text-white py-2">
    <h5 class="modal-title"><i class="fe-dollar-sign"></i> Record Payment</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
   </div>
   <div class="modal-body">
    <div class="mb-2">
     <label class="form-label small">Amount (Due: ৳{{ number_format($dueAmount ?? 0, 0) }})</label>
     <input type="number" step="0.01" name="amount" class="form-control form-control-sm" required max="{{ $dueAmount ?? 0 }}">
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
     <input type="text" name="note" class="form-control form-control-sm">
    </div>
   </div>
   <div class="modal-footer py-2">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fe-check"></i> Save Payment</button>
   </div>
  </form>
 </div>
</div>
@endsection


@section('script')
<script src="{{asset('backEnd/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/assets/js/pages/form-advanced.init.js"></script>
<!-- Plugins js -->
<script src="{{asset('backEnd/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
  $(".summernote").summernote({
    placeholder: "Enter Your Text Here",

  });
</script>
@endsection
