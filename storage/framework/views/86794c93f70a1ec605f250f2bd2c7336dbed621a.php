<?php $__env->startSection('title', $details->name); ?>
<?php $__env->startPush('seo'); ?>
<meta name="app-url" content="<?php echo e(route('product', $details->slug)); ?>" />
<meta name="robots" content="index, follow" />
<meta name="description" content="<?php echo e($details->meta_description); ?>" />
<meta name="keywords" content="<?php echo e($details->slug); ?>" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="<?php echo e($details->name); ?>" />
<meta name="twitter:title" content="<?php echo e($details->name); ?>" />
<meta name="twitter:description" content="<?php echo e($details->meta_description); ?>" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="<?php echo e(route('product', $details->slug)); ?>" />
<meta name="twitter:image" content="<?php echo e(asset($details->image->image)); ?>" />

<!-- Open Graph data -->
<meta property="og:title" content="<?php echo e($details->name); ?>" />
<meta property="og:type" content="product" />
<meta property="og:url" content="<?php echo e(route('product', $details->slug)); ?>" />
<meta property="og:image" content="<?php echo e(asset($details->image->image)); ?>" />
<meta property="og:description" content="<?php echo e($details->meta_description); ?>" />
<meta property="og:site_name" content="<?php echo e($details->name); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/zoomsl.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="homeproduct main-details-page">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <section class="product-section">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-6 position-relative">
                                <?php if($details->old_price): ?>
                                <div class="product-details-discount-badge">
                                    <div class="sale-badge">
                                        <div class="sale-badge-inner">
                                            <div class="sale-badge-box">
                                                <span class="sale-badge-text">
                                                    <p> <?php $discount=(((($details->old_price)-($details->new_price))*100) / ($details->old_price)) ?> <?php echo e(number_format($discount, 0)); ?>%</p>
                                                    ছাড়
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="details_slider owl-carousel">
                                    <?php $__currentLoopData = $details->mainImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="dimage_item" data-color-id="<?php echo e($value->color_id ?? ''); ?>">
                                            <img src="<?php echo e(asset($value->image)); ?>" class="block__pic" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $details->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($value->color_id): ?>
                                        <div class="dimage_item" data-color-id="<?php echo e($value->color_id); ?>" style="display:none;">
                                            <img src="<?php echo e(asset($value->image)); ?>" class="block__pic" />
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div
                                    class="indicator_thumb <?php if(($details->mainImages->count() + $details->images->where('color_id','!=',null)->count()) > 4): ?> thumb_slider owl-carousel <?php endif; ?>">
                                    <?php $__currentLoopData = $details->mainImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="indicator-item" data-id="<?php echo e($key); ?>" data-color-id="<?php echo e($image->color_id ?? ''); ?>">
                                            <img src="<?php echo e(asset($image->image)); ?>" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $mainCount = $details->mainImages->count(); ?>
                                    <?php $__currentLoopData = $details->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($image->color_id): ?>
                                        <div class="indicator-item" data-id="<?php echo e($mainCount + $loop->index); ?>" data-color-id="<?php echo e($image->color_id); ?>" style="display:none;">
                                            <img src="<?php echo e(asset($image->image)); ?>" />
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="details_right">
                                    <div class="breadcrumb">
                                        <ul>
                                            <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
                                            <?php if($details->category && $details->category->slug): ?>
                                            <li><span>/</span></li>
                                            <li><a
                                                    href="<?php echo e(url('/category/' . $details->category->slug)); ?>"><?php echo e($details->category->name); ?></a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($details->subcategory): ?>
                                                <li><span>/</span></li>
                                                <li><a
                                                        href="#"><?php echo e($details->subcategory ? $details->subcategory->subcategoryName : ''); ?></a>
                                                </li>
                                                <?php endif; ?>

                                                <?php if($details->childcategory): ?>
                                                    <li><span>/</span></li>
                                                    <li><a
                                                            href="#"><?php echo e($details->childcategory->childcategoryName); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                        </ul>
                                    </div>

                                    <div class="product">
                                        <div class="product-cart">
                                            <p class="name"><?php echo e($details->name); ?></p>
                                            <p class="details-price" id="productMainPrice">
                                                <?php if($details->old_price): ?>
                                                    <del id="productOldPrice">৳<?php echo e($details->old_price); ?></del>
                                                <?php endif; ?> <span id="productNewPrice">৳<?php echo e($details->new_price); ?></span>
                                                <?php if($productcolors->where('price', '!=', null)->count() > 0 || $productsizes->where('price', '!=', null)->count() > 0): ?>
                                                <small class="text-info d-block" id="variantPriceNote">Select a color/size to see variant price</small>
                                                <?php endif; ?>
                                            </p>
                                            <div class="details-ratting-wrapper">
                                            <?php
                                                $averageRating = $reviews->avg('ratting');
                                                $filledStars = floor($averageRating);
                                                $emptyStars = 5 - $filledStars;
                                            ?>

                                            <?php if($averageRating >= 0 && $averageRating <= 5): ?>
                                                <?php for($i = 1; $i <= $filledStars; $i++): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php endfor; ?>

                                                <?php if($averageRating == $filledStars): ?>
                                                    
                                                <?php else: ?>
                                                    <i class="far fa-star-half-alt"></i>
                                                <?php endif; ?>

                                                <?php for($i = 1; $i <= $emptyStars; $i++): ?>
                                                    <i class="far fa-star"></i>
                                                <?php endfor; ?>

                                                <span><?php echo e(number_format($averageRating, 2)); ?>/5</span>
                                            <?php else: ?>
                                                <span>Invalid rating range</span>
                                            <?php endif; ?>
                                            <a class="all-reviews-button" href="#writeReview">See Reviews</a>
                                            </div>
                                            <div class="product-code">
                                                <p><span>প্রোডাক্ট কোড : </span><?php echo e($details->product_code); ?></p>
                                            </div>
                                            <?php if($details->note): ?>
                                            <div class="">
                                                <span class="text-danger font-italic fs-5"><strong class="bg-danger text-light px-1 py-1">Note :</strong> <strong> <?php echo e($details->note); ?></strong> </span>
                                            </div>
                                            <?php endif; ?>
                                            <form action="<?php echo e(route('cart.store')); ?>" method="POST" name="formName">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($details->id); ?>" />
                                                <?php if($productcolors->count() > 0): ?>
                                                    <div class="pro-color" style="width: 100%;">
                                                        <div class="color_inner">
                                                            <p>Color -</p>
                                                            <div class="size-container">
                                                                <div class="selector">
                                                                    <?php $__currentLoopData = $productcolors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $procolor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <div class="selector-item">
                                                                            <input type="radio"
                                                                                id="fc-option<?php echo e($procolor->id); ?>"
                                                                                value="<?php echo e($procolor->color ? $procolor->color->colorName : ''); ?>"
                                                                                name="product_color"
                                                                                class="selector-item_radio emptyalert color-variant"
                                                                                data-price="<?php echo e($procolor->price); ?>"
                                                                                data-stock="<?php echo e($procolor->stock); ?>"
                                                                                data-color-id="<?php echo e($procolor->color_id); ?>"
                                                                                required />
                                                                            <label for="fc-option<?php echo e($procolor->id); ?>"
                                                                                style="margin-right:5px;background-color: <?php echo e($procolor->color ? $procolor->color->color : ''); ?>"
                                                                                class="selector-item_label">

                                                                                <span>
                                                                                    <img src="<?php echo e(asset('frontEnd/images')); ?>/check-icon.svg"
                                                                                        alt="Checked Icon" />

                                                                                </span>

                                                                            </label>
                                                                             <?php echo e($procolor->color->colorName); ?>

                                                                             <?php if($procolor->price): ?>
                                                                             <small class="d-block text-muted">৳<?php echo e($procolor->price); ?></small>
                                                                             <?php endif; ?>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?> <?php if($productsizes->count() > 0): ?>
                                                        <div class="pro-size" style="width: 100%;">
                                                            <div class="size_inner">
                                                                <p>Size - <span class="attibute-name"></span></p>
                                                                <div class="size-container">
                                                                    <div class="selector">
                                                                        <?php $__currentLoopData = $productsizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prosize): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <div class="selector-item">
                                                                                <input type="radio"
                                                                                    id="f-option<?php echo e($prosize->id); ?>"
                                                                                    value="<?php echo e($prosize->size ? $prosize->size->sizeName : ''); ?>"
                                                                                    name="product_size"
                                                                                    class="selector-item_radio emptyalert size-variant"
                                                                                    data-price="<?php echo e($prosize->price); ?>"
                                                                                    data-stock="<?php echo e($prosize->stock); ?>"
                                                                                    data-size-id="<?php echo e($prosize->size_id); ?>"
                                                                                    required />
                                                                                <label
                                                                                    for="f-option<?php echo e($prosize->id); ?>"
                                                                                    class="selector-item_label"><?php echo e($prosize->size ? $prosize->size->sizeName : ''); ?></label>
                                                                                <?php if($prosize->price): ?>
                                                                                <small class="d-block text-muted">৳<?php echo e($prosize->price); ?></small>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                        <?php if($details->pro_unit): ?>
                                                            <div class="pro_unig">
                                                                <label>Unit: <?php echo e($details->pro_unit); ?></label>
                                                                <input type="hidden" name="pro_unit"
                                                                    value="<?php echo e($details->pro_unit); ?>" />
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="pro_brand">
                                                            <p>Brand :
                                                                <?php echo e($details->brand ? $details->brand->name : 'N/A'); ?>

                                                            </p>
                                                        </div>

                                                        <?php if($details->stock < 1): ?>
                                                        <p class="text-danger text-center border border-danger p-2">স্টক আউট</p>
                                                        <?php else: ?>

                                                        <div class="row">
                                                            <div class="qty-cart col-sm-12">
                                                                <div class="quantity">
                                                                    <span class="minus">-</span>
                                                                    <input type="text" name="qty"
                                                                        value="1" />
                                                                    <span class="plus">+</span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex single_product col-sm-12">
                                                                <input type="submit" class="btn px-4 add_cart_btn"
                                                                    onclick="return sendSuccess();" name="add_cart"
                                                                    value="কার্টে যোগ করুন " />

                                                                <input type="submit"
                                                                    class="btn px-4 order_now_btn order_now_btn_m"
                                                                    onclick="return sendSuccess();" name="order_now"
                                                                    value="অর্ডার করুন" />
                                                            </div>
                                                        </div>

                                                        <?php endif; ?>
                                                        <div class="mt-md-2 mt-2 ">

                                                            <div class="shadow mt-2">
                                                                <a href="tel:<?php echo e($contact->hotline); ?>"
                                                                class="btn btn-primary  d-block   text-light fw-bolder">
                                                                    কল করুন <i class="fa-solid fa-phone"></i> <?php echo e($contact->hotline); ?>

                                                                    </a>
                                                             </div>
                                                             <div class="shadow mt-2">
                                                                <a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', $contact->whatsapp)); ?>?text=<?php echo e(urlencode('Hello, I am interested in your product: ' . $details->name . '. Here is the link: ' . url('/products/' . $details->slug))); ?>"
                                                                class="btn btn-success  d-block   text-light fw-bolder">
                                                                    Whatsapp <i class="fa-brands fa-whatsapp"></i> <?php echo e($contact->whatsapp); ?>

                                                                    </a>
                                                             </div>

                                                        </div>
                                                        <div class="mt-md-2 mt-2">
                                                            <table class="table table-bordered border-1 border-dark">
                                                                <tr>
                                                                    <th colspan="2" class="text-center">
                                                                        কুরিয়ার ডেলিভারি খরচ
                                                                    </th>
                                                                </tr>
                                                                <?php $__currentLoopData = $shippingcharge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <tr>
                                                                    <td><?php echo e($value->name); ?></td>
                                                                    <td class="text-end">৳ <?php echo e($value->amount); ?></td>
                                                                </tr>
                                                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </table>
                                                            
                                                        </div>
                                            </form>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="description-nav-wrapper">
    <div class="container">
        <div class="row">

            <div class="col-sm-12">
                <div class="description-nav">
                    <ul class="desc-nav-ul">
                        
                        <li>
                            <a href="#description" target="_self">Description</a>
                        </li>
                        <li>
                            <a href="#orderpolicy" target="_self">Order Policy</a>
                        </li>
                        <li>
                            <a href="#writeReview" target="_self">Reviews (<?php echo e($reviews->count()); ?>) </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="pro_details_area">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="description tab-content details-action-box" id="description">
                    <h2>বিস্তারিত</h2>
                    <p><?php echo $details->description; ?></p>
                </div>
                 <div class="description tab-content details-action-box" id="orderpolicy">
                    <h2>Order Policy</h2>
                    <p><?php echo $generalsetting ->order_policy; ?></p>
                </div>

                <div class="tab-content details-action-box" id="writeReview">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="section-head">
                                    <div class="title">
                                        <h2>Reviews (<?php echo e($reviews->count()); ?>)</h2>
                                        <p>Get specific details about this product from customers who own it.</p>
                                    </div>
                                    <div class="action">
                                        <div>
                                            <button type="button" class="details-action-btn question-btn btn-overlay"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                Write a review
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php if($reviews->count() > 0): ?>
                                    <div class="customer-review">
                                        <div class="row">
                                            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-sm-12 col-12">
                                                    <div class="review-card">
                                                        <p class="reviewer_name"><i data-feather="message-square"></i>
                                                            <?php echo e($review->name); ?></p>
                                                        <p class="review_data"><?php echo e($review->created_at->format('d-m-Y')); ?></p>
                                                        <p class="review_star"><?php echo str_repeat('<i class="fa-solid fa-star"></i>', $review->ratting); ?></p>
                                                        <p class="review_content"><?php echo e($review->review); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-content">
                                        <i class="fa fa-clipboard-list"></i>
                                        <p class="empty-text">This product has no reviews yet. Be the first one to write a review.</p>
                                    </div>
                                <?php endif; ?>
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog  modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Your review</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="insert-review">
                                                    <?php if(Auth::guard('customer')->user()): ?>
                                                        <form action="<?php echo e(route('customer.review')); ?>" id="review-form"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="product_id" value="<?php echo e($details->id); ?>">
                                                            <div class="fz-12 mb-2">
                                                                <div class="rating">
                                                                    <label title="Excelent">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="5" />
                                                                    </label>
                                                                    <label title="Best">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="4" />
                                                                    </label>
                                                                    <label title="Better">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="3" />
                                                                    </label>
                                                                    <label title="Very Good">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="2" />
                                                                    </label>
                                                                    <label title="Good">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="1" />
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="message-text" class="col-form-label">Message:</label>
                                                                <textarea required class="form-control radius-lg" name="review" id="message-text"></textarea>
                                                                <span id="validation-message" style="color: red;"></span>
                                                            </div>
                                                            <div class="form-group">
                                                                <button class="details-review-button" type="submit">Submit
                                                                    Review</button>
                                                            </div>

                                                        </form>
                                                    <?php else: ?>
                                                        <a class="customer-login-redirect" href="<?php echo e(route('customer.login')); ?>">Login
                                                            to Post
                                                            Your Review</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($details->pro_video): ?>
            <div class="col-sm-4">
                <div class="pro_vide">
                    <h2>ভিডিও</h2>
                    <iframe width="100%" height="315"
                        src="https://www.youtube.com/embed/<?php echo e($details->pro_video); ?>" title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="related-product-section">
    <div class="container">
        <div class="row">
            <div class="related-title">
                <h5>Related Product</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="product-inner owl-carousel related_slider">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="product_item wist_item">
                            <div class="product_item_inner">
                                <?php if($value->old_price): ?>
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p><?php
                                                $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price))
                                                ?>
                                                <?php echo e(number_format($discount, 0)); ?>%</p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="pro_img">
                                    <a href="<?php echo e(route('product', $value->slug)); ?>">
                                        <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>"
                                            alt="<?php echo e($value->name); ?>" />
                                    </a>
                                    <?php if($value->stock < 1): ?>
                                    <div class="stock-out-overlay">STOCK OUT</div>
                                    <?php endif; ?>
                                </div>
                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a
                                            href="<?php echo e(route('product', $value->slug)); ?>"><?php echo e(Str::limit($value->name, 80)); ?></a>
                                    </div>
                                    <div class="pro_price">
                                        <p>
                                            <del>৳ <?php echo e($value->old_price); ?></del>
                                            ৳ <?php echo e($value->new_price); ?> <?php if($value->old_price): ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || !$value->stock): ?>
                                <div class="pro_btn">

                                    <div class="cart_btn order_button">
                                        <a href="<?php echo e(route('product', $value->slug)); ?>"
                                            class="addcartbutton">অর্ডার করুন</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="pro_btn">

                                    <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit">অর্ডার করুন</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?> <?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/js/owl.carousel.min.js')); ?>"></script>

