
<?php $__env->startSection('title','Customer Account'); ?>
<?php $__env->startSection('content'); ?>
<section class="customer-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="customer-sidebar">
                    <?php echo $__env->make('frontEnd.layouts.customer.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="customer-content">
                    <h5 class="account-title">My Account</h5>
                    <table class="table">
                        <?php
                            $customer = \App\Models\Customer::with('cust_area')->find(Auth::guard('customer')->user()->id);
                        ?>
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td><?php echo e($customer->name); ?></td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td><?php echo e($customer->phone); ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><?php echo e($customer->email); ?></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td><?php echo e($customer->address); ?></td>
                            </tr>
                            <tr>
                                <td>Disctrict</td>
                                <td><?php echo e($customer->district); ?></td>
                            </tr>
                            <tr>
                                <td>Area</td>
                                <td><?php echo e($customer->cust_area?$customer->cust_area->area_name:''); ?></td>
                            </tr>
                            <tr>
                                <td>Image</td>
                                <td><img src="<?php echo e(asset($customer->image)); ?>" alt="" class="backend_img"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/')); ?>/js/parsley.min.js"></script>
<script src="<?php echo e(asset('frontEnd/')); ?>/js/form-validation.init.js"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/frontEnd/layouts/customer/account.blade.php ENDPATH**/ ?>