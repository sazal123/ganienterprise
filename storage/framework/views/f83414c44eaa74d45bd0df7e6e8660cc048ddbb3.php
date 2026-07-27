<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startPush('seo'); ?>
<meta name="description" content="<?php echo $generalsetting->meta_description; ?>" />
<meta name="keyword" content="<?php echo $generalsetting->meta_keyword; ?>" />
<meta property="og:title" content="<?php echo e($generalsetting->name); ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo e(URL::to('/')); ?>" />
<meta property="og:image" content="<?php echo e(asset($generalsetting->og_baner)); ?>" />
<meta property="og:description" content="<?php echo $generalsetting->meta_description; ?>" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/owl.carousel.min.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/owl.theme.default.min.css')); ?>" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>




<section class="gani-hero-slider" id="ganiHeroSlider">
    <?php $__empty_1 = true; $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        // Strip 'public/' prefix from image path for asset()
        $sliderImage = $slider->image;
        if (str_starts_with($sliderImage, 'public/')) {
            $sliderImage = substr($sliderImage, 7);
        }
        $subtitle = $slider->subtitle ?: 'গ্রীষ্মকালীন অফার';
        $title = $slider->title ?: '৮০%';
        $btnText = $slider->btn_text ?: 'অফার উপভোগ করুন';
    ?>
    <div class="gani-hero-slide <?php echo e($index === 0 ? 'active' : ''); ?>"
         style="background-image: url('<?php echo e(asset($sliderImage)); ?>');">
        <div class="gani-hero-gradient"></div>
        <div class="gani-hero-content">
            <h2 class="gani-hero-subtitle"><?php echo nl2br(e($subtitle)); ?></h2>
            <h1 class="gani-hero-title"><?php echo e(e($title)); ?> <span class="gani-hero-title-accent">ছাড়</span></h1>
            <a href="<?php echo e($slider->link ?? route('shop')); ?>" class="gani-hero-btn"><?php echo e($btnText); ?></a>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="gani-hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1548036328-c9fa89d128fa?q=80&w=2069&auto=format&fit=crop');">
        <div class="gani-hero-gradient"></div>
        <div class="gani-hero-content">
            <h2 class="gani-hero-subtitle">গ্রীষ্মকালীন<br>অফার</h2>
            <h1 class="gani-hero-title">৮০% <span class="gani-hero-title-accent">ছাড়</span></h1>
            <a href="<?php echo e(route('shop')); ?>" class="gani-hero-btn">অফার উপভোগ করুন</a>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($sliders->count() > 1): ?>
    <div class="gani-slider-dots">
        <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button class="gani-slider-dot <?php echo e($index === 0 ? 'active' : ''); ?>" data-slide="<?php echo e($index); ?>"></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</section>




<?php if($notices->count() > 0): ?>
<section class="gani-notice-bar">
    <div class="gani-notice-track" id="ganiNoticeTrack">
        
        <?php for($r = 0; $r < 3; $r++): ?>
            <?php $__currentLoopData = $notices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($notice->link): ?>
                    <a href="<?php echo e($notice->link); ?>" class="gani-notice-item" target="_blank"><?php echo e($notice->text); ?></a>
                <?php else: ?>
                    <span class="gani-notice-item"><?php echo e($notice->text); ?></span>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endfor; ?>
    </div>
</section>
<?php endif; ?>






<?php if($trendingProducts->count() > 0): ?>
<section class="gani-section gani-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">ট্রেন্ডিং কালেকশন</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php $__currentLoopData = $trendingProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-g5">
                    <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>






