@extends('backEnd.layouts.master')
@section('title','Customer Manage')

@section('css')
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<style>
  .customer-table { font-size: 13px; }
  .customer-table th { white-space: nowrap; background: #f8f9fa; }
  .customer-table td { vertical-align: middle; }
  .search-box { background: #fff; border: 1px solid #dee2e6; border-radius: 6px; padding: 3px; }
  .search-box .form-control { border: none; box-shadow: none; }
  .search-box .btn { border-radius: 4px; }
  .feedback-text { max-width: 150px; white-space: normal; word-wrap: break-word; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('customers.create')}}" class="btn btn-success rounded-pill"><i class="fe-plus-circle"></i> Add New</a>
                </div>
                <h4 class="page-title">Customer Manage</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Search & Filter -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body py-2">
                    <form action="{{route('customers.index')}}" method="GET" class="row align-items-end">
                        <div class="col-md-5">
                            <div class="input-group search-box">
                                <input type="text" class="form-control" name="keyword" value="{{request()->get('keyword')}}" placeholder="Search by name, phone, email, address, customer code, whatsapp, feedback...">
                                <button class="btn btn-info" type="submit"><i class="fe-search"></i> Search</button>
                                @if(request()->get('keyword') || request()->get('status'))
                                <a href="{{route('customers.index')}}" class="btn btn-secondary">Clear</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="Premium" {{request()->get('status') == 'Premium' ? 'selected' : ''}}>Premium</option>
                                <option value="General" {{request()->get('status') == 'General' ? 'selected' : ''}}>General</option>
                                <option value="Inactive" {{request()->get('status') == 'Inactive' ? 'selected' : ''}}>Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100 customer-table">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Customer ID</th>
                                    <th>Customer's Name</th>
                                    <th>Address</th>
                                    <th>Thana</th>
                                    <th>District</th>
                                    <th>Contact</th>
                                    <th>WhatsApp No.</th>
                                    <th>Customer's Status</th>
                                    <th>No. of Deal</th>
                                    <th>Ordered Product Category</th>
                                    <th>Total Order Value (tk)</th>
                                    <th>Last Order Date</th>
                                    <th>Feedback (Customer)</th>
                                    @foreach($months as $month)
                                    <th class="month-col">{{ $month }}</th>
                                    @endforeach
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($show_data as $key => $value)
                                @php
                                    $orders = $value->orders;
                                    $orderCount = $orders->count();
                                    $totalOrderValue = $orders->sum('amount');
                                    $lastOrder = $orders->sortByDesc('created_at')->first();

                                    // Get categories from ordered products
                                    $categoryNames = \App\Models\OrderDetails::whereIn('order_id', $orders->pluck('id'))
                                        ->join('products', 'order_details.product_id', '=', 'products.id')
                                        ->join('categories', 'products.category_id', '=', 'categories.id')
                                        ->distinct()
                                        ->pluck('categories.name')
                                        ->toArray();
                                    $productCategories = implode(', ', array_unique($categoryNames));

                                    // Get monthly order data for this customer
                                    $customerMonthly = isset($monthlyOrders[$value->id]) ? $monthlyOrders[$value->id]->keyBy('month_key') : collect();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $value->customer_code ?? 'GE-OR' . date('y') . '-' . str_pad($value->id, 2, '0', STR_PAD_LEFT) }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{route('customers.profile',['id'=>$value->id])}}" class="text-primary fw-semibold">
                                            {{ $value->name }}
                                        </a>
                                    </td>
                                    <td>{{ $value->address ?? 'N/A' }}</td>
                                    <td>{{ $value->cust_area ? $value->cust_area->area_name : ($value->area ?? 'N/A') }}</td>
                                    <td>
                                        @if($value->district)
                                            @php $district = \App\Models\District::find($value->district); @endphp
                                            {{ $district ? $district->district : 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $value->phone ?? 'N/A' }}</td>
                                    <td>{{ $value->whatsapp ?? 'N/A' }}</td>
                                    <td>
                                        @if($value->status == 'Premium')
                                            <span class="badge bg-soft-warning text-warning">Premium</span>
                                        @elseif($value->status == 'Inactive')
                                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                                        @else
                                            <span class="badge bg-soft-success text-success">General</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $orderCount }}</td>
                                    <td>
                                        @if($productCategories)
                                            <span class="badge bg-soft-info text-info">{{ Str::limit($productCategories, 30) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">৳{{ number_format($totalOrderValue, 2) }}</td>
                                    <td>
                                        @if($lastOrder)
                                            {{ date('d-m-Y', strtotime($lastOrder->created_at)) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($value->feedback)
                                            <span class="feedback-text">{{ $value->feedback }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    @foreach($months as $month)
                                    <td class="text-end month-col">
                                        @if(isset($customerMonthly[$month]))
                                            ৳{{ number_format($customerMonthly[$month]->total, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    <td>
                                        <div class="button-list" style="display:flex; gap:3px; flex-wrap:nowrap;">
                                            @if($value->status != 'Inactive')
                                            <form method="post" action="{{route('customers.inactive')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm" title="Set Inactive"><i class="fe-thumbs-down"></i></button>
                                            </form>
                                            @else
                                            <form method="post" action="{{route('customers.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm" title="Set General"><i class="fe-thumbs-up"></i></button>
                                            </form>
                                            @endif
                                            <a href="{{route('customers.edit',$value->id)}}" class="btn btn-xs btn-primary waves-effect waves-light" title="Edit"><i class="fe-edit-1"></i></a>
                                            <a href="{{route('customers.profile',['id'=>$value->id])}}" class="btn btn-xs btn-blue waves-effect waves-light" title="View Profile"><i class="fe-eye"></i></a>
                                            <form method="post" action="{{route('customers.adminlog')}}" class="d-inline" target="_blank">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-pink waves-effect waves-light change-confirm" title="Login as customer"><i class="fe-log-in"></i></button>
                                            </form>
                                            <form method="post" action="{{route('customers.destroy')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm" title="Delete"><i class="fe-trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @if(count($show_data) == 0)
                                <tr>
                                    <td colspan="{{ 15 + count($months) }}" class="text-center text-muted py-4">No customers found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="custom-paginate">
                        {{$show_data->appends(request()->query())->links('pagination::bootstrap-4')}}
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>

<!-- Delete Modal -->
<form action="{{route('customers.destroy')}}" method="post">
    @csrf
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" name="hidden_id" id="delete_id">
                    <h5 class="text-danger">Are you sure?</h5>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
<!-- third party js -->
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/datatables.init.js"></script>
<!-- third party js ends -->
<script>
    $(document).ready(function() {
        // Delete confirmation
        $(document).on('click', '.delete-confirm', function() {
            var id = $(this).closest('form').find('input[name="hidden_id"]').val();
            $('#delete_id').val(id);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endsection
