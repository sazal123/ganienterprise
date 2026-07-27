<?php $__env->startSection('title', 'All Products'); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/jquery-ui.css')); ?>" />
<style>
/* ───── Shop Page Header ───── */
.shop-page-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 40px 0 35px;
    margin-top: 0;
    position: relative;
    overflow: hidden;
    display:none;
}
.shop-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: rgba(201, 168, 76, 0.06);
}
.shop-page-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: rgba(60, 125, 23, 0.08);
}
.shop-header-content {
    position: relative;
    z-index: 1;
}
.shop-page-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 6px;
}
.shop-page-header .shop-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.6);
}
.shop-page-header .shop-breadcrumb a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    transition: color 0.2s;
}
.shop-page-header .shop-breadcrumb a:hover {
    color: #C9A84C;
}
.shop-page-header .shop-breadcrumb span {
    color: rgba(255,255,255,0.3);
}
.shop-page-header .shop-breadcrumb strong {
    color: #C9A84C;
    font-weight: 600;
}

/* ───── Shop Layout ───── */
.shop-layout {
    padding: 30px 0 60px;
    background: #f8f8f6;
    min-height: 600px;
}

/* ───── Toolbar ───── */
.shop-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: #fff;
    padding: 14px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.shop-toolbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}
.shop-toolbar-left .showing-data {
    font-size: 13px;
    color: #666;
}
.mobile-filter-btn {
    display: none;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: #3c7d17;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}
.mobile-filter-btn:hover { background: #2d5d11; }
.mobile-filter-btn i { font-size: 14px; }

.shop-sort-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}
.shop-sort-wrapper label {
    font-size: 13px;
    color: #666;
    white-space: nowrap;
}
.shop-sort-wrapper select {
    padding: 7px 32px 7px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    color: #333;
    background: #fafafa url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23999'/%3E%3C/svg%3E") no-repeat right 12px center;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: border-color 0.2s;
}
.shop-sort-wrapper select:focus {
    border-color: #3c7d17;
    outline: none;
}

/* ───── Sidebar ───── */
.shop-sidebar {
    position: sticky;
    top: 100px;
}
.shop-sidebar-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 16px;
}
.shop-sidebar-card:last-child { margin-bottom: 0; }

