<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startPush('seo'); ?>

<meta name="description" content="<?php echo $generalsetting->meta_description; ?>" />
<meta name="keyword" content="<?php echo $generalsetting->meta_keyword; ?>" />

		<!-- Open Graph data -->
<meta property="og:title" content="<?php echo e($generalsetting->name); ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo e(URL::to('/')); ?>" />
<meta property="og:image" content="<?php echo e(asset($generalsetting->og_baner)); ?>" />
<meta property="og:description" content="<?php echo $generalsetting->meta_description; ?>" />
<?php $__env->stopPush(); ?> <?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/owl.carousel.min.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/owl.theme.default.min.css')); ?>" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css" rel="stylesheet" />
<?php $__env->stopPush(); ?> <?php $__env->startSection('content'); ?>

<!-- Main Slider Section -->
<section class="hero-slider-section">
    <div class="main_slider owl-carousel">
        <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="slider-item">
                <img src="<?php echo e(asset($value->image)); ?>" alt="" />
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<!-- slider end -->

<!-- Top Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="section-title mb-4">Top Categories</h3>
            </div>
            <div class="col-12">
                <div class="category-carousel owl-carousel">
                    <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="category-item">
                            <a href="<?php echo e(route('category', $value->slug)); ?>" class="category-link">
                                <img src="<?php echo e(asset($value->image)); ?>" alt="<?php echo e($value->name); ?>" class="category-img" />
                                <p class="category-name"><?php echo e($value->name); ?></p>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    $hotDealEndDate = $generalsetting->hot_deal_end_date.'T23:59:59';
    $flashSaleEndDate = $generalsetting->flash_sale_end_date.'T23:59:59';
    $isHotDealActive = $hotDealEndDate && \Carbon\Carbon::parse($hotDealEndDate)->isFuture();
    $isFlashSaleActive = $flashSaleEndDate && \Carbon\Carbon::parse($flashSaleEndDate)->isFuture();
?>

<!-- Flash Sales Section -->
<?php if($isFlashSaleActive): ?>
<section class="flash-sales-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header-wrapper">
                    <h2 class="section-title">Flash Sales</h2>
                    <div class="offer_timer" id="flash_sale_timer"></div>
                </div>
            </div>
            <div class="col-12">
                <div class="products-grid">
                    <?php $__currentLoopData = $flas_sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <?php if($value->old_price): ?>
                                <div class="discount-badge">
                                    <?php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) ?>
                                    <?php echo e(number_format($discount, 0)); ?>%
                                </div>
                                <?php endif; ?>
                                <a href="<?php echo e(route('product', $value->slug)); ?>" class="product-img-link">
                                    <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>"
                                        alt="<?php echo e($value->name); ?>" class="product-image" />
                                </a>
                                <?php if($value->stock < 1): ?>
                                <div class="stock-out-overlay">STOCK OUT</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h5 class="product-name">
                                    <a href="<?php echo e(route('product', $value->slug)); ?>"><?php echo e(Str::limit($value->name, 60)); ?></a>
                                </h5>
                                <p class="product-sold">Sold <?php echo e($value->sold??0); ?></p>
                                <div class="product-price">
                                    <?php if($value->old_price): ?>
                                     <span class="old-price">৳ <?php echo e($value->old_price); ?></span>
                                    <?php endif; ?>
                                    <span class="new-price">৳ <?php echo e($value->new_price); ?></span>
                                </div>
                            </div>
                            <div class="product-action">
                                <?php if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1)): ?>
                                    <a href="<?php echo e(route('product', $value->slug)); ?>" class="btn-order">অর্ডার করুন</a>
                                <?php else: ?>
                                    <form action="<?php echo e(route('cart.store')); ?>" method="POST" class="w-100">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="col-12 text-center mt-4">
               <a href="<?php echo e(route('flashsales')); ?>" class="btn-view-more">View More</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Hot Deals Section -->
