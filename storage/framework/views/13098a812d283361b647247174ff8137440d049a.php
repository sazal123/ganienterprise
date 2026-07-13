<?php $product_discount = 0; ?>
<?php $__currentLoopData = $cartinfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
  <td><img height="35" src="<?php echo e(asset($value->options->image)); ?>" style="border-radius:4px;object-fit:cover;width:45px;" /></td>
  <td class="fw-semibold"><?php echo e($value->name); ?></td>
  <td>
    <?php if($value->options->product_color || $value->options->product_size): ?>
      <span class="badge bg-info" style="font-size:11px;">
        <?php echo e($value->options->product_color ?: ''); ?><?php echo e($value->options->product_color && $value->options->product_size ? ' / ' : ''); ?><?php echo e($value->options->product_size ?: ''); ?>

      </span>
    <?php else: ?>
      <span class="text-muted">—</span>
    <?php endif; ?>
  </td>
  <td>
    <div class="input-group input-group-sm" style="max-width:110px;">
      <button class="btn btn-outline-secondary cart_decrement" data-id="<?php echo e($value->rowId); ?>">-</button>
      <input type="text" class="form-control text-center" value="<?php echo e($value->qty); ?>" readonly />
      <button class="btn btn-outline-secondary cart_increment" data-id="<?php echo e($value->rowId); ?>">+</button>
    </div>
  </td>
  <td>৳<?php echo e($value->price); ?></td>
  <td><input type="number" class="form-control form-control-sm product_discount" style="width:65px;" value="<?php echo e($value->options->product_discount); ?>" placeholder="0" data-id="<?php echo e($value->rowId); ?>" /></td>
  <td class="fw-semibold">৳<?php echo e(($value->price - $value->options->product_discount)*$value->qty); ?></td>
  <td><button type="button" class="btn btn-danger btn-sm cart_remove" data-id="<?php echo e($value->rowId); ?>"><i class="fa fa-times"></i></button></td>
</tr>
<?php
  $product_discount += $value->options->product_discount*$value->qty;
  Session::put('product_discount',$product_discount);
?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/order/cart_content.blade.php ENDPATH**/ ?>