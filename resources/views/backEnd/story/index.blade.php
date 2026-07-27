@extends('backEnd.layouts.master')
@section('title','Stories Manage')
@section('css')
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('stories.create')}}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Stories Manage</h4>
            </div>
        </div>
    </div>
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr><th>SL</th><th>Title</th><th>Product</th><th>Thumbnail</th><th>Order</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @foreach($data as $k=>$v)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$v->title}}</td>
                            <td>{{$v->product ? $v->product->name : 'N/A'}}</td>
                            <td>@if($v->thumbnail)<img src="{{asset($v->thumbnail)}}" style="height:50px;">@endif</td>
                            <td>{{$v->order_id}}</td>
                            <td>@if($v->status==1)<span class="badge bg-soft-success text-success">Active</span>@else<span class="badge bg-soft-danger text-danger">Inactive</span>@endif</td>
                            <td>
                                <a href="{{route('stories.edit',$v->id)}}" class="btn btn-xs btn-primary"><i class="fe-edit-1"></i></a>
                                <form method="post" action="{{route('stories.destroy')}}" class="d-inline">@csrf
                                    <input type="hidden" value="{{$v->id}}" name="hidden_id">
                                    <button type="submit" class="btn btn-xs btn-danger delete-confirm"><i class="mdi mdi-close"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   </div>
</div>
@endsection
@section('script')
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/datatables.init.js"></script>
@endsection