<?php if($isHotDealActive): ?>
<section class="hot-deals-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header-wrapper">
                    <h2 class="section-title">Hot Deals</h2>
                    <div class="offer_timer" id="simple_timer"></div>
                </div>
            </div>
            <div class="col-12">
                <div class="products-grid">
                    <?php $__currentLoopData = $hotdeal_top; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <?php if($value->old_price): ?>
                                <div class="discount-badge">
                                    <?php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) ?>
                                    <?php echo e(number_format($discount, 0)); ?>%
                                </div>
                                <?php endif; ?>
                                <a href="<?php echo e(route('product', $value->slug)); ?>" class="product-img-link">
                                    <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>"
                                        alt="<?php echo e($value->name); ?>" class="product-image" />
                                </a>
                                <?php if($value->stock < 1): ?>
                                <div class="stock-out-overlay">STOCK OUT</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h5 class="product-name">
                                    <a href="<?php echo e(route('product', $value->slug)); ?>"><?php echo e(Str::limit($value->name, 60)); ?></a>
                                </h5>
                                <div class="product-price">
                                    <?php if($value->old_price): ?>
                                     <span class="old-price">৳ <?php echo e($value->old_price); ?></span>
                                    <?php endif; ?>
                                    <span class="new-price">৳ <?php echo e($value->new_price); ?></span>
                                </div>
                            </div>
                            <div class="product-action">
                                <?php if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1)): ?>
                                    <a href="<?php echo e(route('product', $value->slug)); ?>" class="btn-order">অর্ডার করুন</a>
                                <?php else: ?>
                                    <form action="<?php echo e(route('cart.store')); ?>" method="POST" class="w-100">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="col-12 text-center mt-4">
               <a href="<?php echo e(route('hotdeals')); ?>" class="btn-view-more">View More</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Ads -->
<?php if($sliderbottomads): ?>
<section class="ads-section py-5">
    <div class="container">
        <div class="row">
            <?php $__currentLoopData = $sliderbottomads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bottomAds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 mb-4">
                <a href="<?php echo e($bottomAds->link); ?>?sold=show" class="ads-link">
                    <img class="img-fluid w-100" src="<?php echo e($bottomAds->image); ?>" alt="Advertisement"/>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- All Products Section -->
<?php if($generalsetting->show_all_products): ?>
<section class="all-products-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title mb-4">All Products</h2>
            </div>
            <div class="col-12">
                <div class="products-grid">
                    <?php $__currentLoopData = $all_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                             <?php if($value->old_price): ?>
                            <div class="discount-badge">
                               <?php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) ?>
                                <?php echo e(number_format($discount,0)); ?>%
                            </div>
                            <?php endif; ?>
                            <a href="<?php echo e(route('product',$value->slug)); ?>" class="product-img-link">
                                <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>" alt="<?php echo e($value->name); ?>" class="product-image" />
                            </a>
                            <?php if($value->stock < 1): ?>
                            <div class="stock-out-overlay">STOCK OUT</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h5 class="product-name">
                                <a href="<?php echo e(route('product',$value->slug)); ?>"><?php echo e(Str::limit($value->name,60)); ?></a>
                            </h5>
                            <div class="product-price">
                                <?php if($value->old_price): ?>
                                <span class="old-price">৳ <?php echo e($value->old_price); ?></span>
                                <?php endif; ?>
                                <span class="new-price">৳ <?php echo e($value->new_price); ?></span>
                            </div>
                        </div>
                        <div class="product-action">
                             <?php if(! $value->prosizes->isEmpty() || ! $value->procolors->isEmpty() || ($value->stock < 1)): ?>
                            <a href="<?php echo e(route('product',$value->slug)); ?>" class="btn-order">অর্ডার করুন</a>
                            <?php else: ?>
                            <form action="<?php echo e(route('cart.store')); ?>" method="POST" class="w-100">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                <input type="hidden" name="qty" value="1" />
                                <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Category-wise Products -->
