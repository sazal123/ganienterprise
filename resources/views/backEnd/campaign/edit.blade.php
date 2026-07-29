@extends('backEnd.layouts.master')
@section('title','Landing Page Edit')
@section('css')
<link href="{{asset('backEnd/assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/summernote/summernote-lite.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('campaign.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Landing Page Edit</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form action="{{route('campaign.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="hidden_id">

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Landing Page Title *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $edit_data->name}}" id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="banner" class="form-label">Banner Image</label>
                            <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" id="banner">
                            @if($edit_data->banner)
                                <img src="{{asset($edit_data->banner)}}" alt="" class="edit-image mt-2 rounded border" style="max-height: 80px;">
                            @endif
                            @error('banner')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="top_title_1" class="form-label">Badge Tag (Top Title)</label>
                            <input type="text" class="form-control @error('top_title_1') is-invalid @enderror" name="top_title_1" value="{{ $edit_data->top_title_1 }}" id="top_title_1" placeholder="e.g. বিশেষ ডিসকাউন্ট অফার">
                            @error('top_title_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="heading_1" class="form-label">Main Heading</label>
                            <input type="text" class="form-control @error('heading_1') is-invalid @enderror" name="heading_1" value="{{ $edit_data->heading_1 }}" id="heading_1" placeholder="e.g. ধামাকা অফার!">
                            @error('heading_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="deadline" class="form-label">Offer Deadline (Countdown Timer)</label>
                            <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" name="deadline" value="{{ $edit_data->deadline }}" id="deadline">
                            @error('deadline')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">Product Categories (Optional)</label>
                            <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                    name="category_id[]" 
                                    id="category_id"
                                    multiple="multiple"
                                    data-placeholder="Choose categories...">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($select_category_ids) && in_array($cat->id, $select_category_ids)) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Selecting categories will automatically select all active products under those categories.</small>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="product_id" class="form-label">Select Products</label>
                            <select class="select2 form-control @error('product_id') is-invalid @enderror" 
                                    name="product_id[]" 
                                    id="product_id_select"
                                    multiple="multiple" 
                                    data-placeholder="Choose products...">
                                @foreach($products as $value)
                                    <option value="{{ $value->id }}" 
                                            data-category="{{ $value->category_id }}"
                                            {{ in_array($value->id, $select_product_ids) ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea name="short_description" rows="4" class="summernote form-control @error('short_description') is-invalid @enderror">{{$edit_data->short_description}}</textarea>
                            @error('short_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="description" class="form-label">Description / Details</label>
                            <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror">{{$edit_data->description}}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif>
                                <span class="slider round"></span>
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <input type="submit" class="btn btn-success" value="Update Campaign">
                    </div>

                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection

@section('script')
<script src="{{asset('backEnd/assets/libs/parsleyjs/parsley.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-validation.init.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-pickers.init.js')}}"></script>

<script src="{{asset('backEnd/assets/libs/summernote/summernote-lite.min.js')}}"></script>
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });

    $(document).ready(function () {
        $('.select2').select2();

        $('#category_id').on('change', function() {
            let selectedCatIds = $(this).val() || [];
            if (selectedCatIds.length > 0) {
                let selectedVals = [];
                $('#product_id_select option').each(function() {
                    let cat = $(this).data('category');
                    if (selectedCatIds.includes(String(cat))) {
                        selectedVals.push($(this).val());
                    }
                });
                $('#product_id_select').val(selectedVals).trigger('change');
            }
        });
    });
</script>
@endsection