<?php if($primeProducts->count() > 0 || $newProducts->count() > 0): ?>
<section class="gani-section gani-section-white">
    <div class="container">
        
        <div class="gani-collection-tabs">
            <button class="gani-collection-tab active" data-tab="prime">
                PRIME BAGS
            </button>
            <button class="gani-collection-tab" data-tab="new">
                NEW IN TREND
            </button>
        </div>

        
        <div class="gani-collection-panel active" id="panel-prime">
            <?php if($primeProducts->count() > 0): ?>
            <div class="row g-4">
                <?php $__currentLoopData = $primeProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-g5">
                    <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?php echo e(route('collection.prime')); ?>" class="gani-view-all-btn">VIEW ALL</a>
            </div>
            <?php else: ?>
            <p class="text-center text-muted py-4">কোনো প্রাইম পণ্য পাওয়া যায়নি</p>
            <?php endif; ?>
        </div>

        
        <div class="gani-collection-panel" id="panel-new">
            <?php if($newProducts->count() > 0): ?>
            <div class="row g-4">
                <?php $__currentLoopData = $newProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-g5">
                    <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?php echo e(route('collection.new')); ?>" class="gani-view-all-btn">VIEW ALL</a>
            </div>
            <?php else: ?>
            <p class="text-center text-muted py-4">কোনো নতুন পণ্য পাওয়া যায়নি</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>




<?php if($stories->count() > 0): ?>
<section class="gani-section gani-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">STORIES THAT LEAD</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php $__currentLoopData = $stories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $story): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $thumb = $story->thumbnail;
                if ($thumb && str_starts_with($thumb, 'public/')) { $thumb = substr($thumb, 7); }
                $storyProduct = $story->product;
                $prodImg = $storyProduct && $storyProduct->image ? $storyProduct->image->image : '';
            ?>
            <div class="col-6 col-md-g5">
                <div class="gani-story-card"
                     data-video="<?php echo e(asset($story->video)); ?>"
                     data-thumb="<?php echo e($thumb ? asset($thumb) : asset('frontEnd/img/default-product.jpg')); ?>"
                     data-prod-img="<?php echo e($prodImg ? asset($prodImg) : ''); ?>"
                     data-prod-name="<?php echo e($storyProduct ? $storyProduct->name : ''); ?>"
                     data-prod-price="<?php echo e($storyProduct ? number_format($storyProduct->new_price) : '0'); ?>"
                     data-prod-old="<?php echo e($storyProduct && $storyProduct->old_price ? number_format($storyProduct->old_price) : ''); ?>"
                     data-prod-slug="<?php echo e($storyProduct ? $storyProduct->slug : '#'); ?>"
                     data-prod-id="<?php echo e($storyProduct ? $storyProduct->id : ''); ?>"
                     data-prod-stock="<?php echo e($storyProduct ? $storyProduct->stock : 0); ?>"
                     data-prod-link="<?php echo e($storyProduct ? route('product', $storyProduct->slug) : '#'); ?>"
                     data-add-to-cart="<?php echo e($storyProduct && $storyProduct->procolors->isEmpty() && $storyProduct->prosizes->isEmpty() && $storyProduct->stock > 0 ? route('cart.store') : ''); ?>">
                    <div class="gani-story-thumb-wrap">
                        <video src="<?php echo e(asset($story->video)); ?>" class="gani-story-video" muted playsinline loop preload="auto" poster="<?php echo e($thumb ? asset($thumb) : asset('frontEnd/img/default-product.jpg')); ?>"></video>
                        <div class="gani-story-play-indicator"><i class="fa-solid fa-volume-xmark"></i></div>
                    </div>
                    <div class="gani-story-info">
                        <?php if($storyProduct): ?>
                            <div class="gani-story-product-row">
                                <?php if($prodImg): ?>
                                <img src="<?php echo e(asset($prodImg)); ?>" class="gani-story-prod-img" />
                                <?php endif; ?>
                                <div class="gani-story-prod-details">
                                    <h6 class="gani-story-prod-name"><?php echo e(Str::limit($storyProduct->name, 35)); ?></h6>
                                    <span class="gani-story-prod-price">৳<?php echo e(number_format($storyProduct->new_price)); ?></span>
                                </div>
                            </div>
                            <?php if($storyProduct->procolors->isEmpty() && $storyProduct->prosizes->isEmpty() && $storyProduct->stock > 0): ?>
                            <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo e($storyProduct->id); ?>" />
                                <input type="hidden" name="qty" value="1" />
                                <button type="submit" class="gani-story-cart-btn">Add To Cart</button>
                            </form>
                            <?php else: ?>
                            <a href="<?php echo e(route('product', $storyProduct->slug)); ?>" class="gani-story-cart-btn">Add To Cart</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>