.shop-sidebar-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    font-size: 14px;
    font-weight: 600;
    color: #1a1a2e;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s;
}
.shop-sidebar-title:hover { background: #fafaf8; }
.shop-sidebar-title i {
    font-size: 10px;
    color: #999;
    transition: transform 0.3s;
}
.shop-sidebar-title.active i {
    transform: rotate(180deg);
}

.shop-sidebar-body {
    padding: 6px 0;
}
.shop-sidebar-body.hidden { display: none; }

/* ─── Category Tree ─── */
.shop-cat-list { list-style: none; padding: 0; margin: 0; }
.shop-cat-item { border-bottom: 1px solid #f5f5f5; }
.shop-cat-item:last-child { border-bottom: none; }

.shop-cat-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 18px;
    font-size: 13px;
    color: #444;
    text-decoration: none;
    transition: all 0.2s;
}
.shop-cat-link:hover {
    background: #f8f8f6;
    color: #3c7d17;
}
.shop-cat-link.active {
    color: #3c7d17;
    font-weight: 600;
    background: rgba(60,125,23,0.04);
}
.shop-cat-link .cat-count {
    font-size: 11px;
    color: #aaa;
    background: #f5f5f5;
    padding: 1px 8px;
    border-radius: 10px;
}
.shop-cat-toggle {
    background: none;
    border: none;
    padding: 10px 18px;
    width: 100%;
    text-align: left;
    font-size: 13px;
    color: #444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s;
}
.shop-cat-toggle:hover {
    background: #f8f8f6;
    color: #3c7d17;
}
.shop-cat-toggle i {
    font-size: 10px;
    color: #bbb;
    transition: transform 0.3s;
}
.shop-cat-toggle.active i { transform: rotate(90deg); }

.shop-subcat-list {
    list-style: none;
    padding: 0 0 4px 20px;
    margin: 0;
    display: none;
}
.shop-subcat-list.open { display: block; }
.shop-subcat-item { }
.shop-subcat-link {
    display: block;
    padding: 8px 18px 8px 16px;
    font-size: 12px;
    color: #666;
    text-decoration: none;
    border-left: 2px solid transparent;
    transition: all 0.2s;
}
.shop-subcat-link:hover {
    color: #3c7d17;
    border-left-color: #3c7d17;
    background: rgba(60,125,23,0.03);
}

/* ─── Price Range ─── */
.shop-price-range { padding: 18px; }
.price-inputs {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 18px;
}
.price-input-group {
    flex: 1;
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border: 1px solid #e8e8e8;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 13px;
    color: #666;
}
.price-input-group span { margin-right: 4px; }
.price-input-group input {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    color: #333;
    outline: none;
}
.price-separator { color: #ccc; font-size: 13px; }

#price-range {
    margin: 0 4px;
    height: 4px;
    border: none;
    background: #e8e8e8;
    border-radius: 2px;
}
#price-range .ui-slider-range {
    background: linear-gradient(90deg, #3c7d17, #5a9e2a);
    border-radius: 2px;
}
#price-range .ui-slider-handle {
    width: 18px;
    height: 18px;
    border: 2px solid #3c7d17;
    background: #fff;
    border-radius: 50%;
    top: -7px;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    transition: transform 0.15s, box-shadow 0.15s;
}
#price-range .ui-slider-handle:hover,
#price-range .ui-slider-handle:focus {
    transform: scale(1.15);
    box-shadow: 0 2px 8px rgba(60,125,23,0.3);
}
#price-range .ui-slider-handle:focus { outline: none; }

.price-filter-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
}
.btn-price-filter {
    flex: 1;
    padding: 9px 0;
    background: #C9A84C;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    text-align: center;
}
.btn-price-filter:hover { background: #2d5d11; }
.btn-price-reset {
    padding: 9px 16px;
    background: #f0f0f0;
    color: #666;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-price-reset:hover { background: #e0e0e0; color: #333; }

/* ─── Filter Checkboxes ─── */
.shop-filter-list { list-style: none; padding: 4px 0; margin: 0; }
.shop-filter-item { padding: 0; }
.shop-filter-label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 18px;
    cursor: pointer;
    transition: background 0.2s;
}
.shop-filter-label:hover { background: #f8f8f6; }
.shop-filter-label input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 17px;
    height: 17px;
    border: 2px solid #d0d0d0;
    border-radius: 4px;
    cursor: pointer;
    position: relative;
    flex-shrink: 0;
    transition: all 0.2s;
}
.shop-filter-label input[type="checkbox"]:checked {
    background: #3c7d17;
    border-color: #3c7d17;
}
.shop-filter-label input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
}
.shop-filter-label .filter-name {
    font-size: 13px;
    color: #444;
}

/* ───── Product Grid ───── */
.shop-products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 1199px) {
    .shop-products-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 767px) {
    .shop-products-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
}

