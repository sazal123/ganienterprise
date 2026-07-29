@extends('backEnd.layouts.master')
@section('title','Offer Management')

@section('css')
<link href="{{asset('backEnd/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('offer.create')}}" class="btn btn-primary rounded-pill"><i class="fe-plus me-1"></i> Create Offer</a>
                </div>
                <h4 class="page-title">Offer Management</h4>
            </div>
        </div>
    </div>       

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Banner</th>
                                <th>Offer Title</th>
                                <th>Discount Tag</th>
                                <th>Products</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($show_data as $key=>$value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    @if($value->banner)
                                        <img src="{{asset($value->banner)}}" class="backend-image" style="height: 40px; width: 70px; object-fit: cover; border-radius: 4px;" alt="">
                                    @else
                                        <span class="badge bg-secondary">No Banner</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{$value->title}}</strong>
                                    @if($value->subtitle)<br><small class="text-muted">{{Str::limit($value->subtitle, 40)}}</small>@endif
                                </td>
                                <td><span class="badge bg-danger">{{$value->discount_tag ?? 'Special Offer'}}</span></td>
                                <td><span class="badge bg-info">{{$value->products_count}} Products</span></td>
                                <td>{{ $value->end_date ? \Carbon\Carbon::parse($value->end_date)->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td>
                                    @if($value->status == 1)
                                        <span class="badge bg-soft-success text-success">Active</span> 
                                    @else 
                                        <span class="badge bg-soft-danger text-danger">Inactive</span> 
                                    @endif
                                </td>
                                <td>
                                    <div class="button-list">
                                        <a href="{{route('offers')}}" class="btn btn-xs btn-primary waves-effect waves-light" target="_blank" title="View Frontend"><i class="fe-eye"></i></a>
                                        <a href="{{route('offer.edit', $value->id)}}" class="btn btn-xs btn-blue waves-effect waves-light"><i class="fe-edit"></i></a>

                                        @if($value->status == 1)
                                        <form method="post" action="{{route('offer.inactive')}}" class="d-inline"> 
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">       
                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm" title="Make Inactive"><i class="fe-thumbs-down"></i></button>
                                        </form>
                                        @else
                                        <form method="post" action="{{route('offer.active')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm" title="Make Active"><i class="fe-thumbs-up"></i></button>
                                        </form>
                                        @endif

                                        <form method="post" action="{{route('offer.destroy')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="fe-trash-2"></i></button>
                                        </form>
                                    </div>
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
<script src="{{asset('backEnd/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#datatable-buttons').DataTable();
    });
</script>
@endsection
