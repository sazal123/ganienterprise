@extends('backEnd.layouts.master')
@section('title','Customer Summary Report')
@section('css')
<link href="{{asset('/backEnd/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('/backEnd/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    p { margin: 0; }
    @page { margin: 50px 0px 0px 0px; }
    @media print {
        td, th { font-size: 11px; padding: 4px 6px !important; }
        title { font-size: 18px; }
        header, footer, .no-print, .left-side-menu, .navbar-custom { display: none !important; }
        .content-page { margin-left: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
    .report-table { font-size: 12px; }
    .report-table th {
        white-space: nowrap;
        background: #f8f9fa;
        text-align: center;
        vertical-align: middle;
    }
    .report-table td { vertical-align: middle; }
    .month-col { text-align: center; min-width: 55px; }
    .num-col { text-align: right; }
    .total-row { background: #e8f4f8 !important; font-weight: 600; }
    .total-row td { border-top: 2px solid #333; }
    .feedback-text { max-width: 120px; white-space: normal; word-wrap: break-word; font-size: 11px; }
    .summary-box {
        background: #f0f9ff;
        border: 1px solid #cce5ff;
        border-radius: 8px;
        padding: 12px 18px;
        text-align: center;
    }
    .summary-box h5 { margin: 0; font-size: 14px; color: #666; }
    .summary-box .value { font-size: 20px; font-weight: 700; color: #0056b3; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Customer Summary Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form class="no-print row mb-3">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label">Search</label>
                                <input type="text" value="{{request()->get('keyword')}}" class="form-control" name="keyword" placeholder="Name, phone, code...">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="Premium" {{ request('status') == 'Premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="General" {{ request('status') == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" value="{{request()->get('start_date')}}" class="form-control flatdate" name="start_date">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="date" value="{{request()->get('end_date')}}" class="form-control flatdate" name="end_date">
                            </div>
                        </div>
                        <div class="col-sm-1 d-flex align-items-end">
                            <div class="form-group">
                                <button class="btn btn-primary"><i class="fe-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-3 no-print">
                        <div class="col-sm-4">
                            <div class="summary-box">
                                <h5>Total Customers</h5>
                                <div class="value">{{ $totalCustomers }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="summary-box">
                                <h5>Total Deals</h5>
                                <div class="value">{{ $totalDeals }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="summary-box">
                                <h5>Total Revenue</h5>
                                <div class="value">৳{{ number_format($totalRevenue, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Print & Export -->
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <div class="export-print text-end no-print">
                                <button onclick="printFunction()" class="btn btn-success"><i class="fa fa-print"></i> Print</button>
                                <button id="export-excel-button" class="btn btn-info"><i class="fas fa-file-export"></i> Export Excel</button>
                                <a href="{{ route('customers.report') }}" class="btn btn-secondary"><i class="fe-refresh-cw"></i> Reset</a>
                            </div>
                        </div>
                    </div>

                    <!-- Report Table -->
                    <div id="content-to-export" class="table-responsive">
                        <table class="table table-bordered nowrap w-100 report-table" id="customerReportTable">
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
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandTotalDeals = 0;
                                    $grandTotalValue = 0;
                                    $monthlyGrandTotals = array_fill(0, count($months), 0);
                                @endphp
                                @forelse($show_data as $key => $value)
                                @php
                                    $orders = $value->orders;
                                    $orderCount = $orders->count();
                                    $totalOrderValue = $orders->sum('amount');
                                    $lastOrder = $orders->sortByDesc('created_at')->first();

                                    $categoryNames = \App\Models\OrderDetails::whereIn('order_id', $orders->pluck('id'))
                                        ->join('products', 'order_details.product_id', '=', 'products.id')
                                        ->join('categories', 'products.category_id', '=', 'categories.id')
                                        ->distinct()
                                        ->pluck('categories.name')
                                        ->toArray();
                                    $productCategories = implode(', ', array_unique($categoryNames));

                                    $customerMonthly = isset($monthlyOrders[$value->id]) ? $monthlyOrders[$value->id]->keyBy('month_key') : collect();

                                    $grandTotalDeals += $orderCount;
                                    $grandTotalValue += $totalOrderValue;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><strong>{{ $value->customer_code ?? 'GE-OR' . date('y') . '-' . str_pad($value->id, 2, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ $value->name }}</td>
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
                                    <td class="text-center">
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
                                            {{ Str::limit($productCategories, 25) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="num-col">৳{{ number_format($totalOrderValue, 2) }}</td>
                                    <td class="text-center">
                                        @if($lastOrder)
                                            {{ date('d-m-Y', strtotime($lastOrder->created_at)) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="feedback-text">{{ $value->feedback ?? 'N/A' }}</td>
                                    @foreach($months as $mi => $month)
                                    @php
                                        if(isset($customerMonthly[$month])) {
                                            $monthVal = $customerMonthly[$month]->total;
                                            $monthlyGrandTotals[$mi] += $monthVal;
                                        } else {
                                            $monthVal = 0;
                                        }
                                    @endphp
                                    <td class="num-col month-col">
                                        @if($monthVal > 0)
                                            ৳{{ number_format($monthVal, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ 14 + count($months) }}" class="text-center text-muted py-4">No customers found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="9" class="text-end"><strong>Grand Total</strong></td>
                                    <td class="text-center"><strong>{{ $grandTotalDeals }}</strong></td>
                                    <td></td>
                                    <td class="num-col"><strong>৳{{ number_format($grandTotalValue, 2) }}</strong></td>
                                    <td colspan="2"></td>
                                    @foreach($monthlyGrandTotals as $mgTotal)
                                    <td class="num-col month-col"><strong>৳{{ number_format($mgTotal, 2) }}</strong></td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backEnd/assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
        flatpickr(".flatdate", {});
    });

    function printFunction() {
        window.print();
    }

    $('#export-excel-button').on('click', function() {
        var table = document.getElementById('customerReportTable');
        var html = table.outerHTML;
        var blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'customer_summary_report.xls';
        a.click();
        URL.revokeObjectURL(url);
    });
</script>
@endsection
