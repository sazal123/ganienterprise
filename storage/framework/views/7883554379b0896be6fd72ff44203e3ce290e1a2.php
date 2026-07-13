
<?php $__env->startSection('title','Customer Profile'); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(asset('backEnd/assets/libs/select2/css/select2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?php echo e(route('customers.index')); ?>" class="btn btn-primary rounded-pill">Customer List</a>
                    <form method="post" action="<?php echo e(route('customers.adminlog')); ?>" class="d-inline" target="_blank">
                        <?php echo csrf_field(); ?>
                    <input type="hidden" value="<?php echo e($profile->id); ?>" name="hidden_id">        
                    <button type="button" class="btn btn-info rounded-pill change-confirm" title="Login as customer"><i class="fe-log-in"></i> Login</button></form>
                </div>
                <h4 class="page-title">Customer Profile</h4>
            </div>
        </div>
    </div>  
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-4 col-xl-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="<?php echo e(asset($profile->image)); ?>" class="rounded-circle avatar-lg img-thumbnail"
                    alt="profile-image">

                    <h4 class="mb-0"><?php echo e($profile->name); ?></h4>

                    <a href="tel:<?php echo e($profile->phone); ?>" class="btn btn-success btn-xs waves-effect mb-2 waves-light">Call</a>
                    <a href="mailto:<?php echo e($profile->email); ?>" class="btn btn-danger btn-xs waves-effect mb-2 waves-light">Email</a>

                    <div class="text-start mt-3">
                        <h4 class="font-13 text-uppercase">About Me :</h4>
                        <table class="table">
                            <tbody>
                            <tr class="text-muted mb-2 font-13">
                                <td>Full Name </td>
                                <td class="ms-2"><?php echo e($profile->name); ?></td>
                            </tr>

                            <tr class="text-muted mb-2 font-13">
                                <td>Mobile </td>
                                <td class="ms-2"><?php echo e($profile->phone); ?></td>
                            </tr>

                            <tr class="text-muted mb-2 font-13">
                                <td>Email </td> 
                                <td class="ms-2"><?php echo e($profile->email); ?></td>
                            </tr>

                            <tr class="text-muted mb-1 font-13">
                                <td>Address </td> 
                                <td class="ms-2"><?php echo e($profile->address); ?></td>
                            </tr>
                            <tr class="text-muted mb-1 font-13">
                                <td>District </td> 
                                <td class="ms-2"><?php echo e($profile->district); ?></td>
                            </tr>
                            <tr class="text-muted mb-1 font-13">
                                <td>Upzlila </td> 
                                <td class="ms-2"><?php echo e($profile->area); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end card -->

        </div> <!-- end col-->

        <div class="col-lg-8 col-xl-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-fill navtab-bg">
                        
                        <li class="nav-item mt-2">
                            <a href="#order" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                               Order
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="order">
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Invoice</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $profile->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($value->invoice_id); ?></td>
                                        <td><?php echo e($value->shipping?$value->shipping->name:''); ?></td>
                                        <td><?php echo e(date('d-m-Y', strtotime($value->created_at))); ?> ,<?php echo e(date('h:i a', strtotime($value->created_at))); ?></td>
                                        <td>৳<?php echo e($value->amount); ?></td>
                                         <td><?php echo e($value->status?$value->status->name:''); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- end  item-->
                        <!-- end  item-->
                    </div> <!-- end tab-content -->
                </div>
            </div> <!-- end card-->

        </div> <!-- end col -->
    </div>
    <!-- end row-->

</div> <!-- container -->

</div> <!-- content -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
<script src="{{asset('backEnd/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/assets/js/pages/form-advanced.init.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/customer/profile.blade.php ENDPATH**/ ?>