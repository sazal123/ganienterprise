@extends('backEnd.layouts.master')
@section('title','Edit Offer')

@section('css')
<link href="{{asset('backEnd/assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('offer.index')}}" class="btn btn-primary rounded-pill"><i class="fe-list me-1"></i> Manage Offers</a>
                </div>
                <h4 class="page-title">Edit Offer Campaign</h4>
            </div>
        </div>
    </div>       

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('offer.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="hidden_id" value="{{$edit_data->id}}">
                        
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Offer Title *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $edit_data->title) }}" id="title" required="">
                                @error('title')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="discount_tag" class="form-label">Discount Tag / Badge</label>
                                <input type="text" class="form-control" name="discount_tag" value="{{ old('discount_tag', $edit_data->discount_tag) }}" id="discount_tag">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="subtitle" class="form-label">Subtitle / Highlight Text</label>
                                <input type="text" class="form-control" name="subtitle" value="{{ old('subtitle', $edit_data->subtitle) }}" id="subtitle">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="end_date" class="form-label">Offer End Date & Time (for Live Countdown Timer)</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date', $edit_data->end_date ? \Carbon\Carbon::parse($edit_data->end_date)->format('Y-m-d\TH:i') : '') }}" id="end_date">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="banner" class="form-label">Banner Image (Leave blank to keep existing)</label>
                                <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" id="banner">
                                @if($edit_data->banner)
                                    <div class="mt-2">
                                        <img src="{{asset($edit_data->banner)}}" style="height: 50px; width: auto; border-radius: 4px;" alt="">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="product_ids" class="form-label">Select Offer Products</label>
                                <select class="form-control select2-multiple @error('product_ids') is-invalid @enderror" name="product_ids[]" id="product_ids" multiple="multiple" data-placeholder="Choose products to include in this offer...">
                                    @foreach($products as $product)
                                        <option value="{{$product->id}}" @if(in_array($product->id, $select_product_ids)) selected @endif>{{$product->name}} (Code: {{$product->product_code ?? 'N/A'}} | Price: {{$product->new_price}} BDT)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-control" name="status" id="status" required="">
                                    <option value="1" @if($edit_data->status == 1) selected @endif>Active</option>
                                    <option value="0" @if($edit_data->status == 0) selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12 mt-2">
                            <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fe-check-circle me-1"></i> Update Offer</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2();
    });
</script>
@endsection
