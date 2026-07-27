<?php $__env->startSection('title','Notice Manage'); ?>
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
                    <a href="<?php echo e(route('notices.create')); ?>" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Notice Manage</h4>
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
                            <th>Notice Text</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e(Str::limit($value->text, 60)); ?></td>
                            <td><?php echo e($value->order_id); ?></td>
                            <td><?php if($value->status==1): ?><span class="badge bg-soft-success text-success">Active</span> <?php else: ?> <span class="badge bg-soft-danger text-danger">Inactive</span> <?php endif; ?></td>
                            <td>
                                <div class="button-list">
                                    <?php if($value->status == 1): ?>
                                    <form method="post" action="<?php echo e(route('notices.inactive')); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                    <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button></form>
                                    <?php else: ?>
                                    <form method="post" action="<?php echo e(route('notices.active')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                    <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button></form>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('notices.edit',$value->id)); ?>" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                    <form method="post" action="<?php echo e(route('notices.destroy')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                    <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button></form>
                                </div>
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
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/js/pages/datatables.init.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/notice/index.blade.php ENDPATH**/ ?>