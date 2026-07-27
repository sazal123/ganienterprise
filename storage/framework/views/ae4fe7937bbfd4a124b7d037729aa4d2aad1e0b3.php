
<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('css'); ?>
<!-- Plugins css -->
<link href="<?php echo e(asset('backEnd/assets/libs/flatpickr/flatpickr.min.css')); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo e(asset('backEnd/assets/libs/selectize/css/selectize.bootstrap3.css')); ?>" rel="stylesheet" type="text/css" />

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>     
    <!-- end page title --> 

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                <i class="fe-shopping-cart font-22 avatar-title text-primary"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup"><?php echo e($total_order); ?></span></h3>
                                <p class="text-muted mb-1 text-truncate">Total Oreder</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                <i class="fe-shopping-bag font-22 avatar-title text-success"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup"><?php echo e($today_order); ?></span></h3>
                                <p class="text-muted mb-1 text-truncate">Today's Order</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fe-database font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup"><?php echo e($total_product); ?></span></h3>
                                <p class="text-muted mb-1 text-truncate">Products</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                <i class="fe-user font-22 avatar-title text-warning"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup"><?php echo e($total_customer); ?></span></h3>
                                <p class="text-muted mb-1 text-truncate">Customer</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->


    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Edit Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Latest 5 Orders</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Id</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $latest_order; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td style="width: 36px;">
                                        <img src="<?php echo e(asset($order->product?$order->product->image->image:'')); ?>" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <?php echo e($order->invoice_id); ?>

                                    </td>

                                    <td>
                                        <?php echo e($order->amount); ?>

                                    </td>

                                    <td>
                                        <?php echo e($order->customer?$order->customer->name:''); ?>

                                    </td>
                                    <td>
                                        <?php echo e($order->order_status); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Edit Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Latest Customers</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap table-hover table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $latest_customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal"><?php echo e($loop->iteration); ?></h5>
                                    </td>

                                    <td>
                                        <?php echo e($customer->name); ?>

                                    </td>

                                    <td>
                                        <?php echo e($customer->phone); ?>

                                    </td>

                                    <td>
                                        <?php echo e($customer->created_at->format('d-m-Y')); ?>

                                    </td>

                                    <td>
                                        <?php echo e($customer->status); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div>
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->
    
</div> <!-- container -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
 <!-- Plugins js-->
        <script src="<?php echo e(asset('backEnd/assets/libs/flatpickr/flatpickr.min.js')); ?>"></script>
        <script src="<?php echo e(asset('backEnd/assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
        <script src="<?php echo e(asset('backEnd/assets/libs/selectize/js/standalone/selectize.min.js')); ?>"></script>

    <script>

    var colors = ["#f1556c"],
    dataColors = $("#total-revenue").data("colors");
    dataColors && (colors = dataColors.split(","));
    var options = {
          
          chart: {
             height: 242,
             type: "radialBar"
          },
          plotOptions: {
             radialBar: {
                hollow: {
                   size: "65%"
                }
             }
          },
          colors: colors,
          labels: ["Delivery"]
       },
        chart = new ApexCharts(document.querySelector("#total-revenue"), options);
        chart.render();
        colors = ["#1abc9c", "#4a81d4"];
        (dataColors = $("#sales-analytics").data("colors")) && (colors = dataColors.split(","));
        options = {
           series: [{
              name: "Revenue",
              type: "column",
              data: [<?php $__currentLoopData = $monthly_sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($sale->amount); ?>, <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>]
           }, {
              name: "Sales",
              type: "line",
              data: [<?php $__currentLoopData = $monthly_sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($sale->amount); ?>, <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>]
           }],
           chart: {
              height: 378,
              type: "line",
           },
           stroke: {
              width: [2, 3]
           },
           plotOptions: {
              bar: {
                 columnWidth: "50%"
              }
           },
           colors: colors,
           dataLabels: {
              enabled: !0,
              enabledOnSeries: [1]
           },
           labels: [<?php $__currentLoopData = $monthly_sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e(date('d', strtotime($sale->date))); ?> + '-' + <?php echo e(date('m', strtotime($sale->date))); ?>+ '-' + <?php echo e(date('Y', strtotime($sale->date))); ?>, <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>],
           legend: {
              offsetY: 7
           },
           grid: {
              padding: {
                 bottom: 20
              }
           },
           fill: {
              type: "gradient",
              gradient: {
                 shade: "light",
                 type: "horizontal",
                 shadeIntensity: .25,
                 gradientToColors: void 0,
                 inverseColors: !0,
                 opacityFrom: .75,
                 opacityTo: .75,
                 stops: [0, 0, 0]
              }
           },
           yaxis: [{
              title: {
                 text: "Net Revenue"
              }
           }]
        };
        (chart = new ApexCharts(document.querySelector("#sales-analytics"), options)).render(), $("#dash-daterange").flatpickr({
           altInput: !0,
           mode: "range",
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/backEnd/admin/dashboard.blade.php ENDPATH**/ ?>