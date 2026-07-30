@extends('backEnd.layouts.master')
@section('title','Stock Report')
@section('css')
<link href="{{asset('/backEnd/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('/backEnd/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<style>
 p { margin: 0; }
 @page { margin: 50px 0px 0px 0px; }
 @media print {
  td, th { font-size: 12px; padding: 4px 6px !important; }
  title { font-size: 18px; }
  header,footer,.no-print,.left-side-menu,.navbar-custom { display: none !important; }
  .content-page { margin-left: 0 !important; }
  .card { border: none !important; box-shadow: none !important; }
 }
 .summary-card {
  background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 14px 18px; text-align: center;
 }
 .summary-card .label { font-size: 12px; color: #6c757d; margin-bottom: 2px; }
 .summary-card .value { font-size: 18px; font-weight: 700; color: #212529; }
 .summary-card .negative { color: #dc3545; }
 .summary-card .positive { color: #198754; }
 .report-table th { background: #e9ecef; white-space: nowrap; text-align: center; vertical-align: middle; font-size: 12px; }
 .report-table td { text-align: center; vertical-align: middle; font-size: 12px; }
 .report-table .text-end { text-align: right !important; }
</style>
@endsection

@section('content')
<div class="container-fluid">

 <div class="row">
  <div class="col-12">
   <div class="page-title-box">
    <h4 class="page-title">Stock Report</h4>
   </div>
  </div>
 </div>

 <div class="row">
  <div class="col-12">
   <div class="card">
    <div class="card-body">

     <!-- Filters -->
     <form class="no-print row g-2 mb-3">
      <div class="col-sm-4">
       <label class="form-label small">Search</label>
       <input type="text" value="{{request()->get('keyword')}}" class="form-control form-control-sm" name="keyword" placeholder="Product name or code...">
      </div>
      <div class="col-sm-3">
       <label class="form-label small">Category</label>
       <select class="form-control form-control-sm select2" name="category_id">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
        <option value="{{$cat->id}}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{$cat->name}}</option>
        @endforeach
       </select>
      </div>
      <div class="col-sm-2">
       <label class="form-label small">Date</label>
       <input type="date" value="{{request()->get('start_date')}}" class="form-control form-control-sm flatdate" name="start_date">
      </div>
      <div class="col-sm-2">
       <label class="form-label small">&nbsp;</label>
       <input type="date" value="{{request()->get('end_date')}}" class="form-control form-control-sm flatdate" name="end_date">
      </div>
      <div class="col-sm-1 d-flex align-items-end">
       <button class="btn btn-primary btn-sm w-100"><i class="fe-search"></i></button>
      </div>
     </form>

     <!-- Summary Cards -->
     @php
       $revenueData = $revenueData ?? [];
       $totalSellingValue = $products->sum(function($p) { return $p->new_price * $p->stock; });
       $totalRemainingStock = $products->sum(function($p) use ($salesData) {
           return max($p->stock - ($salesData[$p->id] ?? 0), 0);
       });
       $totalSalesRevenue = $products->sum(function($p) use ($revenueData) {
           return $revenueData[$p->id] ?? 0;
       });
       $totalCostOfSold = $products->sum(function($p) use ($salesData) {
           $sold = $salesData[$p->id] ?? 0;
           return $sold * $p->purchase_price;
       });
       $netProfit = $totalSalesRevenue - $totalCostOfSold;
     @endphp
     <div class="row g-2 mb-3">
      <div class="col-md-2">
       <div class="summary-card">
        <div class="label">Total Stock (Pcs)</div>
        <div class="value">{{ $totalStockQty }}</div>
       </div>
      </div>
      <div class="col-md-2">
       <div class="summary-card">
        <div class="label">Total Remaining Stock (Pcs)</div>
        <div class="value">{{ $totalRemainingStock }}</div>
       </div>
      </div>
      <div class="col-md-2">
       <div class="summary-card">
        <div class="label">Total Remaining Stock Price (BDT)</div>
        <div class="value">৳{{ number_format($totalRemainingPrice, 0) }}</div>
       </div>
      </div>
      <div class="col-md-2">
       <div class="summary-card">
        <div class="label">Product Buying Cost</div>
        <div class="value">৳{{ number_format($totalBuyingCost, 0) }}</div>
       </div>
      </div>
      <div class="col-md-2">
       <div class="summary-card">
        <div class="label">Total Product Price (Selling)</div>
        <div class="value">৳{{ number_format($totalSellingValue, 0) }}</div>
       </div>
      </div>
      <div class="col-md-2">
       <div class="summary-card">
        <div class="label">Net Profit</div>
        <div class="value {{ $netProfit >= 0 ? 'positive' : 'negative' }}">{{ $netProfit >= 0 ? '+' : '' }}৳{{ number_format($netProfit, 0) }}</div>
       </div>
      </div>
     </div>

     <!-- Print & Export -->
     <div class="row mb-2">
      <div class="col-sm-6 no-print">
       {{$products->links('pagination::bootstrap-4')}}
      </div>
      <div class="col-sm-6">
       <div class="text-end no-print">
        <button onclick="printFunction()" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Print</button>
        <button id="export-excel-button" class="btn btn-info btn-sm"><i class="fas fa-file-export"></i> Export</button>
       </div>
      </div>
     </div>

     <!-- Stock Table -->
     <div id="content-to-export" class="table-responsive">
      <table class="table table-bordered table-hover report-table w-100">
       <thead>
        <tr>
         <th style="width:4%;">SL</th>
         <th style="width:6%;">Image</th>
         <th style="width:18%;">Product Name</th>
         <th style="width:10%;">Product Code</th>
         <th style="width:7%;">Colour QTY</th>
         <th style="width:9%;">Total Stock QTY</th>
         <th style="width:9%;">Total Sales QTY</th>
         <th style="width:10%;">Total Sales (BDT)</th>
         <th style="width:9%;">Remaining Stock</th>
         <th style="width:7%;">Unit Price</th>
         <th style="width:11%;">Remaining Price</th>
        </tr>
       </thead>
       <tbody>
        @php
          $grandTotalStock = 0;
          $grandTotalSales = 0;
          $grandTotalRemain = 0;
          $grandRemainPrice = 0;
          $grandSalesRevenue = 0;
        @endphp
        @forelse($products as $p)
        @php
          $soldQty = $salesData[$p->id] ?? 0;
          $remainQty = max($p->stock - $soldQty, 0);
          $unitPrice = $p->new_price;
          $remainPrice = $unitPrice * $remainQty;
          $colourCount = $p->procolors->count();
          $salesRevenue = $revenueData[$p->id] ?? 0;

          $grandTotalStock += $p->stock;
          $grandTotalSales += $soldQty;
          $grandTotalRemain += $remainQty;
          $grandRemainPrice += $remainPrice;
          $grandSalesRevenue += $salesRevenue;
        @endphp
        <tr>
         <td>{{ $loop->iteration }}</td>
         <td>
          @if($p->image)
           <img src="{{ asset($p->image->image) }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;" alt="">
          @else
           <span class="text-muted">—</span>
          @endif
         </td>
         <td style="text-align:left;font-weight:600;"><a href="javascript:void(0)" class="product-variant-link" data-id="{{ $p->id }}" style="color:#0056b3;text-decoration:none;">{{ $p->name }}</a> <small class="text-muted fw-normal" style="font-size:11px;display:block;">(Buying: ৳{{ number_format($p->purchase_price, 2) }})</small></td>
         <td class="fw-semibold"><a href="javascript:void(0)" class="product-variant-link" data-id="{{ $p->id }}" style="color:#0056b3;text-decoration:none;">{{ $p->product_code }}</a></td>
         <td>{{ $colourCount > 0 ? $colourCount : '—' }}</td>
         <td>{{ $p->stock }}</td>
         <td>{{ $soldQty }}</td>
         <td class="text-end">৳{{ number_format($salesRevenue, 2) }}</td>
         <td>{{ $remainQty }}</td>
         <td class="text-end">৳{{ number_format($unitPrice, 2) }}</td>
         <td class="text-end">৳{{ number_format($remainPrice, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center py-4 text-muted">No products found</td></tr>
        @endforelse
       </tbody>
       <tfoot>
        <tr class="fw-bold" style="background:#e9ecef;">
         <td colspan="5" class="text-end">Grand Total</td>
         <td>{{ $grandTotalStock }}</td>
         <td>{{ $grandTotalSales }}</td>
         <td class="text-end">৳{{ number_format($grandSalesRevenue, 2) }}</td>
         <td>{{ $grandTotalRemain }}</td>
         <td></td>
         <td class="text-end">৳{{ number_format($grandRemainPrice, 2) }}</td>
        </tr>
       </tfoot>
      </table>
     </div>

    </div>
   </div>
  </div>
 </div>
</div>

<!-- Variant Detail Modal -->
<div class="modal fade" id="variantDetailModal" tabindex="-1">
 <div class="modal-dialog modal-md modal-dialog-centered">
  <div class="modal-content">
   <div class="modal-header bg-dark text-white py-2">
    <h5 class="modal-title"><i class="fe-layers"></i> <span id="variantDetailTitle">Product Variants</span></h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
   </div>
   <div class="modal-body" id="variantDetailBody">
    <div class="text-center py-3 text-muted">Loading...</div>
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

  // Product variant detail popup
  $(document).on('click', '.product-variant-link', function() {
   var id = $(this).data('id');
   $('#variantDetailTitle').text('Loading...');
   $('#variantDetailBody').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2 text-muted">Loading variants...</p></div>');
   $('#variantDetailModal').modal('show');

   $.ajax({
    url: "{{ route('admin.stock_product_variants') }}",
    type: "GET",
    data: { id: id },
    dataType: "json",
    success: function(res) {
     var html = '';
     html += '<div class="text-center mb-3">';
     if (res.image) html += '<img src="'+res.image+'" style="width:60px;height:60px;object-fit:cover;border-radius:6px;" class="mb-2">';
     html += '<h5 class="fw-bold">'+res.name+'</h5></div>';

     if (res.colors && res.colors.length > 0) {
      html += '<h6 class="fw-bold mt-3 mb-2" style="color:#1a237e;"><i class="fe-droplet"></i> Colors</h6>';
      html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0" style="font-size:12px;"><thead class="table-light"><tr><th style="width:50px;">Image</th><th>Color</th><th class="text-end">Price (৳)</th><th class="text-end">Stock</th></tr></thead><tbody>';
      $.each(res.colors, function(i, c) {
       var imgHtml = c.image ? '<img src="'+c.image+'" style="width:36px;height:36px;object-fit:cover;border-radius:4px;">' : '<span class="text-muted">—</span>';
       html += '<tr><td>'+imgHtml+'</td><td>'+c.name+'</td><td class="text-end">'+(c.price ? '৳'+parseFloat(c.price).toFixed(2) : '—')+'</td><td class="text-end">'+(c.stock || '—')+'</td></tr>';
      });
      html += '</tbody></table></div>';
     }

     if (res.sizes && res.sizes.length > 0) {
      html += '<h6 class="fw-bold mt-3 mb-2" style="color:#1a237e;"><i class="fe-maximize"></i> Sizes</h6>';
      html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0" style="font-size:12px;"><thead class="table-light"><tr><th>Size</th><th class="text-end">Price (৳)</th><th class="text-end">Stock</th></tr></thead><tbody>';
      $.each(res.sizes, function(i, s) {
       html += '<tr><td>'+s.name+'</td><td class="text-end">'+(s.price ? '৳'+parseFloat(s.price).toFixed(2) : '—')+'</td><td class="text-end">'+(s.stock || '—')+'</td></tr>';
      });
      html += '</tbody></table></div>';
     }

     if ((!res.colors || res.colors.length === 0) && (!res.sizes || res.sizes.length === 0)) {
      html += '<p class="text-muted text-center py-3">No color or size variants for this product.</p>';
     }

     $('#variantDetailTitle').text(res.name + ' - Variants');
     $('#variantDetailBody').html(html);
    },
    error: function() {
     $('#variantDetailBody').html('<div class="alert alert-danger py-3 text-center">Failed to load variant data.</div>');
    }
   });
  });
 });
 function printFunction() { window.print(); }
 $('#export-excel-button').on('click', function() {
  var table = document.querySelector('.report-table');
  var html = table.outerHTML;
  var blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel' });
  var url = URL.createObjectURL(blob);
  var a = document.createElement('a');
  a.href = url;
  a.download = 'stock_report.xls';
  a.click();
  URL.revokeObjectURL(url);
 });
</script>
@endsection
