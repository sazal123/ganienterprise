 
<?php $__env->startSection('title'); ?>
  <title><?php echo e($subcategory->meta_title); ?></title> 
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/jquery-ui.css')); ?>" />
<?php $__env->stopPush(); ?> 
<?php $__env->startPush('seo'); ?>
<meta name="app-url" content="<?php echo e(route('subcategory',$subcategory->slug)); ?>" />
<meta name="robots" content="index, follow" />
<meta name="description" content="<?php echo e($subcategory->meta_description); ?>" />
<meta name="keywords" content="<?php echo e($subcategory->slug); ?>" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="<?php echo e($subcategory->subcategoryName); ?>" />
<meta name="twitter:title" content="<?php echo e($subcategory->subcategoryName); ?>" />
<meta name="twitter:description" content="<?php echo e($subcategory->meta_description); ?>" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="<?php echo e(route('subcategory',$subcategory->slug)); ?>" />
<meta name="twitter:image" content="<?php echo e(asset($subcategory->image)); ?>" />

<!-- Open Graph data -->
<meta property="og:title" content="<?php echo e($subcategory->subcategoryName); ?>" />
<meta property="og:type" content="product" />
<meta property="og:url" content="<?php echo e(route('subcategory',$subcategory->slug)); ?>" />
<meta property="og:image" content="<?php echo e(asset($subcategory->image)); ?>" />
<meta property="og:description" content="<?php echo e($subcategory->meta_description); ?>" />
<meta property="og:site_name" content="<?php echo e($subcategory->subcategoryName); ?>" />
<?php $__env->stopPush(); ?> 
<?php $__env->startSection('content'); ?>
<section class="product-section">
    <div class="container">
        <div class="sorting-section">
            <div class="row">
                <div class="col-sm-6">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="<?php echo e(route('home')); ?>">Home</a>
                        <span>/</span>
                        <strong><?php echo e($subcategory->subcategoryName); ?></strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="showing-data">
                                <span>Showing <?php echo e($products->firstItem()); ?>-<?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?> Results</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="filter_sort">
                                <div class="filter_btn">
                                    <i class="fa fa-list-ul"></i>
                                </div>
                                <div class="page-sort">
                                    <form action="" class="sort-form">
                                        <select name="sort" class="form-control form-select sort">
                                            <option value="1" <?php if(request()->get('sort')==1): ?>selected <?php endif; ?>>Product: Latest</option>
                                            <option value="2" <?php if(request()->get('sort')==2): ?>selected <?php endif; ?>>Product: Oldest</option>
                                            <option value="3" <?php if(request()->get('sort')==3): ?>selected <?php endif; ?>>Price: High To Low</option>
                                            <option value="4" <?php if(request()->get('sort')==4): ?>selected <?php endif; ?>>Price: Low To High</option>
                                            <option value="5" <?php if(request()->get('sort')==5): ?>selected <?php endif; ?>>Name: A-Z</option>
                                            <option value="6" <?php if(request()->get('sort')==6): ?>selected <?php endif; ?>>Name: Z-A</option>
                                        </select>
                                        <input type="hidden" name="min_price" value="<?php echo e(request()->get('min_price')); ?>" />
                                        <input type="hidden" name="max_price" value="<?php echo e(request()->get('max_price')); ?>" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-3 filter_sidebar">
                
                <div class="filter_close"><i class="fa fa-long-arrow-left"></i> Filter</div>
                <form action="" class="attribute-submit">
                    <div class="sidebar_item wraper__item">
                        <div class="accordion" id="category_sidebar">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseCat" aria-expanded="true" aria-controls="collapseOne">
                                        <?php echo e($subcategory->subcategoryName); ?>

                                    </button>
                                </h2>
                                <div id="collapseCat" class="accordion-collapse collapse show"
                                    data-bs-parent="#category_sidebar">
                                    <div class="accordion-body cust_according_body">
                                        <ul>
                                            <?php $__currentLoopData = $subcategory->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $childcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <a
                                                        href="<?php echo e(url('products/' . $childcat->slug)); ?>"><?php echo e($childcat->childcategoryName); ?></a>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--sidebar item end-->
                    <div class="sidebar_item wraper__item">
                        <div class="accordion" id="price_sidebar">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsePrice" aria-expanded="true" aria-controls="collapseOne">
                                        Price
                                    </button>
                                </h2>
                                <div id="collapsePrice" class="accordion-collapse collapse show"
                                    data-bs-parent="#price_sidebar">
                                    <div class="accordion-body cust_according_body">
                                        <div class="category-filter-box category__wraper" id="categoryFilterBox">
                                            <div class="category-filter-item">
                                                <div class="filter-body">
                                                    <div class="slider-box">
                                                        <form action="" class="price-submit">
                                                            <div class="filter-price-inputs">
                                                                <p class="min-price">৳<input type="text"
                                                                        name="min_price" id="min_price" readonly="" />
                                                                </p>
                                                                <p class="max-price">৳<input type="text"
                                                                        name="max_price" id="max_price" readonly="" />
                                                                </p>
                                                            </div>
    
                                                            <div id="price-range" class="slider form-attribute"></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--sidebar item end-->
                    <div class="sidebar_item wraper__item">
                        <div class="accordion" id="filter_sidebar">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFilter" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        Filter
                                    </button>
                                </h2>
                                <div id="collapseFilter" class="accordion-collapse collapse show"
                                    data-bs-parent="#filter_sidebar">
                                    <div class="accordion-body cust_according_body">
                                        <div class="filter-body">
                                            <form action="" class="subcategory-submit">
                                                <ul class="space-y-3">
                                                    <?php $__currentLoopData = $childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="subcategory-filter-list">
                                                            <label for="<?php echo e($childcategory->slug . '-' . $childcategory->id); ?>"
                                                                class="subcategory-filter-label">
                                                                <input class="form-checkbox form-attribute"
                                                                    id="<?php echo e($childcategory->slug . '-' . $childcategory->id); ?>"
                                                                    name="childcategory[]" value="<?php echo e($childcategory->id); ?>"
                                                                    type="checkbox"
                                                                    <?php if(is_array(request()->get('childcategory')) && in_array($childcategory->id, request()->get('childcategory'))): ?> checked <?php endif; ?> />
                                                                <p class="subcategory-filter-name">
                                                                    <?php echo e($childcategory->childcategoryName); ?></p>
                                                            </label>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--sidebar item end-->
                </form>
            </div>
            <div class="col-sm-9">
                <div class="category-product main_product_inner">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="product_item wist_item">
                        <div class="product_item_inner">
                            <?php if($value->old_price): ?>
                            <div class="sale-badge">
                                <div class="sale-badge-inner">
                                    <div class="sale-badge-box">
                                        <span class="sale-badge-text">
                                           <p> <?php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) ?> <?php echo e(number_format($discount,0)); ?>%</p>
                                            ছাড়
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <!--<?php if($soldShow && $value->sold): ?>-->
                            <!--    <div class="sale-badge" style="left:4px;">-->
                            <!--        <div class="sale-badge-inner">-->
                            <!--            <div class="sale-badge-box">-->
                            <!--                <span class="sale-badge-text">-->
                            <!--                    <p class="text-center"> <?php echo e($value->sold); ?></p>-->
                            <!--                    SOLD-->
                            <!--                </span>-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--<?php endif; ?>-->
                            <div class="pro_img">
                                <a href="<?php echo e(route('product',$value->slug)); ?>">
                                    <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>" alt="<?php echo e($value->name); ?>" />
                                </a>
                              <?php if($value->stock < 1): ?>
                                <div class="stock-out-overlay">STOCK OUT</div>
                                <?php endif; ?>
                            </div>
                            <div class="pro_des">
                                <div class="pro_name">
                                    <a href="<?php echo e(route('product',$value->slug)); ?>"><?php echo e(Str::limit($value->name,80)); ?></a>
                                </div>
                                <?php if($soldShow): ?>
                                <span style="background-color:#FFBCA5" class="px-3 py-1 rounded-pill">Sold <?php echo e($value->sold??0); ?></span>
                                <?php endif; ?>
                                <div class="pro_price">
                                    <p>
                                        <del>৳ <?php echo e($value->old_price); ?></del>
                                        ৳ <?php echo e($value->new_price); ?> <?php if($value->old_price): ?> <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                         <?php if(! $value->prosizes->isEmpty() || ! $value->procolors->isEmpty() || ($value->stock <1)): ?>
                        <div class="pro_btn">
                           
                            <div class="cart_btn order_button">
                                <a href="<?php echo e(route('product',$value->slug)); ?>" class="addcartbutton">অর্ডার করুন</a>
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
        <div class="row">
            <div class="col-sm-12">
                <div class="custom_paginate">
                    <?php echo e($products->links('pagination::bootstrap-4')); ?>

                   
                </div>
            </div>
        </div>
    </div>
