
<?php $__env->startSection('title','Order Success'); ?>
<?php $__env->startSection('content'); ?>
<section class="customer-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                <div class="success-img">
                    <img src="<?php echo e(asset('frontEnd/images/order-success.png')); ?>" alt="">
                </div>
                <div class="success-title">
                    <h2>আপনার অর্ডারটি আমাদের কাছে সফলভাবে পৌঁছেছে, কিছুক্ষনের মধ্যে আমাদের একজন প্রতিনিধি আপনার নাম্বারে কল করবেন </h2>
                </div>

                <h5 class="my-3">Your Order Details</h5>
                <div class="success-table">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <p>Invoice ID</p>
                                    <p><strong><?php echo e($order->invoice_id); ?></strong></p>
                                </td>
                                <td>
                                    <p>Date</p>
                                    <p><strong><?php echo e($order->created_at->format('d-m-y')); ?></strong></p>
                                </td>
                                <td>
                                    <p>Phone</p>
                                    <p><strong><?php echo e($order->shipping?$order->shipping->phone:''); ?></strong></p>
                                </td>
                                <td>
                                    <p>Total</p>
                                    <p><strong>৳ <?php echo e($order->amount); ?></strong></p>
                                </td>
                            </tr>
                            <tr>
                                <?php 
                                    $payments = App\Models\Payment::where('order_id',$order->id)->first();
                                ?>
                                <td colspan="4">
                                    <p>Payment Method</p>
                                    <p><strong><?php echo e($payments->payment_method); ?></strong></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- success table -->
                <h5 class="my-4">Pay with cash upon delivery</h5>
                <div class="success-table">
                    <h6 class="mb-3">Order Delivery</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->orderdetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <p><?php echo e($value->product_name); ?> x <?php echo e($value->qty); ?> <br> <?php if($value->product_size): ?> <small>Size: <?php echo e($value->product_size); ?></small> <?php endif; ?>   <?php if($value->product_color): ?> <small>Color: <?php echo e($value->product_color); ?></small> <?php endif; ?></p>
                                    
                                </td>
                                <td><p><strong>৳ <?php echo e($value->sale_price); ?></strong></p></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <th  class="text-end px-4">Net Total</th>
                                <td><strong id="net_total">৳<?php echo e($order->amount-$order->shipping_charge); ?></strong></td>
                            </tr>
                            <tr>
                                <th  class="text-end px-4">Shipping Cost</th>
                                <td>
                                    <strong id="cart_shipping_cost">৳<?php echo e($order->shipping_charge); ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <th  class="text-end px-4">Grand Total</th>
                                <td>
                                    <strong id="grand_total">৳<?php echo e($order->amount); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <h5 class="my-4">Billing Address</h5>
                                    <p><?php echo e($order->shipping?$order->shipping->name:''); ?></p>
                                    <p><?php echo e($order->shipping?$order->shipping->phone:''); ?></p>
                                    <p><?php echo e($order->shipping?$order->shipping->address:''); ?></p>
                                    <p><?php echo e($order->shipping?$order->shipping->area:''); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- success table -->
                <a href="<?php echo e(route('home')); ?>" class=" my-5 btn btn-primary">Go To Home</a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/')); ?>/js/parsley.min.js"></script>
<script src="<?php echo e(asset('frontEnd/')); ?>/js/form-validation.init.js"></script>
<!-- Data Layer Script for Order Success Event -->
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'event': 'purchase',
        'transactionId': '<?php echo e($order->invoice_id); ?>',
        'transactionTotal': <?php echo e($order->amount); ?>,
        'transactionProducts': [
            <?php $__currentLoopData = $order->orderdetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                {
                    'name': '<?php echo e($detail->product_name); ?>',
                    'id': '<?php echo e($detail->product_id); ?>',  // Assuming you have a product_id
                    'price': <?php echo e($detail->sale_price); ?>,
                    'quantity': <?php echo e($detail->qty); ?>,
                    'size': '<?php echo e($detail->product_size); ?>',
                    'color': '<?php echo e($detail->product_color); ?>'
                } <?php if(!$loop->last): ?>,<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        'paymentMethod': '<?php echo e($payments->payment_method); ?>',
        'shippingCost': <?php echo e($order->shipping_charge); ?>

    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/customer/order_success.blade.php ENDPATH**/ ?>