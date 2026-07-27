@extends('backEnd.layouts.master') @section('title','Product Edit') @section('css')
<style>
  .increment_btn,
  .remove_btn,
  .btn-warning {
    margin-top: -17px;
    margin-bottom: 10px;
  }
</style>
<link href="{{asset('backEnd/assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/summernote/summernote-lite.min.css')}}" rel="stylesheet" type="text/css" />
@endsection @section('content')
<div class="container-fluid">
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <div class="page-title-right">
          <a href="{{route('products.index')}}" class="btn btn-primary rounded-pill">Manage</a>
        </div>
        <h4 class="page-title">Product Edit</h4>
      </div>
    </div>
  </div>
  <!-- end page title -->
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <form action="{{route('products.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
            @csrf
            <input type="hidden" value="{{$edit_data->id}}" name="id" />
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="name" class="form-label">Product Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$edit_data->name }}" id="name" required="" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="category_id" class="form-label">Categories *</label>
                <select class="form-control form-select select2 @error('category_id') is-invalid @enderror" name="category_id" value="{{ old('category_id') }}" required>
                  <optgroup>
                    <option value="">Select..</option>
                    @foreach($categories as $category)
                    <option value="{{$category->id}}" @if($edit_data->category_id==$category->id) selected @endif>{{$category->name}}</option>
                    @foreach ($category->childrenCategories as $childCategory)<option value="{{$childCategory->id}}" @if($edit_data->category_id==$childCategory->id) selected @endif>- {{$childCategory->name}}</option>
                    } @endforeach @endforeach
                  </optgroup>
                </select>
                @error('category_id')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="subcategory_id" class="form-label">SubCategories (Optional)</label>
                <select class="form-control form-select select2-multiple @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id" data-placeholder="Choose ...">
                  <optgroup>
                    <option value="">Select..</option>
                    @foreach($subcategory as $key=>$value)
                    <option value="{{$value->id}}">{{$value->subcategoryName}}</option>
                    @endforeach
                  </optgroup>
                </select>
                @error('subcategory_id')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
             <!-- col end -->
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="childcategory_id" class="form-label">Child Categories (Optional)</label>
                <select class="form-control form-select select2-multiple @error('childcategory_id') is-invalid @enderror" id="childcategory_id" name="childcategory_id" data-placeholder="Choose ...">
                  <optgroup>
                    <option value="">Select..</option>
                    @foreach($childcategory as $key=>$value)
                    <option value="{{$value->id}}">{{$value->childcategoryName}}</option>
                    @endforeach
                  </optgroup>
                </select>
                @error('childcategory_id')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="brand_id" class="form-label">Brands</label>
                <select class="form-control select2 @error('brand_id') is-invalid @enderror" value="{{ old('brand_id') }}" name="brand_id">
                  <option value="">Select..</option>
                  @foreach($brands as $value)
                  <option value="{{$value->id}}" @if($edit_data->brand_id==$value->id) selected @endif>{{$value->name}}</option>
                  @endforeach
                </select>
                @error('brand_id')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="purchase_price" class="form-label">Purchase Price *</label>
                <input type="text" class="form-control @error('purchase_price') is-invalid @enderror" name="purchase_price" value="{{ $edit_data->purchase_price}}" id="purchase_price" required />
                @error('purchase_price')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->
            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="old_price" class="form-label">Old Price *</label>
                <input type="text" class="form-control @error('old_price') is-invalid @enderror" name="old_price" value="{{ $edit_data->old_price }}" id="old_price" />
                @error('old_price')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->
            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="new_price" class="form-label">New Price *</label>
                <input type="text" class="form-control @error('new_price') is-invalid @enderror" name="new_price" value="{{ $edit_data->new_price }}" id="new_price" required />
                @error('new_price')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->
            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="stock" class="form-label">Stock *</label>
                <input type="text" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ $edit_data->stock }}" id="stock" />
                @error('stock')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->

            <div class="col-sm-4 mb-3">
              <label for="image">Image *</label>
              <div class="input-group control-group increment">
                <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" />
                <div class="input-group-btn">
                  <button class="btn btn-success btn-increment" type="button"><i class="fa fa-plus"></i></button>
                </div>
                @error('image')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>

              <div class="clone hide" style="display: none;">
                <div class="control-group input-group">
                  <input type="file" name="image[]" class="form-control" />
                  <div class="input-group-btn">
                    <button class="btn btn-danger" type="button"><i class="fa fa-trash"></i></button>
                  </div>
                </div>
              </div>
              <div class="product_img">
                @foreach($edit_data->mainImages as $image)
                <img src="{{asset($image->image)}}" class="edit-image border" alt="" />
                <a href="{{route('products.image.destroy',['id'=>$image->id])}}" class="btn btn-xs btn-danger waves-effect waves-light"><i class="mdi mdi-close"></i></a>
                @endforeach
              </div>
            </div>
            <!-- col end -->
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="pro_unit" class="form-label">Product Unit (Optional)</label>
                <input type="text" class="form-control @error('pro_unit') is-invalid @enderror" name="pro_unit" value="{{ $edit_data->pro_unit }}" id="pro_unit" />
                @error('pro_unit')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="pro_video" class="form-label">Product Video (Optional)</label>
                <input type="text" class="form-control @error('pro_video') is-invalid @enderror" name="pro_video" value="{{ $edit_data->pro_video }}" id="pro_video" />
                @error('pro_video')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <!-- Dynamic Size Variants -->
            <div class="col-sm-12">
              <div class="card bg-light mb-3">
                <div class="card-header bg-primary text-white">
                  <h5 class="mb-0 text-white">Size Variants (Optional)</h5>
                </div>
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-sm-12">
                      <label for="proSizeEdit" class="form-label">Select Sizes</label>
                      <select class="form-control select2" id="proSizeEdit" multiple="multiple">
                        @foreach($totalsizes as $size)
                        <option value="{{$size->id}}" data-name="{{$size->sizeName}}"
                          @foreach($selectsizes as $ss) @if($size->id == $ss->size_id) selected @endif @endforeach>
                          {{$size->sizeName}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="sizeVariantTable">
                          <thead class="table-secondary">
                            <tr>
                              <th width="30%">Size</th>
                              <th width="25%">Price</th>
                              <th width="25%">Stock</th>
                              <th width="20%">Action</th>
                            </tr>
                          </thead>
                          <tbody id="sizeVariantBody">
                            @php $hasSize = false; @endphp
                            @foreach($selectsizes as $ss)
                            @php $hasSize = true; @endphp
                            <tr id="sizeRow_{{$ss->size_id}}" data-id="{{$ss->size_id}}">
                              <td>{{$ss->size->sizeName ?? 'N/A'}}<input type="hidden" name="proSize[]" value="{{$ss->size_id}}"></td>
                              <td><input type="number" step="0.01" class="form-control form-control-sm" name="sizePrice[{{$ss->size_id}}]" placeholder="Price" value="{{$ss->price}}"></td>
                              <td><input type="number" class="form-control form-control-sm" name="sizeStock[{{$ss->size_id}}]" placeholder="Stock" value="{{$ss->stock}}"></td>
                              <td><button type="button" class="btn btn-danger btn-sm remove-size-variant" data-id="{{$ss->size_id}}"><i class="fa fa-trash"></i></button></td>
                            </tr>
                            @endforeach
                            @if(!$hasSize)
                            <tr id="noSizeRow">
                              <td colspan="4" class="text-center text-muted">Select sizes above to add variants</td>
                            </tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- col end -->

            <!-- Dynamic Color Variants -->
            <div class="col-sm-12">
              <div class="card bg-light mb-3">
                <div class="card-header bg-success text-white">
                  <h5 class="mb-0 text-white">Color Variants (Optional)</h5>
                </div>
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-sm-12">
                      <label for="proColorEdit" class="form-label">Select Colors</label>
                      <select class="form-control select2" id="proColorEdit" multiple="multiple">
                        @foreach($totalcolors as $color)
                        <option value="{{$color->id}}" data-name="{{$color->colorName}}"
                          @foreach($selectcolors as $sc) @if($color->id == $sc->color_id) selected @endif @endforeach>
                          {{$color->colorName}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="colorVariantTable">
                          <thead class="table-secondary">
                            <tr>
                              <th width="20%">Color</th>
                              <th width="20%">Price</th>
                              <th width="15%">Stock</th>
                              <th width="30%">Image</th>
                              <th width="15%">Action</th>
                            </tr>
                          </thead>
                          <tbody id="colorVariantBody">
                            @php $hasColor = false; @endphp
                            @foreach($selectcolors as $sc)
                            @php
                              $hasColor = true;
                              $colorImage = $edit_data->images()->where('color_id', $sc->color_id)->first();
                            @endphp
                            <tr id="colorRow_{{$sc->color_id}}" data-id="{{$sc->color_id}}">
                              <td>{{$sc->color->colorName ?? 'N/A'}}<input type="hidden" name="proColor[]" value="{{$sc->color_id}}"></td>
                              <td><input type="number" step="0.01" class="form-control form-control-sm" name="colorPrice[{{$sc->color_id}}]" placeholder="Price" value="{{$sc->price}}"></td>
                              <td><input type="number" class="form-control form-control-sm" name="colorStock[{{$sc->color_id}}]" placeholder="Stock" value="{{$sc->stock}}"></td>
                              <td>
                                <input type="file" name="colorImage[{{$sc->color_id}}]" class="form-control form-control-sm">
                                @if($colorImage)
                                <div class="mt-1">
                                  <img src="{{asset($colorImage->image)}}" width="50" height="50" class="border" alt="">
                                  <a href="{{route('products.image.destroy',['id'=>$colorImage->id])}}" class="btn btn-xs btn-danger waves-effect waves-light"><i class="mdi mdi-close"></i></a>
                                </div>
                                @endif
                              </td>
                              <td><button type="button" class="btn btn-danger btn-sm remove-color-variant" data-id="{{$sc->color_id}}"><i class="fa fa-trash"></i></button></td>
                            </tr>
                            @endforeach
                            @if(!$hasColor)
                            <tr id="noColorRow">
                              <td colspan="5" class="text-center text-muted">Select colors above to add variants</td>
                            </tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- col end -->
            <div class="col-sm-12 mb-3">
              <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror">{{$edit_data->description}}</textarea>
                @error('description')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->
           <div class="col-sm-12 mb-3">
              <div class="form-group">
                <label for="note" class="form-label">Note</label>
                <textarea name="note" rows="6" class=" form-control @error('note') is-invalid @enderror">{{$edit_data->note}}</textarea>
                @error('note')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->
             <div class="col-sm-3 mb-3">
              <div class="form-group mb-3">
                <label for="sold" class="form-label">Sold</label>
                <input type="text" class="form-control @error('sold') is-invalid @enderror" name="sold" value="{{ $edit_data->sold }}" id="sold" />
                @error('sold')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->
            <div class="col-sm-3 mb-3">
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
            <!-- col end -->
            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="topsale" class="d-block">Hot Deals</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1)checked @endif>
                  <span class="slider round"></span>
                </label>
                @error('topsale')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <!-- col end -->
            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="flashsale" class="d-block">Flash Sales</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="flashsale" @if($edit_data->flashsale==1)checked @endif>
                  <span class="slider round"></span>
                </label>
                @error('flashsale')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->
            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="is_new" class="d-block">New Collection</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="is_new" @if($edit_data->is_new==1)checked @endif>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>
            <!-- col end -->
            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="is_prime" class="d-block">Prime Collection</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="is_prime" @if($edit_data->is_prime==1)checked @endif>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>
            <!-- col end -->

            <div>
              <input type="submit" class="btn btn-success" value="Submit" />
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
@endsection @section('script')
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
    $(".btn-increment").click(function () {
      var html = $(".clone").html();
      $(".increment").after(html);
    });
    $("body").on("click", ".btn-danger", function () {
      $(this).parents(".control-group").remove();
    });
  });
