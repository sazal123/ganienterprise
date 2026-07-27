<div class="gani-product-card">
    <div class="gani-product-img-wrap">
        <a href="<?php echo e(route('product', $product->slug)); ?>" class="d-block w-100 h-100">
            <?php $mainImage = $product->image ? $product->image->image : 'frontEnd/img/default-product.jpg'; ?>
            <img src="<?php echo e(asset($mainImage)); ?>"
                 alt="<?php echo e($product->name); ?>"
                 class="w-100 h-100 object-fit-cover gani-product-img"
                 data-main-img="<?php echo e(asset($mainImage)); ?>" />
        </a>

        
        <?php if($product->old_price && $product->old_price > $product->new_price): ?>
            <?php $discount = round((($product->old_price - $product->new_price) / $product->old_price) * 100); ?>
            <span class="gani-badge gani-badge-dark"><?php echo e($discount); ?>% ছাড়</span>
        <?php else: ?>
            <span class="gani-badge gani-badge-gold">নতুন</span>
        <?php endif; ?>

        
        <div class="gani-product-hover">
            <?php if($product->procolors->isEmpty() && $product->prosizes->isEmpty() && $product->stock > 0): ?>
                <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo e($product->id); ?>" />
                    <input type="hidden" name="qty" value="1" />
                    <button type="submit" class="gani-add-cart-btn">কার্টে যোগ করুন</button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('product', $product->slug)); ?>" class="gani-add-cart-btn">কার্টে যোগ করুন</a>
            <?php endif; ?>
        </div>

        
        <?php if($product->stock < 1): ?>
        <div class="gani-stock-overlay">স্টক আউট</div>
        <?php endif; ?>
    </div>

    <div class="gani-product-info">
        <a href="<?php echo e(route('product', $product->slug)); ?>">
            <h6 class="gani-product-name"><?php echo e(Str::limit($product->name, 50)); ?></h6>
        </a>
        <div class="gani-product-price">
            <?php if($product->old_price && $product->old_price > $product->new_price): ?>
                <span class="gani-old-price">৳<?php echo e(number_format($product->old_price)); ?></span>
            <?php endif; ?>
            <span class="gani-new-price">৳<?php echo e(number_format($product->new_price)); ?></span>
        </div>

        
        <?php if($product->procolors && $product->procolors->count() > 0): ?>
        <div class="gani-color-swatches">
            <?php $__currentLoopData = $product->procolors->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($pc->color): ?>
                    <?php
                        $colorImage = $product->images->where('color_id', $pc->color_id)->first();
                        $thumbUrl = asset($colorImage ? $colorImage->image : $mainImage);
                    ?>
                    <button type="button" class="gani-swatch-link gani-swatch-btn"
                            data-swap-img="<?php echo e($thumbUrl); ?>"
                            title="<?php echo e($pc->color->colorName ?? ''); ?>">
                        <img src="<?php echo e($thumbUrl); ?>"
                             alt="<?php echo e($pc->color->colorName ?? ''); ?>"
                             class="gani-swatch-img" />
                    </button>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($product->procolors->count() > 5): ?>
                <a href="<?php echo e(route('product', $product->slug)); ?>" class="gani-swatch-more">+<?php echo e($product->procolors->count() - 5); ?></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/frontEnd/layouts/pages/_product_card_folks.blade.php ENDPATH**/ ?>