<?php if($generalsetting->show_category_wise_products): ?>
    <?php $__currentLoopData = $homeproducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homecat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <section class="category-products-section py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="section-title mb-4"><?php echo e($homecat->name); ?></h2>
                    </div>
                    <div class="col-12">
                        <div class="products-grid">
                            <?php $__currentLoopData = $homecat->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               <div class="product-card">
                                <div class="product-image-wrapper">
                                    <?php if($value->old_price): ?>
                                    <div class="discount-badge">
                                        <?php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) ?>
                                        <?php echo e(number_format($discount, 0)); ?>%
                                    </div>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('product', $value->slug)); ?>" class="product-img-link">
                                        <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>"
                                            alt="<?php echo e($value->name); ?>" class="product-image" />
                                    </a>
                                    <?php if($value->stock < 1): ?>
                                    <div class="stock-out-overlay">STOCK OUT</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-name">
                                        <a href="<?php echo e(route('product', $value->slug)); ?>"><?php echo e(Str::limit($value->name, 60)); ?></a>
                                    </h5>
                                    <div class="product-price">
                                        <?php if($value->old_price): ?>
                                         <span class="old-price">৳ <?php echo e($value->old_price); ?></span>
                                        <?php endif; ?>
                                        <span class="new-price">৳ <?php echo e($value->new_price); ?></span>
                                    </div>
                                </div>
                                <div class="product-action">
                                    <?php if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1)): ?>
                                        <a href="<?php echo e(route('product', $value->slug)); ?>" class="btn-order">অর্ডার করুন</a>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('cart.store')); ?>" method="POST" class="w-100">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                            <input type="hidden" name="qty" value="1" />
                                            <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <a href="<?php echo e(route('category', $homecat->slug)); ?>" class="btn-view-more">View More</a>
                    </div>
                </div>
            </div>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<!-- Campaign Ads -->
<?php if($campaognads): ?>
<section class="campaign-ads-section py-5">
    <div class="container">
        <div class="row">
            <?php $__currentLoopData = $campaognads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaignAds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 mb-4">
                <a href="<?php echo e($campaignAds->link); ?>?sold=show" class="ads-link">
                    <img class="img-fluid w-100" src="<?php echo e($campaignAds->image); ?>" alt="Campaign"/>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Customer Reviews Section -->
<?php if($reviews->count()>0): ?>
<section class="reviews-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">সম্মানীত কাষ্টমারদের পজিটিভ রিভিউ</h2>
            </div>
            <div class="col-12">
                <div class="reviews-carousel owl-carousel">
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="review-item">
                        <img class="img-fluid w-100" src="<?php echo e(asset($review->image)); ?>" alt="Customer Review"/>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Footer Ads -->
<section class="footer-ads-section py-5">
    <div class="container">
        <div class="row">
            <?php $__currentLoopData = $footertopads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerAds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 mb-4">
                <a href="<?php echo e($footerAds->link); ?>?sold=show" class="ads-link">
                    <img class="img-fluid w-100" src="<?php echo e($footerAds->image); ?>" alt="Footer Advertisement"/>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontEnd/js/jquery.syotimer.min.js')); ?>"></script>

<script>
    $(document).ready(function() {
        // Main slider
        $(".main_slider").owlCarousel({
            items: 1,
            loop: true,
            dots: false,
            autoplay: true,
            nav: true,
            autoplayHoverPause: true,
            margin: 0,
            mouseDrag: true,
            smartSpeed: 8000,
            autoplayTimeout: 3000,
            animateOut: "fadeOutRight",
            animateIn: "slideInLeft",
            navText: ["<i class='fa-solid fa-angle-left'></i>", "<i class='fa-solid fa-angle-right'></i>"],
        });

        // Category carousel
        $(".category-carousel").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true,
                },
                600: {
                    items: 4,
                    nav: false,
                },
                1000: {
                    items: 7,
                    nav: true,
                },
            },
        });

        // Reviews carousel
        $(".reviews-carousel").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: false,
                },
            },
        });
    });

    // Flash sale timer
    $("#flash_sale_timer").syotimer({
        date: new Date("<?php echo e($generalsetting->flash_sale_end_date); ?>T23:59:59"),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: false,
    });

    // Hot deal timer
    $("#simple_timer").syotimer({
        date: new Date("<?php echo e($generalsetting->hot_deal_end_date); ?>T23:59:59"),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: false,
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/frontEnd/layouts/pages/index.blade.php ENDPATH**/ ?>