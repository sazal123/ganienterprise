@extends('backEnd.layouts.master')
@section('title','Create Offer')

@section('css')
<link href="{{asset('backEnd/assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('offer.index')}}" class="btn btn-primary rounded-pill"><i class="fe-list me-1"></i> Manage Offers</a>
                </div>
                <h4 class="page-title">Create New Offer Campaign</h4>
            </div>
        </div>
    </div>       

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('offer.store')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Offer Title *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" id="title" placeholder="e.g. Mega Summer Sale" required="">
                                @error('title')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="discount_tag" class="form-label">Discount Tag / Badge</label>
                                <input type="text" class="form-control" name="discount_tag" value="{{ old('discount_tag', 'UP TO 50% OFF') }}" id="discount_tag" placeholder="e.g. UP TO 50% OFF or FLAT 200 BDT OFF">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="subtitle" class="form-label">Subtitle / Highlight Text</label>
                                <input type="text" class="form-control" name="subtitle" value="{{ old('subtitle') }}" id="subtitle" placeholder="e.g. Exclusive deals on top items for a limited time only!">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="end_date" class="form-label">Offer End Date & Time (for Live Countdown Timer)</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" id="end_date">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="banner" class="form-label">Banner Image (Recommended 1200x400px)</label>
                                <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" id="banner">
                                @error('banner')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="product_ids" class="form-label">Select Offer Products *</label>
                                <select class="form-control select2-multiple @error('product_ids') is-invalid @enderror" name="product_ids[]" id="product_ids" multiple="multiple" data-placeholder="Choose products to include in this offer...">
                                    @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}} (Code: {{$product->product_code ?? 'N/A'}} | Price: {{$product->new_price}} BDT)</option>
                                    @endforeach
                                </select>
                                @error('product_ids')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-control" name="status" id="status" required="">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12 mt-2">
                            <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fe-check-circle me-1"></i> Save Offer</button>
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
