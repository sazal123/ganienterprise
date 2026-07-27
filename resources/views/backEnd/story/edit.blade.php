@extends('backEnd.layouts.master')
@section('title','Story Edit')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right"><a href="{{route('stories.index')}}" class="btn btn-primary rounded-pill">Manage</a></div>
                <h4 class="page-title">Story Edit</h4>
            </div>
        </div>
    </div>
   <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{route('stories.update')}}" method="POST" enctype="multipart/form-data" class="row">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="id">
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="{{$edit_data->title}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="product_id" class="form-label">Linked Product</label>
                            <select class="form-control" name="product_id">
                                <option value="">Select product...</option>
                                @foreach($products as $p)
                                <option value="{{$p->id}}" @if($edit_data->product_id==$p->id)selected @endif>{{$p->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="order_id" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="order_id" value="{{$edit_data->order_id}}" min="0">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="video" class="form-label">Video</label>
                            <input type="file" class="form-control" name="video" accept="video/*">
                            @if($edit_data->video)<small class="text-muted">Current: {{$edit_data->video}}</small>@endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail</label>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                            @if($edit_data->thumbnail)<br><img src="{{asset($edit_data->thumbnail)}}" style="height:50px;">@endif
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch"><input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif><span class="slider round"></span></label>
                        </div>
                    </div>
                    <div><input type="submit" class="btn btn-success" value="Submit"></div>
                </form>
            </div>
        </div>
    </div>
   </div>
</div>
@endsection