</script>
<script type="text/javascript">
  $(document).ready(function () {
    $(".select2").select2();
  });

  // ===== Dynamic Size Variants (Edit) =====
  $('#proSizeEdit').on('change', function() {
    var selected = $(this).val() || [];
    var currentRows = [];
    $('#sizeVariantBody tr').each(function() {
      var id = $(this).data('id');
      if (id) currentRows.push(String(id));
    });

    // Remove rows for unselected sizes
    currentRows.forEach(function(id) {
      if (!selected.includes(id)) {
        $('#sizeRow_' + id).remove();
      }
    });

    // Add rows for newly selected sizes
    selected.forEach(function(id) {
      if (!currentRows.includes(id)) {
        var name = $('#proSizeEdit option[value="' + id + '"]').data('name');
        var row = '<tr id="sizeRow_' + id + '" data-id="' + id + '">' +
          '<td>' + name + '<input type="hidden" name="proSize[]" value="' + id + '"></td>' +
          '<td><input type="number" step="0.01" class="form-control form-control-sm" name="sizePrice[' + id + ']" placeholder="Price" value=""></td>' +
          '<td><input type="number" class="form-control form-control-sm" name="sizeStock[' + id + ']" placeholder="Stock" value=""></td>' +
          '<td><button type="button" class="btn btn-danger btn-sm remove-size-variant" data-id="' + id + '"><i class="fa fa-trash"></i></button></td>' +
          '</tr>';
        $('#noSizeRow').hide();
        $('#sizeVariantBody').append(row);
      }
    });

    if (selected.length === 0) {
      $('#sizeVariantBody').empty();
      $('#sizeVariantBody').append('<tr id="noSizeRow"><td colspan="4" class="text-center text-muted">Select sizes above to add variants</td></tr>');
    }
  });

  $(document).on('click', '.remove-size-variant', function() {
    var id = $(this).data('id');
    $('#sizeRow_' + id).remove();
    $('#proSizeEdit option[value="' + id + '"]').prop('selected', false);
    $('#proSizeEdit').trigger('change');
    if ($('#sizeVariantBody tr').length === 0) {
      $('#sizeVariantBody').append('<tr id="noSizeRow"><td colspan="4" class="text-center text-muted">Select sizes above to add variants</td></tr>');
    }
  });

  // ===== Dynamic Color Variants (Edit) =====
  $('#proColorEdit').on('change', function() {
    var selected = $(this).val() || [];
    var currentRows = [];
    $('#colorVariantBody tr').each(function() {
      var id = $(this).data('id');
      if (id) currentRows.push(String(id));
    });

    // Remove rows for unselected colors
    currentRows.forEach(function(id) {
      if (!selected.includes(id)) {
        $('#colorRow_' + id).remove();
      }
    });

    // Add rows for newly selected colors
    selected.forEach(function(id) {
      if (!currentRows.includes(id)) {
        var name = $('#proColorEdit option[value="' + id + '"]').data('name');
        var row = '<tr id="colorRow_' + id + '" data-id="' + id + '">' +
          '<td>' + name + '<input type="hidden" name="proColor[]" value="' + id + '"></td>' +
          '<td><input type="number" step="0.01" class="form-control form-control-sm" name="colorPrice[' + id + ']" placeholder="Price" value=""></td>' +
          '<td><input type="number" class="form-control form-control-sm" name="colorStock[' + id + ']" placeholder="Stock" value=""></td>' +
          '<td><input type="file" name="colorImage[' + id + ']" class="form-control form-control-sm"></td>' +
          '<td><button type="button" class="btn btn-danger btn-sm remove-color-variant" data-id="' + id + '"><i class="fa fa-trash"></i></button></td>' +
          '</tr>';
        $('#noColorRow').hide();
        $('#colorVariantBody').append(row);
      }
    });

    if (selected.length === 0) {
      $('#colorVariantBody').empty();
      $('#colorVariantBody').append('<tr id="noColorRow"><td colspan="5" class="text-center text-muted">Select colors above to add variants</td></tr>');
    }
  });

  $(document).on('click', '.remove-color-variant', function() {
    var id = $(this).data('id');
    $('#colorRow_' + id).remove();
    $('#proColorEdit option[value="' + id + '"]').prop('selected', false);
    $('#proColorEdit').trigger('change');
    if ($('#colorVariantBody tr').length === 0) {
      $('#colorVariantBody').append('<tr id="noColorRow"><td colspan="5" class="text-center text-muted">Select colors above to add variants</td></tr>');
    }
  });

  // category to sub
  $("#category_id").on("change", function () {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: "GET",
        url: "{{url('ajax-product-subcategory')}}?category_id=" + ajaxId,
        success: function (res) {
          if (res) {
            $("#subcategory_id").empty();
            $("#subcategory_id").append('<option value="0">Choose...</option>');
            $.each(res, function (key, value) {
              $("#subcategory_id").append('<option value="' + key + '">' + value + "</option>");
            });
          } else {
            $("#subcategory_id").empty();
          }
        },
      });
    } else {
      $("#subcategory_id").empty();
    }
  });

  // subcategory to childcategory
  $("#subcategory_id").on("change", function () {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: "GET",
        url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + ajaxId,
        success: function (res) {
          if (res) {
            $("#childcategory_id").empty();
            $("#childcategory_id").append('<option value="0">Choose...</option>');
            $.each(res, function (key, value) {
              $("#childcategory_id").append('<option value="' + key + '">' + value + "</option>");
            });
          } else {
            $("#childcategory_id").empty();
          }
        },
      });
    } else {
      $("#childcategory_id").empty();
    }
  });
</script>
<script type="text/javascript">
  document.forms["editForm"].elements["category_id"].value = "{{$edit_data->category_id}}";
  document.forms["editForm"].elements["subcategory_id"].value = "{{$edit_data->subcategory_id}}";
  document.forms["editForm"].elements["childcategory_id"].value = "{{$edit_data->childcategory_id}}";
</script>
@endsection