<div class="gani-video-modal" id="ganiStoryModal">
    <button class="gani-video-close" onclick="closeStoryModal()">&times;</button>
    <div class="gani-story-modal-inner">
        <div class="gani-story-modal-left">
            <video id="ganiStoryVideo" controls playsinline></video>
        </div>
        <div class="gani-story-modal-right">
            <h3 class="gani-sm-title" id="ganiSmTitle">Product Name</h3>
            <div class="gani-sm-price-row">
                <span class="gani-sm-price" id="ganiSmPrice">৳0</span>
                <span class="gani-sm-old-price" id="ganiSmOldPrice"></span>
            </div>
            <div class="gani-sm-colors" id="ganiSmColors">
                <span class="gani-sm-color-label">Color: <strong id="ganiSmColorName">Brown</strong></span>
                <div class="gani-sm-color-swatches" id="ganiSmColorSwatches"></div>
            </div>
            <form id="ganiSmCartForm" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" id="ganiSmProdId" value="" />
                <input type="hidden" name="qty" value="1" />
                <button type="submit" class="gani-sm-cart-btn" id="ganiSmCartBtn">Add To Cart</button>
            </form>
            <a href="#" class="gani-sm-view-link" id="ganiSmViewLink">View Full Details →</a>
        </div>
    </div>
</div>




<?php if($primeDropBanner): ?>
<?php
    $pdbImg = $primeDropBanner->image;
    if (str_starts_with($pdbImg, 'public/')) { $pdbImg = substr($pdbImg, 7); }
    $pdbTitle = $primeDropBanner->title ?: 'PATCHEE TOP PICKS';
    $pdbSubtitle = $primeDropBanner->subtitle ?: 'THE PRIME DROP';
    $pdbBtnText = $primeDropBanner->btn_text ?: 'View All';
    $pdbLink = $primeDropBanner->link ?: route('shop');
?>
<section class="gani-prime-drop" <?php if($pdbImg): ?> style="background-image: url('<?php echo e(asset($pdbImg)); ?>');" <?php endif; ?>>
    <div class="gani-prime-drop-overlay"></div>
    <div class="container position-relative gani-prime-drop-inner">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="gani-pd-badge">UPTO 50% OFF</span>
                <h3 class="gani-pd-title"><?php echo e($pdbTitle); ?></h3>
                <h2 class="gani-pd-subtitle"><?php echo e($pdbSubtitle); ?></h2>
                <a href="<?php echo e($pdbLink); ?>" class="gani-pd-btn"><?php echo e($pdbBtnText); ?></a>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end gap-3">
                <?php $__currentLoopData = $primeDropProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pdp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('product', $pdp->slug)); ?>" class="gani-pd-product">
                        <img src="<?php echo e(asset($pdp->image ? $pdp->image->image : 'frontEnd/img/default-product.jpg')); ?>"
                             alt="<?php echo e($pdp->name); ?>" class="gani-pd-product-img" />
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>




