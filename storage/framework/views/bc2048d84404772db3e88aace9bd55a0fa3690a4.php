<?php $__env->startSection('title', $category->meta_title ?? $category->name); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/jquery-ui.css')); ?>" />
<style>
/* ───── Category Header ───── */
.cat-page-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 32px 0 28px;
    position: relative;
    overflow: hidden;
    display:none;
}
.cat-page-header::before {
    content: ''; position: absolute; top: -50%; right: -20%;
    width: 500px; height: 500px; border-radius: 50%;
    background: rgba(201,168,76,0.06);
}
.cat-page-header::after {
    content: ''; position: absolute; bottom: -30%; left: -10%;
    width: 300px; height: 300px; border-radius: 50%;
    background: rgba(60,125,23,0.08);
}
.cat-header-inner { position: relative; z-index: 1; }
.cat-page-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 28px; font-weight: 700; color: #fff; margin: 0 0 4px;
}
.cat-breadcrumb {
    display: flex; align-items: center; gap: 8px; font-size: 13px;
    color: rgba(255,255,255,0.6);
}
.cat-breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; }
.cat-breadcrumb a:hover { color: #C9A84C; }
.cat-breadcrumb strong { color: #C9A84C; font-weight: 600; }
.cat-breadcrumb span { color: rgba(255,255,255,0.3); }

/* ───── Layout ───── */
.cat-layout { padding: 30px 0 60px; background: #f8f8f6; min-height: 600px; }

/* ───── Toolbar ───── */
.cat-toolbar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    background: #fff; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.cat-toolbar-left {
    display: flex; align-items: center; gap: 16px;
}
.cat-toolbar-left .showing-data { font-size: 13px; color: #666; }
.mobile-filter-btn {
    display: none; align-items: center; gap: 6px;
    padding: 7px 16px; background: #3c7d17; color: #fff; border: none;
    border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;
}
.mobile-filter-btn:hover { background: #2d5d11; }

.cat-sort-wrapper {
    display: flex; align-items: center; gap: 8px;
}
.cat-sort-wrapper label { font-size: 13px; color: #666; white-space: nowrap; }
.cat-sort-wrapper select {
    padding: 7px 32px 7px 12px; border: 1px solid #e0e0e0; border-radius: 6px;
    font-size: 13px; color: #333;
    background: #fafafa url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23999'/%3E%3C/svg%3E") no-repeat right 12px center;
    appearance: none; -webkit-appearance: none; cursor: pointer;
}
.cat-sort-wrapper select:focus { border-color: #3c7d17; outline: none; }

/* ───── Sidebar ───── */
.cat-sidebar { position: sticky; top: 100px; }
.cat-sidebar-card {
    background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow: hidden; margin-bottom: 16px;
}
.cat-sidebar-card:last-child { margin-bottom: 0; }

.cat-sidebar-title {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; font-size: 14px; font-weight: 600; color: #1a1a2e;
    border-bottom: 1px solid #f0f0f0; cursor: pointer; user-select: none;
}
.cat-sidebar-title:hover { background: #fafaf8; }
.cat-sidebar-title i { font-size: 10px; color: #999; transition: transform 0.3s; }
.cat-sidebar-title.active i { transform: rotate(180deg); }
.cat-sidebar-body { padding: 6px 0; }
.cat-sidebar-body.hidden { display: none; }

/* Category Tree */
.cat-list { list-style: none; padding: 0; margin: 0; }
.cat-item { border-bottom: 1px solid #f5f5f5; }
.cat-item:last-child { border-bottom: none; }

.cat-link {
    display: block; padding: 10px 18px; font-size: 13px; color: #444;
    text-decoration: none; transition: all 0.2s;
}
.cat-link:hover { background: #f8f8f6; color: #3c7d17; }
.cat-link.active { color: #3c7d17; font-weight: 600; background: rgba(60,125,23,0.04); }

.cat-toggle {
    background: none; border: none; padding: 10px 18px; width: 100%; text-align: left;
    font-size: 13px; color: #444; cursor: pointer;
    display: flex; align-items: center; justify-content: space-between;
}
.cat-toggle:hover { background: #f8f8f6; color: #3c7d17; }
.cat-toggle i { font-size: 10px; color: #bbb; transition: transform 0.3s; }
.cat-toggle.active i { transform: rotate(90deg); }

.cat-sub-list {
    list-style: none; padding: 0 0 4px 20px; margin: 0; display: none;
}
.cat-sub-list.open { display: block; }
.cat-sub-link {
    display: block; padding: 8px 18px 8px 16px; font-size: 12px; color: #666;
    text-decoration: none; border-left: 2px solid transparent; transition: all 0.2s;
}
.cat-sub-link:hover { color: #3c7d17; border-left-color: #3c7d17; background: rgba(60,125,23,0.03); }

/* Price Range */
.cat-price-range { padding: 18px; }
.price-inputs {
    display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 18px;
}
.price-input-group {
    flex: 1; display: flex; align-items: center;
    background: #f5f5f5; border: 1px solid #e8e8e8; border-radius: 6px;
    padding: 6px 10px; font-size: 13px; color: #666;
}
.price-input-group span { margin-right: 4px; }
.price-input-group input {
    width: 100%; border: none; background: transparent;
    font-size: 13px; font-weight: 600; color: #333; outline: none;
}
.price-separator { color: #ccc; font-size: 13px; }

#cat-price-range {
    margin: 0 4px; height: 4px; border: none;
    background: #e8e8e8; border-radius: 2px;
}
#cat-price-range .ui-slider-range {
    background: linear-gradient(90deg, #3c7d17, #5a9e2a); border-radius: 2px;
}
#cat-price-range .ui-slider-handle {
    width: 18px; height: 18px; border: 2px solid #3c7d17; background: #fff;
    border-radius: 50%; top: -7px; cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
}
#cat-price-range .ui-slider-handle:hover { transform: scale(1.15); box-shadow: 0 2px 8px rgba(60,125,23,0.3); }

.price-filter-actions { display: flex; gap: 8px; margin-top: 16px; }
.btn-price-filter {
    flex: 1; padding: 9px 0; background: #C9A84C; color: #fff;
    border: none; border-radius: 6px; font-size: 13px; font-weight: 600;
    cursor: pointer; text-align: center;
}
.btn-price-filter:hover { background: #2d5d11; }
.btn-price-reset {
    padding: 9px 16px; background: #f0f0f0; color: #666;
    border: none; border-radius: 6px; font-size: 13px; cursor: pointer;
}
.btn-price-reset:hover { background: #e0e0e0; color: #333; }

/* Filter Checkboxes */
.cat-filter-list { list-style: none; padding: 4px 0; margin: 0; }
.cat-filter-label {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 18px; cursor: pointer; transition: background 0.2s;
}
.cat-filter-label:hover { background: #f8f8f6; }
.cat-filter-label input[type="checkbox"] {
    appearance: none; -webkit-appearance: none;
    width: 17px; height: 17px; border: 2px solid #d0d0d0;
    border-radius: 4px; cursor: pointer; position: relative; flex-shrink: 0;
}
.cat-filter-label input[type="checkbox"]:checked {
    background: #3c7d17; border-color: #3c7d17;
}
.cat-filter-label input[type="checkbox"]:checked::after {
    content: '✓'; position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%); color: #fff; font-size: 11px; font-weight: 700;
}
.cat-filter-label .filter-name { font-size: 13px; color: #444; }

/* ───── Product Grid ───── */
.cat-products-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
@media (max-width: 1199px) { .cat-products-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px) { .cat-products-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }

/* ───── Pagination ───── */
.cat-pagination-wrap {
    margin-top: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: #fff;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.cat-pagination-info {
    font-size: 13px;
    color: #888;
}
.cat-pagination-info strong { color: #333; font-weight: 600; }
.cat-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
}
.cat-pagination .pagination {
    display: flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
    align-items: center;
}
.cat-pagination .pagination .page-item { display: inline-block; margin: 0; }
.cat-pagination .pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border: 1px solid #e8e8e8;
    border-radius: 6px;
    background: #fff;
    color: #555;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}
.cat-pagination .pagination .page-link:hover {
    border-color: #3c7d17;
    color: #3c7d17;
    background: rgba(60,125,23,0.04);
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(60,125,23,0.12);
}
.cat-pagination .pagination .active .page-link {
    background: #3c7d17;
    border-color: #3c7d17;
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(60,125,23,0.25);
    cursor: default;
}
.cat-pagination .pagination .active .page-link:hover {
    background: #3c7d17;
    border-color: #3c7d17;
    color: #fff;
    transform: none;
    box-shadow: 0 2px 8px rgba(60,125,23,0.25);
}
.cat-pagination .pagination .disabled .page-link {
    opacity: 0.35;
    pointer-events: none;
    background: #fafafa;
}
.cat-pagination .pagination .page-item:first-child .page-link,
.cat-pagination .pagination .page-item:last-child .page-link {
    padding: 0 14px;
    font-weight: 600;
    font-size: 13px;
}
.cat-pagination .disabled span.page-link {
    border: none;
    background: transparent;
    color: #bbb;
    font-size: 14px;
    letter-spacing: 2px;
    opacity: 1;
    min-width: auto;
    padding: 0 4px;
    pointer-events: none;
}
@media (max-width: 576px) {
    .cat-pagination-wrap {
        flex-direction: column;
        text-align: center;
        padding: 14px;
    }
    .cat-pagination .pagination .page-link {
        min-width: 32px;
        height: 32px;
        font-size: 12px;
        padding: 0 8px;
    }
}

/* ───── Empty ───── */
.cat-empty { text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px; }
.cat-empty i { font-size: 48px; color: #ddd; margin-bottom: 16px; }
.cat-empty h4 { font-size: 18px; color: #333; margin-bottom: 8px; }
.cat-empty p { color: #888; font-size: 14px; }

/* ───── Mobile ───── */
.cat-filter-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9998; }
.cat-filter-overlay.active { display: block; }

@media (max-width: 767px) {
    .mobile-filter-btn { display: flex; }
    .cat-sidebar {
        position: fixed; top: 0; left: 0; bottom: 0; width: 300px; max-width: 85vw;
        background: #fff; z-index: 9999; transform: translateX(-100%);
        transition: transform 0.35s ease; overflow-y: auto; padding: 0;
        box-shadow: 2px 0 20px rgba(0,0,0,0.15);
    }
    .cat-sidebar.active { transform: translateX(0); }
    .cat-sidebar-card { border-radius: 0; box-shadow: none; margin-bottom: 0; border-bottom: 1px solid #f0f0f0; }
    .cat-sidebar-sticky {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; background: #fff; border-bottom: 1px solid #eee;
        position: sticky; top: 0; z-index: 2;
    }
    .cat-sidebar-sticky h5 { font-size: 15px; font-weight: 600; margin: 0; }
    .cat-sidebar-close { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; }
    .cat-page-header { padding: 24px 0 20px; }
    .cat-page-header h1 { font-size: 22px; }
    .cat-toolbar { padding: 12px 14px; }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('seo'); ?>
<meta name="app-url" content="<?php echo e(route('category', $category->slug)); ?>" />
<meta name="robots" content="index, follow" />
<meta name="description" content="<?php echo e($category->meta_description); ?>" />
<meta name="keywords" content="<?php echo e($category->slug); ?>" />
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="<?php echo e($category->name); ?>" />
<meta name="twitter:title" content="<?php echo e($category->name); ?>" />
<meta name="twitter:description" content="<?php echo e($category->meta_description); ?>" />
<meta name="twitter:creator" content="Creative Design" />
<meta property="og:url" content="<?php echo e(route('category', $category->slug)); ?>" />
<meta name="twitter:image" content="<?php echo e(asset($category->image)); ?>" />
<meta property="og:title" content="<?php echo e($category->name); ?>" />
<meta property="og:type" content="product" />
<meta property="og:url" content="<?php echo e(route('category', $category->slug)); ?>" />
<meta property="og:image" content="<?php echo e(asset($category->image)); ?>" />
<meta property="og:description" content="<?php echo e($category->meta_description); ?>" />
<meta property="og:site_name" content="<?php echo e($category->name); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<section class="cat-page-header">
    <div class="container">
        <div class="cat-header-inner">
            <h1><?php echo e($category->name); ?></h1>
            <div class="cat-breadcrumb">
                <a href="<?php echo e(route('home')); ?>">Home</a>
                <span>›</span>
                <strong><?php echo e($category->name); ?></strong>
            </div>
        </div>
    </div>
</section>


<section class="cat-layout">
    <div class="container">
        
        <div class="cat-toolbar">
            <div class="cat-toolbar-left">
                <button class="mobile-filter-btn" id="catMobileFilterToggle">
                    <i class="fa fa-sliders-h"></i> Filter
                </button>
                <div class="showing-data">
                    Showing <strong><?php echo e($products->firstItem()); ?></strong>–<strong><?php echo e($products->lastItem()); ?></strong>
                    of <strong><?php echo e($products->total()); ?></strong> results
                </div>
            </div>
            <div class="cat-sort-wrapper">
                <label for="catSort">Sort by:</label>
                <form action="" class="sort-form" id="catSortForm">
                    <select name="sort" id="catSort" class="sort">
                        <option value="1" <?php echo e(request()->get('sort')==1 ? 'selected' : ''); ?>>Latest</option>
                        <option value="2" <?php echo e(request()->get('sort')==2 ? 'selected' : ''); ?>>Oldest</option>
                        <option value="3" <?php echo e(request()->get('sort')==3 ? 'selected' : ''); ?>>Price: High to Low</option>
                        <option value="4" <?php echo e(request()->get('sort')==4 ? 'selected' : ''); ?>>Price: Low to High</option>
                        <option value="5" <?php echo e(request()->get('sort')==5 ? 'selected' : ''); ?>>Name: A–Z</option>
                        <option value="6" <?php echo e(request()->get('sort')==6 ? 'selected' : ''); ?>>Name: Z–A</option>
                    </select>
                    <input type="hidden" name="min_price" value="<?php echo e(request()->get('min_price')); ?>" />
                    <input type="hidden" name="max_price" value="<?php echo e(request()->get('max_price')); ?>" />
                </form>
            </div>
        </div>

        <div class="row">
            
            <div class="col-lg-3">
                <div class="cat-sidebar" id="catSidebar">
                    <div class="cat-sidebar-sticky d-lg-none">
                        <h5>Filters</h5>
                        <button class="cat-sidebar-close" id="catSidebarClose">&times;</button>
                    </div>

                    
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title active" data-toggle="collapse" data-target="#catSidebarCategories">
                            Categories <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="cat-sidebar-body" id="catSidebarCategories">
                            <ul class="cat-list">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="cat-item">
                                    <?php $subs = $cat->subcategories ?? collect(); ?>
                                    <?php if($subs->count() > 0): ?>
                                    <button class="cat-toggle" data-toggle="subcat" data-target="#catSub-<?php echo e($cat->id); ?>">
                                        <span><?php echo e($cat->name); ?></span>
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                    <ul class="cat-sub-list" id="catSub-<?php echo e($cat->id); ?>">
                                        <li class="cat-sub-item">
                                            <a href="<?php echo e(route('category', $cat->slug)); ?>"
                                               class="cat-sub-link <?php echo e($cat->id == $category->id ? 'active' : ''); ?>">
                                                <?php echo e($cat->name); ?> (All)
                                            </a>
                                        </li>
                                        <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="cat-sub-item">
                                            <a href="<?php echo e(url('subcategory/'.$sub->slug)); ?>" class="cat-sub-link"><?php echo e($sub->subcategoryName); ?></a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php else: ?>
                                    <a href="<?php echo e(route('category', $cat->slug)); ?>"
                                       class="cat-link <?php echo e($cat->id == $category->id ? 'active' : ''); ?>">
                                        <?php echo e($cat->name); ?>

                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title active" data-toggle="collapse" data-target="#catSidebarPrice">
                            Price Range <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="cat-sidebar-body" id="catSidebarPrice">
                            <div class="cat-price-range">
                                <form action="" method="GET" id="catPriceForm">
                                    <?php if(request()->get('sort')): ?>
                                    <input type="hidden" name="sort" value="<?php echo e(request()->get('sort')); ?>" />
                                    <?php endif; ?>
                                    <div class="price-inputs">
                                        <div class="price-input-group">
                                            <span>৳</span>
                                            <input type="text" name="min_price" id="cat_price_min"
                                                   value="<?php echo e(request()->get('min_price', floor($globalMin ?? 0))); ?>" readonly />
                                        </div>
                                        <span class="price-separator">—</span>
                                        <div class="price-input-group">
                                            <span>৳</span>
                                            <input type="text" name="max_price" id="cat_price_max"
                                                   value="<?php echo e(request()->get('max_price', ceil($globalMax ?? 10000))); ?>" readonly />
                                        </div>
                                    </div>
                                    <div id="cat-price-range"></div>
                                    <div class="price-filter-actions">
                                        <button type="submit" class="btn-price-filter">Apply</button>
                                        <a href="<?php echo e(route('category', $category->slug)); ?>" class="btn-price-reset">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <?php if($subcategories->count() > 0): ?>
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title active" data-toggle="collapse" data-target="#catSidebarFilter">
                            Filter by Subcategory <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="cat-sidebar-body" id="catSidebarFilter">
                            <form action="" method="GET" class="attribute-submit">
                                <?php if(request()->get('sort')): ?>
                                <input type="hidden" name="sort" value="<?php echo e(request()->get('sort')); ?>" />
                                <?php endif; ?>
                                <ul class="cat-filter-list">
                                    <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="cat-filter-item">
                                        <label class="cat-filter-label">
                                            <input type="checkbox" name="subcategory[]" value="<?php echo e($subcat->id); ?>"
                                                   class="form-attribute"
                                                   <?php echo e(is_array(request()->get('subcategory')) && in_array($subcat->id, request()->get('subcategory')) ? 'checked' : ''); ?> />
                                            <span class="filter-name"><?php echo e($subcat->subcategoryName); ?></span>
                                        </label>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="cat-filter-overlay" id="catFilterOverlay"></div>
            </div>

            
            <div class="col-lg-9">
                <?php if($products->count() > 0): ?>
                <div class="cat-products-grid">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="cat-pagination-wrap">
                    <div class="cat-pagination-info">
                        Showing <strong><?php echo e($products->firstItem()); ?></strong>–<strong><?php echo e($products->lastItem()); ?></strong>
                        of <strong><?php echo e(number_format($products->total())); ?></strong> results
                        <?php if($products->total() > 0): ?>
                        <span style="color:#ccc;margin:0 8px;">|</span>
                        Page <strong><?php echo e($products->currentPage()); ?></strong> of <strong><?php echo e($products->lastPage()); ?></strong>
                        <?php endif; ?>
                    </div>
                    <div class="cat-pagination">
                        <?php echo e($products->appends(request()->query())->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
                <?php else: ?>
                <div class="cat-empty">
                    <i class="fa fa-box-open"></i>
                    <h4>No products found</h4>
                    <p>Try adjusting your filters or search criteria.</p>
                    <a href="<?php echo e(route('category', $category->slug)); ?>" class="btn-price-filter" style="display:inline-block;padding:10px 30px;text-decoration:none;">View All</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if($category->meta_description): ?>
<section style="background:#fff;padding:24px 0;">
    <div class="container">
        <div class="meta_des" style="font-size:13px;color:#888;line-height:1.7;">
            <?php echo $category->meta_description; ?>

        </div>
    </div>
</section>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/js/jquery-ui.js')); ?>"></script>
<script>
$(document).ready(function() {
    // Sort change
    $(".sort").change(function() { $("#catSortForm").submit(); });

    // Sidebar collapse toggle
    $('[data-toggle="collapse"]').on('click', function() {
        var target = $(this).data('target');
        $(target).toggleClass('hidden');
        $(this).toggleClass('active');
    });

    // Subcategory toggle
    $('[data-toggle="subcat"]').on('click', function() {
        var target = $(this).data('target');
        $(target).toggleClass('open');
        $(this).toggleClass('active');
    });

    // Auto-open active subcategory
    $('.cat-sub-link.active').closest('.cat-sub-list').addClass('open').prev('.cat-toggle').addClass('active');

    // Auto-submit on checkbox change
    $(".form-attribute").on('change', function() {
        $(".attribute-submit").submit();
    });

    // Price range slider
    var minVal = <?php echo e(request()->get('min_price', floor($globalMin ?? 0))); ?>;
    var maxVal = <?php echo e(request()->get('max_price', ceil($globalMax ?? 10000))); ?>;
    var absMin = <?php echo e(floor($globalMin ?? 0)); ?>;
    var absMax = <?php echo e(ceil($globalMax ?? 10000)); ?>;

    $("#cat-price-range").slider({
        range: true, min: absMin, max: absMax,
        values: [minVal, maxVal],
        slide: function(event, ui) {
            $("#cat_price_min").val(ui.values[0]);
            $("#cat_price_max").val(ui.values[1]);
        }
    });

    // Mobile filter toggle
    $("#catMobileFilterToggle").on('click', function() {
        $("#catSidebar").addClass('active');
        $("#catFilterOverlay").addClass('active');
        $('body').css('overflow', 'hidden');
    });

    function closeCatSidebar() {
        $("#catSidebar").removeClass('active');
        $("#catFilterOverlay").removeClass('active');
        $('body').css('overflow', '');
    }

    $("#catSidebarClose, #catFilterOverlay").on('click', closeCatSidebar);

    $(window).on('resize', function() {
        if ($(window).width() >= 768) closeCatSidebar();
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/pages/category.blade.php ENDPATH**/ ?>