<script src="<?php echo e(asset('frontEnd/js/zoomsl.min.js')); ?>"></script>

<script>
    $(document).ready(function() {
        $(".details_slider").owlCarousel({
            margin: 15,
            items: 1,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
        });
        $(".indicator-item").on("click", function() {
            var slideIndex = $(this).data("id");
            $(".details_slider").trigger("to.owl.carousel", slideIndex);
        });

        // Dynamic variant pricing
        var basePrice = <?php echo e($details->new_price); ?>;
        var baseOldPrice = <?php echo e($details->old_price ?? 0); ?>;

        function updateVariantPrice() {
            var colorPrice = $('input[name="product_color"]:checked').data('price');
            var sizePrice = $('input[name="product_size"]:checked').data('price');
            var newPrice = basePrice;
            var oldPrice = baseOldPrice;

            // Use color variant price if set
            if (colorPrice && parseFloat(colorPrice) > 0) {
                newPrice = parseFloat(colorPrice);
            }
            // Use size variant price if set (overrides color)
            if (sizePrice && parseFloat(sizePrice) > 0) {
                newPrice = parseFloat(sizePrice);
            }

            $('#productNewPrice').text('৳' + newPrice.toFixed(2));
            if (oldPrice > 0) {
                $('#productOldPrice').show();
            }
            $('#variantPriceNote').hide();
        }

        // ===== Color-specific image switching =====
        function filterImagesByColor(colorId) {
            var $slider = $('.details_slider');
            var $thumbContainer = $('.indicator_thumb');
            var hasColorFilter = colorId && colorId !== '';

            // Show/hide slider items based on color
            $slider.find('.dimage_item').each(function() {
                var imgColorId = $(this).data('color-id');
                // Show if: no color filter (show all) OR image has matching color OR image has no color (default)
                if (!hasColorFilter || imgColorId == colorId || imgColorId === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Show/hide thumbnail indicators
            $thumbContainer.find('.indicator-item').each(function() {
                var imgColorId = $(this).data('color-id');
                if (!hasColorFilter || imgColorId == colorId || imgColorId === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Refresh owl carousel and go to first slide
            $slider.trigger('refresh.owl.carousel').trigger('to.owl.carousel', 0);
        }

        $('.color-variant').on('change', function() {
            updateVariantPrice();
            var colorId = $(this).data('color-id');
            filterImagesByColor(colorId);
        });

        // When no color is selected (deselect), show all images
        $('input[name="product_color"]').on('change', function() {
            if (!$('input[name="product_color"]:checked').length) {
                filterImagesByColor('');
            }
        });

        $('.size-variant').on('change', function() {
            updateVariantPrice();
        });
    });
</script>
<!--Data Layer Start-->
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];

    dataLayer.push({
        event: "view_item",
        ecommerce: {
            items: [{
                item_name: "<?php echo e($details->name); ?>",
                item_id: "<?php echo e($details->id); ?>",
                price: "<?php echo e($details->new_price); ?>",
                item_brand: "<?php echo e($details->brand?$details->brand->name:''); ?>",
                item_category: "<?php echo e($details->category?$details->category->name:''); ?>",
                item_variant: "<?php echo e($details->pro_unit); ?>",
                currency: "BDT",
                quantity: <?php echo e($details->stock ?? 0); ?>

            }],
            impression: [
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    {
                        item_name: "<?php echo e($value->name); ?>",
                        item_id: "<?php echo e($value->id); ?>",
                        price: "<?php echo e($value->new_price); ?>",
                        item_brand: "<?php echo e($details->brand?$details->brand->name:''); ?>",
                        item_category: "<?php echo e($value->category ? $value->category->name : ''); ?>",
                        item_variant: "<?php echo e($value->pro_unit); ?>",
                        currency: "BDT",
                        quantity: <?php echo e($value->stock ?? 0); ?>

                    },
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#add_to_cart').click(function() {
            gtag("event", "add_to_cart", {
                currency: "BDT",
                value: "1.5",
                items: [
                    <?php $__currentLoopData = Cart::instance('shopping')->content(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cartInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        {
                            item_id: "<?php echo e($details->id); ?>",
                            item_name: "<?php echo e($details->name); ?>",
                            price: "<?php echo e($details->new_price); ?>",
                            currency: "BDT",
                            quantity: <?php echo e($cartInfo->qty ?? 0); ?>

                        },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ]
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#order_now').click(function() {
            gtag("event", "add_to_cart", {
                currency: "BDT",
                value: "1.5",
                items: [
                    <?php $__currentLoopData = Cart::instance('shopping')->content(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cartInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        {
                            item_id: "<?php echo e($details->id); ?>",
                            item_name: "<?php echo e($details->name); ?>",
                            price: "<?php echo e($details->new_price); ?>",
                            currency: "BDT",
                            quantity: <?php echo e($cartInfo->qty ?? 0); ?>

                        },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ]
            });
        });
    });
</script>

<!-- Data Layer End-->
<script>
    $(document).ready(function() {
        $(".related_slider").owlCarousel({
            margin: 10,
            items: 6,
            loop: true,
            dots: true,
            nav: true,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: true,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 6,
                    nav: true,
                    loop: true,
                },
            },
        });
        // $('.owl-nav').remove();
    });
</script>
<script>
    $(document).ready(function() {
        $(".minus").click(function() {
            var $input = $(this).parent().find("input");
            var count = parseInt($input.val()) - 1;
            count = count < 1 ? 1 : count;
            $input.val(count);
            $input.change();
            return false;
        });
        $(".plus").click(function() {
            var $input = $(this).parent().find("input");
            $input.val(parseInt($input.val()) + 1);
            $input.change();
            return false;
        });
    });
</script>

<script>
    function sendSuccess() {
        // size validation
        size = document.forms["formName"]["product_size"].value;
        if (size != "") {
            // access
        } else {
            toastr.warning("Please select any size");
            return false;
        }
        color = document.forms["formName"]["product_color"].value;
        if (color != "") {
            // access
        } else {
            toastr.error("Please select any color");
            return false;
        }
    }
</script>
<script>
    $(document).ready(function() {
        $(".rating label").click(function() {
            $(".rating label").removeClass("active");
            $(this).addClass("active");
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".thumb_slider").owlCarousel({
            margin: 15,
            items: 4,
            loop: true,
            dots: false,
            nav: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
        });
    });
</script>

<script type="text/javascript">
    $(".block__pic").imagezoomsl({
        zoomrange: [3, 3]
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/frontEnd/layouts/pages/details.blade.php ENDPATH**/ ?>