<?php $__env->startSection('title','Story Create'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right"><a href="<?php echo e(route('stories.index')); ?>" class="btn btn-primary rounded-pill">Manage</a></div>
                <h4 class="page-title">Story Create</h4>
            </div>
        </div>
    </div>
   <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('stories.store')); ?>" method="POST" enctype="multipart/form-data" class="row">
                    <?php echo csrf_field(); ?>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo e(old('title')); ?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="product_id" class="form-label">Linked Product</label>
                            <select class="form-control" name="product_id">
                                <option value="">Select product...</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="order_id" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="order_id" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="video" class="form-label">Video *</label>
                            <input type="file" class="form-control" name="video" accept="video/*" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail Image</label>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch"><input type="checkbox" value="1" name="status" checked><span class="slider round"></span></label>
                        </div>
                    </div>
                    <div><input type="submit" class="btn btn-success" value="Submit"></div>
                </form>
            </div>
        </div>
    </div>
   </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/story/create.blade.php ENDPATH**/ ?>