</section>

<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="meta_des">
                    <?php echo $subcategory->meta_description; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script>
        $("#price-range").click(function() {
            $(".price-submit").submit();
        })
        $(".form-attribute").on('change click',function(){
            $(".attribute-submit").submit();
        })
        $(".sort").change(function() {
            $(".sort-form").submit();
        })
        $(".form-checkbox").change(function() {
            $(".subcategory-submit").submit();
        })
    </script>
    <script>
        $(function() {
            $("#price-range").slider({
                step: 5,
                range: true,
                min: <?php echo e($min_price); ?>,
                max: <?php echo e($max_price); ?>,
                values: [
                    <?php echo e(request()->get('min_price') ? request()->get('min_price') : $min_price); ?>,
                    <?php echo e(request()->get('max_price') ? request()->get('max_price') : $max_price); ?>

                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val(<?php echo e(request()->get('min_price') ? request()->get('min_price') : $min_price); ?>);
            $("#max_price").val(<?php echo e(request()->get('max_price') ? request()->get('max_price') : $max_price); ?>);
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

            $("#mobile-price-range").slider({
                step: 5,
                range: true,
                min: <?php echo e($min_price); ?>,
                max: <?php echo e($max_price); ?>,
                values: [
                    <?php echo e(request()->get('min_price') ? request()->get('min_price') : $min_price); ?>,
                    <?php echo e(request()->get('max_price') ? request()->get('max_price') : $max_price); ?>

                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val(<?php echo e(request()->get('min_price') ? request()->get('min_price') : $min_price); ?>);
            $("#max_price").val(<?php echo e(request()->get('max_price') ? request()->get('max_price') : $max_price); ?>);
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

        });
    </script>

<script>
    // $(".sort").change(function(){
    //   $('#loading').show();
    //   $(".sort-form").submit();
    // })
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/pages/subcategory.blade.php ENDPATH**/ ?>