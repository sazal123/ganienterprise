<?php $__env->startSection('title','Stories Manage'); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?php echo e(route('stories.create')); ?>" class="btn btn-primary rounded-pill">Create</a>
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
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($v->title); ?></td>
                            <td><?php echo e($v->product ? $v->product->name : 'N/A'); ?></td>
                            <td><?php if($v->thumbnail): ?><img src="<?php echo e(asset($v->thumbnail)); ?>" style="height:50px;"><?php endif; ?></td>
                            <td><?php echo e($v->order_id); ?></td>
                            <td><?php if($v->status==1): ?><span class="badge bg-soft-success text-success">Active</span><?php else: ?><span class="badge bg-soft-danger text-danger">Inactive</span><?php endif; ?></td>
                            <td>
                                <a href="<?php echo e(route('stories.edit',$v->id)); ?>" class="btn btn-xs btn-primary"><i class="fe-edit-1"></i></a>
                                <form method="post" action="<?php echo e(route('stories.destroy')); ?>" class="d-inline"><?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($v->id); ?>" name="hidden_id">
                                    <button type="submit" class="btn btn-xs btn-danger delete-confirm"><i class="mdi mdi-close"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/js/pages/datatables.init.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/story/index.blade.php ENDPATH**/ ?>