/* ───── Empty State ───── */
.shop-empty {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.shop-empty i {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 16px;
}
.shop-empty h4 {
    font-size: 18px;
    color: #333;
    margin-bottom: 8px;
}
.shop-empty p {
    color: #888;
    font-size: 14px;
}

/* ───── Pagination ───── */
.shop-pagination-wrap {
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
.shop-pagination-info {
    font-size: 13px;
    color: #888;
}
.shop-pagination-info strong {
    color: #333;
    font-weight: 600;
}
.shop-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
}
.shop-pagination .pagination {
    display: flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
    align-items: center;
}
.shop-pagination .pagination .page-item {
    display: inline-block;
    margin: 0;
}
.shop-pagination .pagination .page-link {
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
.shop-pagination .pagination .page-link:hover {
    border-color: #3c7d17;
    color: #3c7d17;
    background: rgba(60,125,23,0.04);
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(60,125,23,0.12);
}
.shop-pagination .pagination .active .page-link {
    background: #3c7d17;
    border-color: #3c7d17;
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(60,125,23,0.25);
    cursor: default;
}
.shop-pagination .pagination .active .page-link:hover {
    background: #3c7d17;
    border-color: #3c7d17;
    color: #fff;
    transform: none;
    box-shadow: 0 2px 8px rgba(60,125,23,0.25);
}
.shop-pagination .pagination .disabled .page-link {
    opacity: 0.35;
    pointer-events: none;
    background: #fafafa;
}
.shop-pagination .pagination .page-item:first-child .page-link,
.shop-pagination .pagination .page-item:last-child .page-link {
    padding: 0 14px;
    font-weight: 600;
    font-size: 13px;
}
/* Disabled dots styling */
.shop-pagination .pagination .disabled span.page-link {
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
    .shop-pagination-wrap {
        flex-direction: column;
        text-align: center;
        padding: 14px;
    }
    .shop-pagination .pagination .page-link {
        min-width: 32px;
        height: 32px;
        font-size: 12px;
        padding: 0 8px;
    }
}

/* ───── Mobile Sidebar Overlay ───── */
.shop-filter-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9998;
}
.shop-filter-overlay.active { display: block; }

@media (max-width: 767px) {
    .mobile-filter-btn { display: flex; }
    .shop-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 300px;
        max-width: 85vw;
        background: #fff;
        z-index: 9999;
        transform: translateX(-100%);
        transition: transform 0.35s ease;
        overflow-y: auto;
        padding: 0;
        box-shadow: 2px 0 20px rgba(0,0,0,0.15);
    }
    .shop-sidebar.active {
        transform: translateX(0);
    }
    .shop-sidebar-card {
        border-radius: 0;
        box-shadow: none;
        margin-bottom: 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .shop-sidebar-sticky {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: #fff;
        border-bottom: 1px solid #eee;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .shop-sidebar-sticky h5 {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }
    .shop-sidebar-close {
        background: none;
        border: none;
        font-size: 20px;
        color: #999;
        cursor: pointer;
        padding: 4px;
    }
    .shop-sidebar-close:hover { color: #333; }
    .shop-page-header { padding: 28px 0 24px; }
    .shop-page-header h1 { font-size: 24px; }
    .shop-toolbar { padding: 12px 14px; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<section class="shop-page-header">
    <div class="container">
        <div class="shop-header-content">
            <h1>All Products</h1>
            <div class="shop-breadcrumb">
                <a href="<?php echo e(route('home')); ?>">Home</a>
                <span>›</span>
                <strong>All Products</strong>
            </div>
        </div>
    </div>
</section>


<section class="shop-layout">
    <div class="container">
        
        <div class="shop-toolbar">
            <div class="shop-toolbar-left">
                <button class="mobile-filter-btn" id="mobileFilterToggle">
                    <i class="fa fa-sliders-h"></i> Filter
                </button>
                <div class="showing-data">
                    Showing <strong><?php echo e($products->firstItem()); ?></strong>–<strong><?php echo e($products->lastItem()); ?></strong> of <strong><?php echo e($products->total()); ?></strong> results
                </div>
            </div>
            <div class="shop-sort-wrapper">
                <label for="sortSelect">Sort by:</label>
                <form action="" class="sort-form" id="sortForm">
                    <select name="sort" id="sortSelect" class="sort">
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
                <div class="shop-sidebar" id="shopSidebar">
                    
                    <div class="shop-sidebar-sticky d-lg-none">
                        <h5>Filters</h5>
                        <button class="shop-sidebar-close" id="shopSidebarClose">&times;</button>
                    </div>

                    
                    <div class="shop-sidebar-card">
                        <div class="shop-sidebar-title active" data-toggle="sidebar-collapse" data-target="#sidebarCategories">
                            Categories
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="shop-sidebar-body" id="sidebarCategories">
                            <ul class="shop-cat-list">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="shop-cat-item">
                                    <?php
                                        $subcats = $category->subcategories ?? collect();
                                        $catActive = request()->routeIs('category') && request()->segment(2) == $category->slug;
                                    ?>
                                    <?php if($subcats->count() > 0): ?>
                                    <button class="shop-cat-toggle" data-toggle="subcat" data-target="#subcat-<?php echo e($category->id); ?>">
                                        <span><?php echo e($category->name); ?></span>
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                    <ul class="shop-subcat-list" id="subcat-<?php echo e($category->id); ?>">
                                        <li class="shop-subcat-item">
                                            <a href="<?php echo e(route('category', $category->slug)); ?>" class="shop-subcat-link <?php echo e($catActive ? 'active' : ''); ?>">
                                                <?php echo e($category->name); ?> (All)
                                            </a>
                                        </li>
                                        <?php $__currentLoopData = $subcats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="shop-subcat-item">
                                            <a href="<?php echo e(url('subcategory/'.$subcat->slug)); ?>" class="shop-subcat-link">
                                                <?php echo e($subcat->subcategoryName); ?>

                                            </a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php else: ?>
                                    <a href="<?php echo e(route('category', $category->slug)); ?>" class="shop-cat-link <?php echo e($catActive ? 'active' : ''); ?>">
                                        <span><?php echo e($category->name); ?></span>
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="shop-sidebar-card">
                        <div class="shop-sidebar-title active" data-toggle="sidebar-collapse" data-target="#sidebarPrice">
                            Price Range
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="shop-sidebar-body" id="sidebarPrice">
                            <div class="shop-price-range">
                                <form action="" method="GET" id="priceFilterForm">
                                    <?php if(request()->get('sort')): ?>
                                    <input type="hidden" name="sort" value="<?php echo e(request()->get('sort')); ?>" />
                                    <?php endif; ?>
                                    <div class="price-inputs">
                                        <div class="price-input-group">
                                            <span>৳</span>
                                            <input type="text" name="min_price" id="price_min"
                                                   value="<?php echo e(request()->get('min_price', floor($globalMin ?? 0))); ?>"
                                                   readonly />
                                        </div>
                                        <span class="price-separator">—</span>
                                        <div class="price-input-group">
                                            <span>৳</span>
                                            <input type="text" name="max_price" id="price_max"
                                                   value="<?php echo e(request()->get('max_price', ceil($globalMax ?? 10000))); ?>"
                                                   readonly />
                                        </div>
                                    </div>
                                    <div id="price-range"></div>
                                    <div class="price-filter-actions">
                                        <button type="submit" class="btn-price-filter">Apply</button>
                                        <a href="<?php echo e(route('shop')); ?>" class="btn-price-reset">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <?php if(isset($categories) && $categories->count() > 0): ?>
                    <div class="shop-sidebar-card d-lg-none">
                        <div class="shop-sidebar-title active" data-toggle="sidebar-collapse" data-target="#sidebarFilter">
                            Filter by Category
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="shop-sidebar-body" id="sidebarFilter">
                            <form action="" method="GET" id="filterForm">
                                <?php if(request()->get('sort')): ?>
                                <input type="hidden" name="sort" value="<?php echo e(request()->get('sort')); ?>" />
                                <?php endif; ?>
                                <ul class="shop-filter-list">
                                    <li class="shop-filter-item">
                                        <label class="shop-filter-label">
                                            <input type="radio" name="category_filter" value=""
                                                   <?php echo e(!request()->get('category_filter') ? 'checked' : ''); ?> />
                                            <span class="filter-name">All Categories</span>
                                        </label>
                                    </li>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="shop-filter-item">
                                        <label class="shop-filter-label">
                                            <input type="radio" name="category_filter" value="<?php echo e($category->slug); ?>"
                                                   <?php echo e(request()->get('category_filter') == $category->slug ? 'checked' : ''); ?> />
                                            <span class="filter-name"><?php echo e($category->name); ?></span>
                                        </label>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="shop-filter-overlay" id="shopFilterOverlay"></div>
            </div>

            
            <div class="col-lg-9">
                <?php if($products->count() > 0): ?>
                <div class="shop-products-grid">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div class="shop-pagination-wrap">
                    <div class="shop-pagination-info">
                        Showing <strong><?php echo e($products->firstItem()); ?></strong>–<strong><?php echo e($products->lastItem()); ?></strong>
                        of <strong><?php echo e(number_format($products->total())); ?></strong> results
                        <?php if($products->total() > 0): ?>
                        <span style="color:#ccc;margin:0 8px;">|</span>
                        Page <strong><?php echo e($products->currentPage()); ?></strong> of <strong><?php echo e($products->lastPage()); ?></strong>
                        <?php endif; ?>
                    </div>
                    <div class="shop-pagination">
                        <?php echo e($products->appends(request()->query())->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
                <?php else: ?>
                <div class="shop-empty">
                    <i class="fa fa-box-open"></i>
                    <h4>No products found</h4>
                    <p>Try adjusting your filters or search criteria.</p>
                    <a href="<?php echo e(route('shop')); ?>" class="btn-price-filter" style="display:inline-block;padding:10px 30px;text-decoration:none;">View All Products</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/js/jquery-ui.js')); ?>"></script>
<script>
$(document).ready(function() {
    // ── Sort change ──
    $(".sort").change(function() {
        $("#sortForm").submit();
    });

    // ── Sidebar accordion toggle ──
    $('[data-toggle="sidebar-collapse"]').on('click', function() {
        var target = $(this).data('target');
        $(target).toggleClass('hidden');
        $(this).toggleClass('active');
    });

    // ── Subcategory toggle ──
    $('[data-toggle="subcat"]').on('click', function() {
        var target = $(this).data('target');
        $(target).toggleClass('open');
        $(this).toggleClass('active');
    });

    // ── Open subcategory if active ──
    $('.shop-subcat-link.active').closest('.shop-subcat-list').addClass('open').prev('.shop-cat-toggle').addClass('active');

    // ── Price range slider ──
    var minVal = <?php echo e(request()->get('min_price', floor($globalMin ?? 0))); ?>;
    var maxVal = <?php echo e(request()->get('max_price', ceil($globalMax ?? 10000))); ?>;
    var absMin = <?php echo e(floor($globalMin ?? 0)); ?>;
    var absMax = <?php echo e(ceil($globalMax ?? 10000)); ?>;

    $("#price-range").slider({
        range: true,
        min: absMin,
        max: absMax,
        values: [minVal, maxVal],
        slide: function(event, ui) {
            $("#price_min").val(ui.values[0]);
            $("#price_max").val(ui.values[1]);
        }
    });

    // ── Mobile filter toggle ──
    $("#mobileFilterToggle").on('click', function() {
        $("#shopSidebar").addClass('active');
        $("#shopFilterOverlay").addClass('active');
        $('body').css('overflow', 'hidden');
    });

    function closeSidebar() {
        $("#shopSidebar").removeClass('active');
        $("#shopFilterOverlay").removeClass('active');
        $('body').css('overflow', '');
    }

    $("#shopSidebarClose, #shopFilterOverlay").on('click', closeSidebar);

    // ── Radio filter auto-submit ──
    $('input[name="category_filter"]').on('change', function() {
        $("#filterForm").submit();
    });

    // ── Responsive: reset body overflow on window resize ──
    $(window).on('resize', function() {
        if ($(window).width() >= 768) {
            closeSidebar();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/pages/shop.blade.php ENDPATH**/ ?>