<?php if($homeCategories->count() > 0): ?>
<section class="gani-section gani-section-white category-wise">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">Trending Categories</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php $__currentLoopData = $homeCategories->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $catImg = $category->image;
                if ($catImg && str_starts_with($catImg, 'public/')) {
                    $catImg = substr($catImg, 7);
                }
            ?>
            <div class="col-6 col-md-g5">
                <a href="<?php echo e(route('category', $category->slug)); ?>" class="gani-cat-card">
                    <div class="gani-cat-img-wrap">
                        <?php if($category->image): ?>
                            <img src="<?php echo e(asset($catImg)); ?>" alt="<?php echo e($category->name); ?>" class="w-100 h-100 object-fit-cover gani-cat-img" />
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #C9A84C, #8B6914);">
                                <span class="text-white fw-bold" style="font-size:32px; font-family:'Playfair Display',serif;"><?php echo e(mb_substr($category->name, 0, 1)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="gani-cat-overlay">
                            <span class="gani-cat-shop-btn">শপিং করুন</span>
                        </div>
                    </div>
                    <h5 class="gani-cat-name"><?php echo e($category->name); ?></h5>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo e(route('shop')); ?>" class="gani-view-all-btn">View All</a>
        </div>
    </div>
</section>
<?php endif; ?>




<?php if($spotlightCategories->count() > 0): ?>
<section class="gani-section" style="background: #f8f8f6;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">Tshirts & Clothing</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php $__currentLoopData = $spotlightCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $catImg = $cat->image;
                if ($catImg && str_starts_with($catImg, 'public/')) { $catImg = substr($catImg, 7); }
            ?>
            <div class="col-6 col-md-g5">
                <a href="<?php echo e(route('category', $cat->slug)); ?>" class="gani-spotlight-card gani-clothing-card" style="border-radius:8px;overflow:hidden;">
                    <div style="position:relative;aspect-ratio:3/4;overflow:hidden;">
                        <?php if($cat->image): ?>
                            <img src="<?php echo e(asset($catImg)); ?>" alt="<?php echo e($cat->name); ?>" class="w-100 h-100 object-fit-cover gani-clothing-img" />
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#C9A84C,#8B6914);">
                                <span class="text-white fw-bold" style="font-size:40px;font-family:'Playfair Display',serif;"><?php echo e(mb_substr($cat->name,0,1)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 50%);"></div>
                        <div style="position:absolute;bottom:0;left:0;right:0;padding:20px;">
                            <h3 class="gani-clothing-name"><?php echo e($cat->name); ?></h3>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo e(route('shop')); ?>" class="gani-view-all-btn">View All</a>
        </div>
    </div>
</section>
<?php endif; ?>





<section class="gani-section gani-trust-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-truck gani-trust-icon"></i>
                    <h5 class="gani-trust-title">ফ্রি শিপিং</h5>
                    <p class="gani-trust-text">৫০০০ টাকার বেশি অর্ডারে</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-rotate-left gani-trust-icon"></i>
                    <h5 class="gani-trust-title">ফ্রি রিটার্ন</h5>
                    <p class="gani-trust-text">৩০ দিনের রিটার্ন পলিসি</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-shield-halved gani-trust-icon"></i>
                    <h5 class="gani-trust-title">নিরাপদ পেমেন্ট</h5>
                    <p class="gani-trust-text">শতভাগ নিরাপদ পেমেন্ট</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-headset gani-trust-icon"></i>
                    <h5 class="gani-trust-title">সার্বক্ষণিক সাপোর্ট</h5>
                    <p class="gani-trust-text">নিবেদিত কাস্টমার সার্ভিস</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontEnd/js/jquery.syotimer.min.js')); ?>"></script>

<script>
    // Story product modal
    function openStoryModal(card) {
        var modal = document.getElementById('ganiStoryModal');
        var video = document.getElementById('ganiStoryVideo');
        var videoSrc = card.getAttribute('data-video');
        var thumbSrc = card.getAttribute('data-thumb');
        video.src = videoSrc;
        // If video fails to load, show thumbnail as poster
        video.setAttribute('poster', thumbSrc);
        video.onerror = function() {
            video.setAttribute('poster', thumbSrc);
        };
        document.getElementById('ganiSmTitle').textContent = card.getAttribute('data-prod-name');
        document.getElementById('ganiSmPrice').textContent = '৳' + card.getAttribute('data-prod-price');
        var oldEl = document.getElementById('ganiSmOldPrice');
        var oldPrice = card.getAttribute('data-prod-old');
        oldEl.textContent = oldPrice ? '৳' + oldPrice : '';
        oldEl.style.display = oldPrice ? 'inline' : 'none';
        document.getElementById('ganiSmProdId').value = card.getAttribute('data-prod-id');
        var cartForm = document.getElementById('ganiSmCartForm');
        var cartAction = card.getAttribute('data-add-to-cart');
        if (cartAction) {
            cartForm.action = cartAction;
            document.getElementById('ganiSmCartBtn').style.display = 'block';
        } else {
            cartForm.action = '';
            document.getElementById('ganiSmCartBtn').style.display = 'none';
        }
        document.getElementById('ganiSmViewLink').href = card.getAttribute('data-prod-link');
        modal.classList.add('active');
        video.load();
        video.play().catch(function() {});
    }
    function closeStoryModal() {
        var modal = document.getElementById('ganiStoryModal');
        var video = document.getElementById('ganiStoryVideo');
        modal.classList.remove('active');
        video.pause();
        video.src = '';
    }
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('ganiStoryModal');
        if (modal && e.target === modal) closeStoryModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeStoryModal();
    });
    // Bind story card clicks — open modal
    document.querySelectorAll('.gani-story-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.gani-story-cart-btn') || e.target.closest('form')) return;
            openStoryModal(this);
        });
    });

    // Auto-play videos when in viewport
    (function() {
        var videos = document.querySelectorAll('.gani-story-video');
        if (!videos.length || !window.IntersectionObserver) return;
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                var video = entry.target;
                if (entry.isIntersecting) {
                    video.play().catch(function(){});
                } else {
                    video.pause();
                }
            });
        }, { threshold: 0.5 });
        videos.forEach(function(v) { observer.observe(v); });
    })();

    // Hero Slider — vanilla JS
    (function() {
        var slider = document.getElementById('ganiHeroSlider');
        if (!slider) return;
        var slides = slider.querySelectorAll('.gani-hero-slide');
        var dots = slider.querySelectorAll('.gani-slider-dot');
        if (slides.length === 0) return;
        var current = 0;
        var interval;

        // Ensure first slide is active
        slides.forEach(function(s, i) {
            s.classList.toggle('active', i === 0);
        });
        dots.forEach(function(d, i) {
            d.classList.toggle('active', i === 0);
        });

        function goToSlide(index) {
            if (index < 0 || index >= slides.length) return;
            slides.forEach(function(s) { s.classList.remove('active'); });
            dots.forEach(function(d) { d.classList.remove('active'); });
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
            current = index;
        }

        function nextSlide() {
            if (slides.length < 2) return;
            goToSlide((current + 1) % slides.length);
        }

        function startAutoPlay() {
            stopAutoPlay();
            if (slides.length < 2) return;
            interval = setInterval(nextSlide, 5000);
        }

        function stopAutoPlay() {
            if (interval) { clearInterval(interval); interval = null; }
        }

        // Dot clicks
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                var idx = parseInt(this.getAttribute('data-slide'));
                goToSlide(idx);
                startAutoPlay();
            });
        });

        // Pause on hover
        slider.addEventListener('mouseenter', stopAutoPlay);
        slider.addEventListener('mouseleave', startAutoPlay);

        startAutoPlay();
    })();

    $(document).ready(function() {
        // Reviews carousel
        if ($(".reviews-carousel").length) {
            $(".reviews-carousel").owlCarousel({
                margin: 15,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: { items: 2, nav: false },
                    600: { items: 3, nav: false },
                    1000: { items: 5, nav: false },
                },
            });
        }
    });

    // Color image swap on hover/click
    document.querySelectorAll('.gani-swatch-btn').forEach(function(btn) {
        btn.addEventListener('mouseenter', function() {
            var card = this.closest('.gani-product-card');
            var mainImg = card ? card.querySelector('.gani-product-img') : null;
            var swapUrl = this.getAttribute('data-swap-img');
            if (mainImg && swapUrl) {
                mainImg.setAttribute('src', swapUrl);
            }
        });
        btn.addEventListener('mouseleave', function() {
            var card = this.closest('.gani-product-card');
            var mainImg = card ? card.querySelector('.gani-product-img') : null;
            var origUrl = mainImg ? mainImg.getAttribute('data-main-img') : null;
            if (mainImg && origUrl) {
                mainImg.setAttribute('src', origUrl);
            }
        });
    });

    // Collection tabs
    document.querySelectorAll('.gani-collection-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            document.querySelectorAll('.gani-collection-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.gani-collection-panel').forEach(function(p) { p.classList.remove('active'); });
            this.classList.add('active');
            var panel = document.getElementById('panel-' + target);
            if (panel) panel.classList.add('active');
        });
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/pages/index.blade.php ENDPATH**/ ?>