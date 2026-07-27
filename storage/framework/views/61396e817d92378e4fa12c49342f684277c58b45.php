<?php $__env->startSection('title', $details->name); ?>
<?php $__env->startPush('seo'); ?>
<meta name="app-url" content="<?php echo e(route('product', $details->slug)); ?>" />
<meta name="robots" content="index, follow" />
<meta name="description" content="<?php echo e($details->meta_description); ?>" />
<meta name="keywords" content="<?php echo e($details->slug); ?>" />
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="<?php echo e($details->name); ?>" />
<meta name="twitter:title" content="<?php echo e($details->name); ?>" />
<meta name="twitter:description" content="<?php echo e($details->meta_description); ?>" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="<?php echo e(route('product', $details->slug)); ?>" />
<meta name="twitter:image" content="<?php echo e(asset($details->image ? $details->image->image : '')); ?>" />
<meta property="og:title" content="<?php echo e($details->name); ?>" />
<meta property="og:type" content="product" />
<meta property="og:url" content="<?php echo e(route('product', $details->slug)); ?>" />
<meta property="og:image" content="<?php echo e(asset($details->image ? $details->image->image : '')); ?>" />
<meta property="og:description" content="<?php echo e($details->meta_description); ?>" />
<meta property="og:site_name" content="<?php echo e($details->name); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/zoomsl.css')); ?>">
<style>
/* ===== PRODUCT DETAILS — Redesigned ===== */

/* ── Header Banner ── */
.pdp-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 32px 0 28px;
    position: relative;
    overflow: hidden;
}
.pdp-header::before {
    content: '';
    position: absolute; top: -50%; right: -20%;
    width: 500px; height: 500px; border-radius: 50%;
    background: rgba(201,168,76,0.06);
}
.pdp-header::after {
    content: '';
    position: absolute; bottom: -30%; left: -10%;
    width: 300px; height: 300px; border-radius: 50%;
    background: rgba(60,125,23,0.08);
}
.pdp-header-inner { position: relative; z-index: 1; }
.pdp-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 26px; font-weight: 700; color: #fff; margin: 0 0 4px;
}
.pdp-breadcrumb {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    font-size: 12px; color: rgba(255,255,255,0.6);
}
.pdp-breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; transition: color 0.2s; }
.pdp-breadcrumb a:hover { color: #C9A84C; }
.pdp-breadcrumb span { color: rgba(255,255,255,0.3); }
.pdp-breadcrumb strong { color: #C9A84C; font-weight: 600; }

/* ── Main Product Section ── */
.pdp-main { padding: 30px 0; background: #f8f8f6; }
.pdp-layout { display: flex; gap: 30px; }
.pdp-gallery { flex: 0 0 50%; max-width: 50%; }
.pdp-info  { flex: 0 0 50%; max-width: 50%; }

/* ── Main Image Stage ── */
.pdp-gallery-stage {
    position: relative;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}
.pdp-gallery-badge {
    position: absolute; top: 18px; left: 18px; z-index: 10;
    background: linear-gradient(135deg, #C41E3A, #a01830);
    color: #fff; padding: 7px 16px; border-radius: 6px;
    font-size: 13px; font-weight: 800; letter-spacing: 0.5px;
    box-shadow: 0 3px 10px rgba(196,30,58,0.35);
}
.pdp-main-swiper { width: 100%; aspect-ratio: 1/1; background: #fafafa; }
.pdp-main-swiper .swiper-slide {
    display: flex; align-items: center; justify-content: center;
}
.pdp-main-swiper .swiper-slide img {
    width: 100%; height: 100%; object-fit: contain;
    user-select: none; -webkit-user-drag: none;
}
/* Swiper nav arrows */
.pdp-main-swiper .swiper-button-prev,
.pdp-main-swiper .swiper-button-next {
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(255,255,255,0.92);
    box-shadow: 0 2px 10px rgba(0,0,0,0.12);
    transition: all 0.25s ease; opacity: 0;
}
.pdp-gallery-stage:hover .swiper-button-prev,
.pdp-gallery-stage:hover .swiper-button-next { opacity: 1; }
.pdp-main-swiper .swiper-button-prev:hover,
.pdp-main-swiper .swiper-button-next:hover {
    background: #fff; color: #3c7d17;
    transform: scale(1.08); box-shadow: 0 4px 16px rgba(0,0,0,0.18);
}
.pdp-main-swiper .swiper-button-prev::after,
.pdp-main-swiper .swiper-button-next::after { font-size: 16px; font-weight: 700; }
.pdp-main-swiper .swiper-button-prev { left: 10px; }
.pdp-main-swiper .swiper-button-next { right: 10px; }
/* Pagination dots */
.pdp-swiper-pagination { bottom: 10px !important; }
.pdp-swiper-pagination .swiper-pagination-bullet {
    width: 8px; height: 8px; background: #ddd; opacity: 1;
}
.pdp-swiper-pagination .swiper-pagination-bullet-active {
    background: #3c7d17; width: 22px; border-radius: 4px;
}

/* ── 3D Zoom Lens & Result ── */
.pdp-zoom-lens {
    display: none; position: absolute; top: 0; left: 0;
    width: 160px; height: 160px; border-radius: 50%;
    border: 3px solid rgba(60,125,23,0.5);
    background: rgba(255,255,255,0.08);
    pointer-events: none; z-index: 10;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.4), inset 0 0 0 1px rgba(255,255,255,0.2);
    transform: translate(-50%, -50%);
    backdrop-filter: blur(1px);
}
.pdp-zoom-result {
    display: none; position: absolute; top: 0;
    left: calc(100% + 20px); width: 420px; height: 420px;
    border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    border: 2px solid #fff; z-index: 20;
    background-repeat: no-repeat; background-color: #fff;
    overflow: hidden;
}
.pdp-zoom-label {
    position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);
    background: rgba(0,0,0,0.6); color: #fff; font-size: 11px;
    padding: 4px 12px; border-radius: 20px; white-space: nowrap;
    backdrop-filter: blur(4px); letter-spacing: 0.5px;
}
.pdp-zoom-lens.show, .pdp-zoom-result.show { display: block; }

@media (max-width: 1199px) { .pdp-zoom-result { width: 320px; height: 320px; } }
@media (max-width: 991px) {
    .pdp-zoom-result, .pdp-zoom-lens { display: none !important; }
}

/* ── Thumbnails ── */
.pdp-thumb-strip { overflow: hidden; }
.pdp-thumb-scroll {
    display: flex; gap: 8px; overflow-x: auto; scroll-behavior: smooth;
    padding-bottom: 6px; scrollbar-width: thin;
    scrollbar-color: #d0d0d0 #f0f0f0; -webkit-overflow-scrolling: touch;
}
.pdp-thumb-scroll::-webkit-scrollbar { height: 4px; }
.pdp-thumb-scroll::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 2px; }
.pdp-thumb-scroll::-webkit-scrollbar-thumb { background: #d0d0d0; border-radius: 2px; }
.pdp-thumb-item {
    flex: 0 0 80px; width: 80px; height: 80px; border-radius: 8px; overflow: hidden;
    border: 2.5px solid transparent; cursor: pointer; transition: all 0.2s ease;
    background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.pdp-thumb-item:hover { border-color: #bbb; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.pdp-thumb-item.active { border-color: #3c7d17; box-shadow: 0 2px 8px rgba(60,125,23,0.2); }
.pdp-thumb-item img { width: 100%; height: 100%; object-fit: cover; pointer-events: none; }

/* ── Color Swatches ── */
.pdp-gallery-colors {
    display: flex; align-items: center; gap: 10px;
    margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0;
}
.pdp-gallery-colors-label { font-size: 12px; font-weight: 600; color: #666; white-space: nowrap; }
.pdp-gallery-swatches { display: flex; gap: 8px; flex-wrap: wrap; }
.pdp-gallery-swatch {
    width: 28px; height: 28px; border-radius: 50%;
    border: 2.5px solid transparent; cursor: pointer; transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12); position: relative;
}
.pdp-gallery-swatch:hover { transform: scale(1.12); }
.pdp-gallery-swatch.active {
    border-color: #3c7d17; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #3c7d17; transform: scale(1.1);
}
.pdp-gallery-swatch .swatch-tooltip {
    display: none; position: absolute; bottom: calc(100% + 6px); left: 50%;
    transform: translateX(-50%); background: #333; color: #fff;
    font-size: 10px; padding: 3px 8px; border-radius: 4px; white-space: nowrap; pointer-events: none;
}
.pdp-gallery-swatch:hover .swatch-tooltip { display: block; }

@media (max-width: 991px) {
    .pdp-gallery, .pdp-info { flex: 0 0 100%; max-width: 100%; }
    .pdp-thumb-item { flex: 0 0 64px; width: 64px; height: 64px; }
    .pdp-main-swiper .swiper-button-prev,
    .pdp-main-swiper .swiper-button-next { opacity: 1; }
}
@media (max-width: 576px) {
    .pdp-gallery-badge { top: 10px; left: 10px; padding: 5px 12px; font-size: 11px; }
}

/* ── Product Info ── */
.pdp-name {
    font-family: 'Playfair Display', serif;
    font-size: 24px; font-weight: 700; color: #1a1a2e; margin: 0 0 6px; line-height: 1.3;
}
.pdp-sku { font-size: 12px; color: #999; margin-bottom: 12px; }

.pdp-price-row {
    display: flex; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap;
}
.pdp-current-price { font-size: 28px; font-weight: 800; color: #3c7d17; }
.pdp-old-price { font-size: 16px; color: #bbb; text-decoration: line-through; }
.pdp-save-badge {
    background: #C41E3A; color: #fff; padding: 3px 10px; border-radius: 4px;
    font-size: 12px; font-weight: 700;
}

.pdp-installment {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #f0f7ee, #e4f0de); border: 1px solid #c8dfbe; border-radius: 6px;
    padding: 8px 14px; font-size: 12px; color: #2d5d11; margin-bottom: 12px;
    font-weight: 500;
}
.pdp-installment i { font-size: 16px; }

.pdp-shipping-info {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: #888; margin-bottom: 16px; padding-bottom: 16px;
    border-bottom: 1px solid #f0f0f0;
}
.pdp-shipping-info i { color: #3c7d17; font-size: 14px; }

/* ── Variant Selectors ── */
.pdp-variant-section { margin-bottom: 16px; }
.pdp-variant-label {
    font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;
}
.pdp-color-swatches {
    display: flex; gap: 10px; flex-wrap: wrap;
}
.pdp-color-swatch {
    position: relative; width: 36px; height: 36px; border-radius: 50%;
    border: 3px solid transparent; cursor: pointer; transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.pdp-color-swatch:hover { transform: scale(1.1); }
.pdp-color-swatch.active { border-color: #3c7d17; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #3c7d17; }
.pdp-color-swatch input { position: absolute; opacity: 0; width: 0; height: 0; }
.pdp-color-name { font-size: 12px; color: #666; margin-top: 4px; }

.pdp-size-options {
    display: flex; gap: 8px; flex-wrap: wrap;
}
.pdp-size-btn {
    position: relative;
    min-width: 44px; height: 36px; padding: 0 14px;
    border: 1.5px solid #ddd; border-radius: 6px; background: #fff;
    font-size: 13px; font-weight: 500; color: #444; cursor: pointer;
    transition: all 0.2s; display: flex; align-items: center; justify-content: center;
}
.pdp-size-btn:hover { border-color: #3c7d17; color: #3c7d17; }
.pdp-size-btn.active { border-color: #3c7d17; background: #3c7d17; color: #fff; }
.pdp-size-btn input { position: absolute; opacity: 0; width: 0; height: 0; }
.pdp-size-btn .size-price {
    display: block; font-size: 10px; font-weight: 400; opacity: 0.8;
}

/* ── Qty + Buttons ── */
.pdp-qty-row { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; }
.pdp-qty {
    display: flex; align-items: center; border: 1.5px solid #e0e0e0; border-radius: 8px; overflow: hidden;
}
.pdp-qty button {
    width: 36px; height: 38px; border: none; background: #f8f8f8;
    font-size: 16px; font-weight: 600; color: #444; cursor: pointer; transition: background 0.2s;
}
.pdp-qty button:hover { background: #eee; }
.pdp-qty input {
    width: 50px; height: 38px; border: none; border-left: 1.5px solid #e0e0e0; border-right: 1.5px solid #e0e0e0;
    text-align: center; font-size: 15px; font-weight: 600; color: #333; outline: none;
}

.pdp-actions { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
.pdp-add-cart {
    flex: 1; min-width: 180px; padding: 14px 28px;
    background: #3c7d17; color: #fff;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.25s ease;
    text-transform: uppercase; letter-spacing: 1px;
    box-shadow: 0 3px 10px rgba(60,125,23,0.25);
}
.pdp-add-cart:hover {
    background: #2d5d11; color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(60,125,23,0.35);
}
.pdp-buy-now {
    flex: 1; min-width: 180px; padding: 14px 28px;
    background: #C9A84C; color: #1a1a2e;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.25s ease;
    text-transform: uppercase; letter-spacing: 1px;
    box-shadow: 0 3px 10px rgba(201,168,76,0.25);
}
.pdp-buy-now:hover {
    background: #b8942e; color: #1a1a2e;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(201,168,76,0.35);
}

/* ── Feature Icons ── */

/* ── Feature Icons ── */
.pdp-features {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; padding: 16px 0;
    border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
}
.pdp-feature-item {
    display: flex; align-items: center; gap: 8px;
    background: #f8f8f6; padding: 8px 14px; border-radius: 6px; font-size: 12px; color: #555;
}
.pdp-feature-item i { font-size: 16px; color: #3c7d17; }

/* ── Trust Badges ── */
.pdp-trust {
    display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
}
.pdp-trust-item {
    display: flex; align-items: center; gap: 8px; font-size: 12px; color: #666;
}
.pdp-trust-item i { font-size: 18px; color: #C9A84C; }

/* ── Stock Out ── */
.pdp-stock-out {
    display: inline-block; padding: 8px 20px; background: #fff0f0; border: 1px solid #ff4444;
    border-radius: 6px; color: #ff4444; font-size: 14px; font-weight: 700; margin-bottom: 16px;
}

/* ── Contact Buttons ── */
.pdp-contact-row { display: flex; gap: 10px; margin-bottom: 16px; }
.pdp-contact-btn {
    flex: 1; padding: 11px; border-radius: 8px; font-size: 12px; font-weight: 600;
    text-align: center; text-decoration: none; transition: all 0.25s ease; display: flex;
    align-items: center; justify-content: center; gap: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.pdp-call-btn { background: #3c7d17; color: #fff; }
.pdp-call-btn:hover { background: #2d5d11; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(60,125,23,0.25); }
.pdp-whatsapp-btn { background: #25D366; color: #fff; }
.pdp-whatsapp-btn:hover { background: #1da851; color: #fff; transform: translateY(-1px); }

/* ── Shipping Table ── */
.pdp-shipping-table { margin-bottom: 20px; }
.pdp-shipping-table table {
    width: 100%; border-collapse: collapse; font-size: 12px; border: 1px solid #eee; border-radius: 6px; overflow: hidden;
}
.pdp-shipping-table th {
    background: #3c7d17; color: #fff; padding: 8px 12px; text-align: center; font-weight: 600;
}
.pdp-shipping-table td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; color: #555; }
.pdp-shipping-table tr:last-child td { border-bottom: none; }

/* ── Tabs ── */
.pdp-tabs {
    background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; margin-top: 30px;
}
.pdp-tab-nav {
    display: flex; border-bottom: 1px solid #f0f0f0; background: #fafaf8; overflow-x: auto;
}
.pdp-tab-btn {
    padding: 14px 24px; border: none; background: none; font-size: 13px; font-weight: 600;
    color: #888; cursor: pointer; transition: all 0.2s; white-space: nowrap;
    border-bottom: 2px solid transparent;
}
.pdp-tab-btn:hover { color: #333; }
.pdp-tab-btn.active { color: #3c7d17; border-bottom-color: #3c7d17; background: #fff; }
.pdp-tab-content { padding: 24px; }
.pdp-tab-pane { display: none; }
.pdp-tab-pane.active { display: block; }
.pdp-tab-pane h3 { font-size: 18px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
.pdp-tab-pane p { font-size: 14px; line-height: 1.7; color: #555; }

/* ── Review Section ── */
.pdp-review-summary {
    display: flex; gap: 24px; align-items: center; margin-bottom: 24px;
    padding: 20px; background: #f8f8f6; border-radius: 8px;
}
.pdp-review-score { text-align: center; }
.pdp-review-score .score { font-size: 36px; font-weight: 800; color: #1a1a2e; line-height: 1; }
.pdp-review-score .stars { font-size: 16px; color: #f5a623; margin: 4px 0; }
.pdp-review-score .total { font-size: 12px; color: #888; }
.pdp-review-list { max-height: 500px; overflow-y: auto; }
.pdp-review-card {
    padding: 16px 0; border-bottom: 1px solid #f0f0f0;
}
.pdp-review-card:last-child { border-bottom: none; }
.pdp-review-card .reviewer { font-size: 14px; font-weight: 600; color: #333; }
.pdp-review-card .review-date { font-size: 11px; color: #aaa; margin-left: 8px; }
.pdp-review-card .review-stars { font-size: 13px; color: #f5a623; margin: 4px 0; }
.pdp-review-card .review-text { font-size: 13px; color: #666; line-height: 1.5; }

/* ── Write Review Modal ── */
.pdp-write-review-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 24px; background: #3c7d17; color: #fff; border: none;
    border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;
    transition: all 0.25s ease; letter-spacing: 0.5px;
    box-shadow: 0 3px 10px rgba(60,125,23,0.2);
}
.pdp-write-review-btn:hover { background: #2d5d11; color: #fff; transform: translateY(-1px); box-shadow: 0 5px 15px rgba(60,125,23,0.3); }

/* ── Related / Pair Sections ── */
.pdp-section { padding: 30px 0; }
.pdp-section-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 700; color: #1a1a2e; text-align: center; margin-bottom: 24px;
}
.pdp-section-divider {
    width: 60px; height: 3px; background: #3c7d17; margin: 0 auto 24px; border-radius: 2px;
}
.pdp-carousel {
    display: flex; gap: 16px; overflow-x: auto; padding-bottom: 8px; scroll-snap-type: x mandatory;
}
.pdp-carousel-item {
    flex: 0 0 200px; scroll-snap-align: start;
}

/* ── Note Banner ── */
.pdp-note {
    background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px;
    padding: 10px 14px; font-size: 13px; color: #856404; margin-bottom: 16px;
}
.pdp-note strong { font-weight: 700; }

/* ── Video Section ── */
.pdp-video { margin-top: 24px; }
.pdp-video h3 { font-size: 16px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
.pdp-video iframe { width: 100%; border-radius: 8px; }

/* ── Star Rating in Modal ── */
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
.star-rating input { display: none; }
.star-rating label { cursor: pointer; font-size: 24px; color: #ddd; transition: color 0.2s; }
.star-rating label i { font-size: 24px; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #f5a623; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .pdp-layout { flex-direction: column; }
    .pdp-gallery, .pdp-info { flex: 0 0 100%; max-width: 100%; }
    .pdp-header h1 { font-size: 20px; }
    .pdp-name { font-size: 20px; }
    .pdp-current-price { font-size: 24px; }
}
@media (max-width: 576px) {
    .pdp-main { padding: 16px 0; }
    .pdp-actions { flex-direction: column; }
    .pdp-add-cart, .pdp-buy-now { min-width: 100%; }
    .pdp-review-summary { flex-direction: column; text-align: center; }
    .pdp-tab-btn { padding: 12px 16px; font-size: 12px; }
    .pdp-features { gap: 8px; }
    .pdp-feature-item { padding: 6px 10px; font-size: 11px; }
    .pdp-trust { gap: 10px; }
    .pdp-contact-row { flex-direction: column; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<section class="pdp-header">
    <div class="container">
        <div class="pdp-header-inner">
            <div class="pdp-breadcrumb">
                <a href="<?php echo e(route('home')); ?>">Home</a>
                <?php if($details->category): ?>
                <span>›</span>
                <a href="<?php echo e(route('category', $details->category->slug)); ?>"><?php echo e($details->category->name); ?></a>
                <?php endif; ?>
                <?php if($details->subcategory): ?>
                <span>›</span>
                <a href="<?php echo e(url('subcategory/'.$details->subcategory->slug)); ?>"><?php echo e($details->subcategory->subcategoryName); ?></a>
                <?php endif; ?>
                <span>›</span>
                <strong><?php echo e(Str::limit($details->name, 40)); ?></strong>
            </div>
        </div>
    </div>
</section>


<section class="pdp-main">
    <div class="container">
        <div class="pdp-layout">
            
            <div class="pdp-gallery">
                
                <div class="pdp-gallery-stage">
                    <?php $discount = $details->old_price ? round((($details->old_price - $details->new_price) / $details->old_price) * 100) : 0; ?>
                    <?php if($discount > 0): ?>
                    <div class="pdp-gallery-badge">-<?php echo e($discount); ?>%</div>
                    <?php endif; ?>

                    <div class="swiper pdp-main-swiper" id="pdpMainSwiper">
                        <div class="swiper-wrapper" id="pdpSwiperWrapper">
                            <?php $__empty_1 = true; $__currentLoopData = $details->mainImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="swiper-slide dimage_item" data-color-id="<?php echo e($img->color_id ?? ''); ?>">
                                <img src="<?php echo e(asset($img->image)); ?>" alt="<?php echo e($details->name); ?>" />
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="swiper-slide dimage_item">
                                <img src="<?php echo e(asset('frontEnd/img/default-product.jpg')); ?>" alt="<?php echo e($details->name); ?>" />
                            </div>
                            <?php endif; ?>
                            <?php $__currentLoopData = $details->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($img->color_id): ?>
                                <div class="swiper-slide dimage_item" data-color-id="<?php echo e($img->color_id); ?>" style="display:none;">
                                    <img src="<?php echo e(asset($img->image)); ?>" alt="<?php echo e($details->name); ?>" />
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                        <div class="pdp-zoom-lens" id="pdpZoomLens"></div>
                        <div class="pdp-zoom-result" id="pdpZoomResult">
                            <div class="pdp-zoom-label">🔍 Zoom</div>
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-pagination pdp-swiper-pagination"></div>
                    </div>
                </div>

                
                <div class="pdp-thumb-strip">
                    <div class="pdp-thumb-scroll" id="pdpThumbScroll">
                        <?php $__empty_1 = true; $__currentLoopData = $details->mainImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="pdp-thumb-item <?php echo e($key === 0 ? 'active' : ''); ?>"
                             data-index="<?php echo e($key); ?>"
                             data-src="<?php echo e(asset($img->image)); ?>"
                             data-color-id="<?php echo e($img->color_id ?? ''); ?>">
                            <img src="<?php echo e(asset($img->image)); ?>" alt="" />
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="pdp-thumb-item active" data-index="0" data-src="<?php echo e(asset('frontEnd/img/default-product.jpg')); ?>" data-color-id="">
                            <img src="<?php echo e(asset('frontEnd/img/default-product.jpg')); ?>" alt="" />
                        </div>
                        <?php endif; ?>
                        <?php $mainCount = $details->mainImages->count(); ?>
                        <?php $__currentLoopData = $details->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($img->color_id): ?>
                            <div class="pdp-thumb-item"
                                 data-index="<?php echo e($mainCount + $loop->index); ?>"
                                 data-src="<?php echo e(asset($img->image)); ?>"
                                 data-color-id="<?php echo e($img->color_id); ?>"
                                 style="display:none;">
                                <img src="<?php echo e(asset($img->image)); ?>" alt="" />
                            </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <?php if($productcolors->count() > 0): ?>
                <div class="pdp-gallery-colors">
                    <span class="pdp-gallery-colors-label">Colors:</span>
                    <div class="pdp-gallery-swatches" id="pdpGallerySwatches">
                        <?php $__currentLoopData = $productcolors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $procolor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $hex = $procolor->color ? $procolor->color->color : '#ccc'; ?>
                        <button class="pdp-gallery-swatch <?php if($loop->first): ?> active <?php endif; ?>"
                                style="background-color: <?php echo e($hex); ?>;"
                                title="<?php echo e($procolor->color ? $procolor->color->colorName : ''); ?>"
                                data-color-id="<?php echo e($procolor->color_id); ?>">
                            <span class="swatch-tooltip"><?php echo e($procolor->color ? $procolor->color->colorName : ''); ?></span>
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="pdp-info">
                <h1 class="pdp-name"><?php echo e($details->name); ?></h1>
                <div class="pdp-sku">SKU: <?php echo e($details->product_code); ?></div>

                
                <div class="pdp-price-row">
                    <span class="pdp-current-price" id="pdpNewPrice">৳<?php echo e(number_format($details->new_price)); ?></span>
                    <?php if($details->old_price): ?>
                    <span class="pdp-old-price" id="pdpOldPrice">৳<?php echo e(number_format($details->old_price)); ?></span>
                    <span class="pdp-save-badge">SAVE <?php echo e($discount); ?>%</span>
                    <?php endif; ?>
                </div>

                
                <div class="pdp-installment">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>Buy Now, Pay Later! — Easy EMI Available</span>
                </div>
                <div class="pdp-shipping-info">
                    <i class="fa-solid fa-truck"></i>
                    <span>Estimated Delivery: 7–10 Days · Ship From: Overseas</span>
                </div>

                
                <form action="<?php echo e(route('cart.store')); ?>" method="POST" id="pdpForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo e($details->id); ?>" />

                    
                    <?php if($productcolors->count() > 0): ?>
                    <div class="pdp-variant-section">
                        <div class="pdp-variant-label">Color: <span class="attibute-name" id="selectedColorName">Select a color</span></div>
                        <div class="pdp-color-swatches">
                            <?php $__currentLoopData = $productcolors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $procolor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pdp-color-swatch <?php if($loop->first): ?> active <?php endif; ?>"
                                   style="background-color: <?php echo e($procolor->color ? $procolor->color->color : '#ccc'); ?>;"
                                   title="<?php echo e($procolor->color ? $procolor->color->colorName : ''); ?>"
                                   data-price="<?php echo e($procolor->price); ?>" data-stock="<?php echo e($procolor->stock); ?>"
                                   data-color-id="<?php echo e($procolor->color_id); ?>">
                                <input type="radio" name="product_color"
                                       value="<?php echo e($procolor->color ? $procolor->color->colorName : ''); ?>"
                                       class="color-variant"
                                       data-price="<?php echo e($procolor->price); ?>"
                                       data-stock="<?php echo e($procolor->stock); ?>"
                                       data-color-id="<?php echo e($procolor->color_id); ?>"
                                       <?php if($loop->first): ?> checked <?php endif; ?> />
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($productcolors->where('price', '!=', null)->count() > 0): ?>
                        <small class="text-muted" style="font-size:11px;margin-top:4px;display:block;">* Price may vary by color</small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($productsizes->count() > 0): ?>
                    <div class="pdp-variant-section">
                        <div class="pdp-variant-label">Size: <span class="attibute-name" id="selectedSizeName">Select a size</span></div>
                        <div class="pdp-size-options">
                            <?php $__currentLoopData = $productsizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prosize): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pdp-size-btn <?php if($loop->first): ?> active <?php endif; ?>">
                                <input type="radio" name="product_size"
                                       value="<?php echo e($prosize->size ? $prosize->size->sizeName : ''); ?>"
                                       class="size-variant"
                                       data-price="<?php echo e($prosize->price); ?>"
                                       data-stock="<?php echo e($prosize->stock); ?>"
                                       data-size-id="<?php echo e($prosize->size_id); ?>"
                                       <?php if($loop->first): ?> checked <?php endif; ?> />
                                <?php echo e($prosize->size ? $prosize->size->sizeName : ''); ?>

                                <?php if($prosize->price): ?>
                                <span class="size-price">+৳<?php echo e($prosize->price); ?></span>
                                <?php endif; ?>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($details->brand): ?>
                    <div class="pdp-variant-label" style="margin-bottom:12px;">Brand: <strong><?php echo e($details->brand->name); ?></strong></div>
                    <?php endif; ?>
                    <?php if($details->pro_unit): ?>
                    <input type="hidden" name="pro_unit" value="<?php echo e($details->pro_unit); ?>" />
                    <?php endif; ?>

                    
                    <?php if($details->stock < 1): ?>
                    <div class="pdp-stock-out"><i class="fa-solid fa-circle-exclamation"></i> স্টক আউট</div>
                    <?php else: ?>
                    
                    <div class="pdp-qty-row">
                        <div class="pdp-qty">
                            <button type="button" class="qty-minus">−</button>
                            <input type="text" name="qty" value="1" readonly />
                            <button type="button" class="qty-plus">+</button>
                        </div>
                    </div>

                    <div class="pdp-actions">
                        <button type="submit" name="add_cart" value="1" class="pdp-add-cart">
                            <i class="fa-solid fa-bag-shopping"></i> ADD TO CART
                        </button>
                        <button type="submit" name="order_now" value="1" class="pdp-buy-now">
                            <i class="fa-solid fa-bolt"></i> BUY IT NOW
                        </button>
                    </div>
                    <?php endif; ?>

                    
                    <?php if(isset($features) && count($features) > 0): ?>
                    <div class="pdp-features">
                        <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="pdp-feature-item">
                            <i class="fa-solid fa-check-circle"></i> <?php echo e($feature); ?>

                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="pdp-features">
                        <span class="pdp-feature-item"><i class="fa-solid fa-check-circle"></i> Premium Quality</span>
                        <span class="pdp-feature-item"><i class="fa-solid fa-check-circle"></i> Adjustable Strap</span>
                        <span class="pdp-feature-item"><i class="fa-solid fa-check-circle"></i> With Zip</span>
                        <span class="pdp-feature-item"><i class="fa-solid fa-check-circle"></i> Interior Pocket</span>
                    </div>
                    <?php endif; ?>

                    
                    <div class="pdp-trust">
                        <span class="pdp-trust-item"><i class="fa-solid fa-shield-halved"></i> 100% Authentic</span>
                        <span class="pdp-trust-item"><i class="fa-solid fa-lock"></i> Secure Checkout</span>
                        <span class="pdp-trust-item"><i class="fa-solid fa-rotate-left"></i> 48h Easy Return</span>
                        <span class="pdp-trust-item"><i class="fa-solid fa-truck-fast"></i> Guaranteed Delivery</span>
                    </div>
                </form>

                
                <div class="pdp-contact-row">
                    <a href="tel:<?php echo e($contact->hotline); ?>" class="pdp-contact-btn pdp-call-btn">
                        <i class="fa-solid fa-phone"></i> Call <?php echo e($contact->hotline); ?>

                    </a>
                    <a href="https://wa.me/<?php echo e(str_replace(['+',' ','-'], '', $contact->whatsapp)); ?>?text=<?php echo e(urlencode('Hello, I am interested in: '.$details->name.' - '.url('/products/'.$details->slug))); ?>"
                       target="_blank" class="pdp-contact-btn pdp-whatsapp-btn">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp <?php echo e($contact->whatsapp); ?>

                    </a>
                </div>

                
                <?php if($shippingcharge->count() > 0): ?>
                <div class="pdp-shipping-table">
                    <table>
                        <tr><th colspan="2">কুরিয়ার ডেলিভারি খরচ</th></tr>
                        <?php $__currentLoopData = $shippingcharge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr><td><?php echo e($charge->name); ?></td><td style="text-align:right;">৳ <?php echo e($charge->amount); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                </div>
                <?php endif; ?>

                
                <?php if($details->note): ?>
                <div class="pdp-note"><strong>Note:</strong> <?php echo e($details->note); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


<section class="pdp-tabs" id="pdpTabs">
    <div class="container">
        <div class="pdp-tab-nav">
            <button class="pdp-tab-btn active" data-tab="description">Description</button>
            <button class="pdp-tab-btn" data-tab="orderpolicy">Order Policy</button>
            <button class="pdp-tab-btn" data-tab="reviews">Reviews (<?php echo e($totalReviews); ?>)</button>
            <?php if($details->pro_video): ?>
            <button class="pdp-tab-btn" data-tab="video">Video</button>
            <?php endif; ?>
        </div>
        <div class="pdp-tab-content">
            
            <div class="pdp-tab-pane active" id="tab-description">
                <h3>Product Description</h3>
                <p><?php echo $details->description; ?></p>
            </div>

            
            <div class="pdp-tab-pane" id="tab-orderpolicy">
                <h3>Order Policy</h3>
                <p><?php echo $generalsetting->order_policy; ?></p>
            </div>

            
            <div class="pdp-tab-pane" id="tab-reviews">
                <div class="pdp-review-summary">
                    <div class="pdp-review-score">
                        <div class="score"><?php echo e(number_format($avgRating, 1)); ?></div>
                        <div class="stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= round($avgRating)): ?>
                                <i class="fa-solid fa-star"></i>
                                <?php else: ?>
                                <i class="fa-regular fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="total"><?php echo e($totalReviews); ?> review(s)</div>
                    </div>
                    <div style="flex:1;text-align:right;">
                        <button class="pdp-write-review-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="fa-regular fa-pen-to-square"></i> Write a Review
                        </button>
                    </div>
                </div>

                <?php if($reviews->count() > 0): ?>
                <div class="pdp-review-list">
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="pdp-review-card">
                        <div>
                            <span class="reviewer"><?php echo e($review->name); ?></span>
                            <span class="review-date"><?php echo e($review->created_at->format('d M Y')); ?></span>
                        </div>
                        <div class="review-stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $review->ratting): ?>
                                <i class="fa-solid fa-star"></i>
                                <?php else: ?>
                                <i class="fa-regular fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="review-text"><?php echo e($review->review); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5" style="color:#aaa;">
                    <i class="fa-regular fa-message" style="font-size:36px;display:block;margin-bottom:12px;"></i>
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
                <?php endif; ?>
            </div>

            
            <?php if($details->pro_video): ?>
            <div class="pdp-tab-pane" id="tab-video">
                <div class="pdp-video">
                    <h3>Product Video</h3>
                    <iframe src="https://www.youtube.com/embed/<?php echo e($details->pro_video); ?>"
                            title="Product Video" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0;padding:16px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:16px;">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <?php if(Auth::guard('customer')->user()): ?>
                <form action="<?php echo e(route('customer.review')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo e($details->id); ?>" />
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Your Rating</label>
                        <div class="star-rating">
                            <?php for($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?php echo e($i); ?>" name="ratting" value="<?php echo e($i); ?>" <?php if($i === 5): ?> required <?php endif; ?> />
                            <label for="star<?php echo e($i); ?>" title="<?php echo e($i); ?> stars"><i class="fa-solid fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewMessage" class="form-label fw-semibold" style="font-size:13px;">Your Review</label>
                        <textarea class="form-control" id="reviewMessage" name="review" rows="4" required
                                  style="border:1px solid #e0e0e0;border-radius:6px;font-size:13px;resize:vertical;"></textarea>
                    </div>
                    <button type="submit" class="pdp-write-review-btn w-100 justify-content-center">Submit Review</button>
                </form>
                <?php else: ?>
                <div class="text-center py-4">
                    <p class="mb-3" style="color:#666;">Please login to write a review.</p>
                    <a href="<?php echo e(route('customer.login')); ?>" class="pdp-add-cart" style="display:inline-block;text-decoration:none;padding:10px 30px;">Login</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php if($relatedProducts->count() > 0): ?>
<section class="pdp-section" style="background:#fff;">
    <div class="container">
        <h2 class="pdp-section-title">You May Also Like</h2>
        <div class="pdp-section-divider"></div>
        <div class="row g-4">
            <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-3 col-lg-2">
                <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($pairProducts->count() > 0): ?>
<section class="pdp-section" style="background:#f8f8f6;">
    <div class="container">
        <h2 class="pdp-section-title">Pair It &amp; Shine!</h2>
        <div class="pdp-section-divider"></div>
        <div class="row g-4">
            <?php $__currentLoopData = $pairProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-3">
                <?php echo $__env->make('frontEnd.layouts.pages._product_card_folks', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<div class="modal fade" id="colorPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="border:none;padding:12px 16px;position:absolute;top:0;right:0;z-index:10;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="background-color:#C9A84C;border-radius:50%;padding:10px;box-shadow:0 2px 8px rgba(0,0,0,0.2);opacity:1;background-size:14px;background-position:center;border:2px solid #fff;"></button>
            </div>
            <div class="modal-body" style="padding:0;background:#fafafa;text-align:center;">
                <img id="colorPreviewImg" src="" alt="Color preview" style="max-width:100%;max-height:80vh;object-fit:contain;margin:0 auto;display:block;" />
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
$(document).ready(function() {
    // ═══════════════════════════════════════════
    //  SWIPER GALLERY
    // ═══════════════════════════════════════════

    var mainSwiper = new Swiper('#pdpMainSwiper', {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.pdp-swiper-pagination',
            clickable: true,
        },
        on: {
            slideChange: function() {
                var activeSlide = this.slides[this.activeIndex];
                var colorId = $(activeSlide).data('color-id');
                $('.pdp-thumb-item').removeClass('active');
                if (colorId) {
                    $('.pdp-thumb-item:visible[data-color-id="' + colorId + '"]').first().addClass('active');
                } else {
                    $('.pdp-thumb-item:visible').first().addClass('active');
                }
                // Re-bind zoom to new active slide
                bindZoomToSlide(activeSlide);
            },
            init: function() {
                // Bind zoom to first slide after Swiper initializes
                var that = this;
                setTimeout(function() {
                    var firstSlide = that.slides[that.activeIndex];
                    if (firstSlide) bindZoomToSlide(firstSlide);
                }, 100);
            }
        }
    });

    // ═══════════════════════════════════════════
    //  3D ZOOM on active slide
    // ═══════════════════════════════════════════

    var $zoomLens = $('#pdpZoomLens');
    var $zoomResult = $('#pdpZoomResult');
    var $swiperEl = $('#pdpMainSwiper');
    var zoomEnabled = $(window).width() > 991;
    var zoomActive = false;

    function bindZoomToSlide(slideEl) {
        // Remove old handlers
        var $slide = $(slideEl);
        $slide.off('mouseenter.zoom mouseleave.zoom mousemove.zoom');
        $zoomLens.removeClass('show');
        $zoomResult.removeClass('show');

        if (!zoomEnabled) return;

        $slide.on('mouseenter.zoom', function() {
            if (!zoomEnabled) return;
            zoomActive = true;
            $zoomLens.addClass('show');
            $zoomResult.addClass('show');
        });

        $slide.on('mouseleave.zoom', function() {
            zoomActive = false;
            $zoomLens.removeClass('show');
            $zoomResult.removeClass('show');
        });

        $slide.on('mousemove.zoom', function(e) {
            if (!zoomEnabled || !zoomActive) return;

            var $img = $slide.find('img');
            var imgSrc = $img.attr('src');
            if (!imgSrc) return;

            var swiperOffset = $swiperEl.offset();
            var swiperW = $swiperEl.width();
            var swiperH = $swiperEl.height();
            var lensW = $zoomLens.width();
            var lensH = $zoomLens.height();
            var resultW = $zoomResult.width();
            var resultH = $zoomResult.height();

            // Mouse position relative to swiper container
            var mx = e.clientX - swiperOffset.left;
            var my = e.clientY - swiperOffset.top;

            // Clamp lens center so it stays within swiper bounds
            var lx = Math.max(lensW / 2, Math.min(mx, swiperW - lensW / 2));
            var ly = Math.max(lensH / 2, Math.min(my, swiperH - lensH / 2));

            // Position lens centered on cursor
            $zoomLens.css({ left: lx + 'px', top: ly + 'px' });

            // Calculate result background position
            var ratioX = resultW / lensW;
            var ratioY = resultH / lensH;

            // Background offset = negative (lens position - half lens) * ratio
            var bgX = -(lx - lensW / 2) * ratioX;
            var bgY = -(ly - lensH / 2) * ratioY;

            $zoomResult.css({
                backgroundImage: 'url(' + imgSrc + ')',
                backgroundSize: (swiperW * ratioX) + 'px ' + (swiperH * ratioY) + 'px',
                backgroundPosition: bgX + 'px ' + bgY + 'px'
            });
        });
    }

    $(window).on('resize', function() {
        zoomEnabled = $(window).width() > 991;
        if (!zoomEnabled) {
            $zoomLens.removeClass('show').hide();
            $zoomResult.removeClass('show').hide();
        }
    });

    // ── Thumbnail click ──
    $('.pdp-thumb-item').on('click', function() {
        var idx = $(this).data('index');
        mainSwiper.slideTo(idx);
    });

    // ── Gallery color swatch click ──
    $('.pdp-gallery-swatch').on('click', function() {
        $('.pdp-gallery-swatch').removeClass('active');
        $(this).addClass('active');
        var colorId = $(this).data('color-id');

        // Sync product form color swatches
        $('.pdp-color-swatch').removeClass('active');
        $('.pdp-color-swatch[data-color-id="' + colorId + '"]').addClass('active')
            .find('input[type="radio"]').prop('checked', true).trigger('change');

        // Open modal with matching color image
        var $matchingSlide = $('.dimage_item[data-color-id="' + colorId + '"]').first();
        if ($matchingSlide.length) {
            var imgSrc = $matchingSlide.find('img').attr('src');
            if (imgSrc) {
                $('#colorPreviewImg').attr('src', imgSrc);
                $('#colorPreviewModal').modal('show');
            }
        }
    });

    // ── Product form color swatch click (sync to gallery) ──
    $('.pdp-color-swatch').on('click', function() {
        $('.pdp-color-swatch').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
        var name = $(this).attr('title') || 'Selected';
        $('#selectedColorName').text(name);
        var colorId = $(this).data('color-id');
        $('.pdp-gallery-swatch').removeClass('active');
        $('.pdp-gallery-swatch[data-color-id="' + colorId + '"]').addClass('active');

        // Find matching color-specific image and open modal
        var $matchingSlide = $('.dimage_item[data-color-id="' + colorId + '"]').first();
        if ($matchingSlide.length) {
            var imgSrc = $matchingSlide.find('img').attr('src');
            if (imgSrc) {
                $('#colorPreviewImg').attr('src', imgSrc);
                $('#colorPreviewModal').modal('show');
            }
        }
    });

    // ── Color-variant radio change ──
    $('.color-variant').on('change', function() {
        updateVariantPrice();
        var colorId = $(this).data('color-id');
        $('.pdp-gallery-swatch').removeClass('active');
        $('.pdp-gallery-swatch[data-color-id="' + colorId + '"]').addClass('active');
    });

    // ── Qty buttons ──
    $(".qty-minus").on("click", function() {
        var $input = $(this).siblings('input[name="qty"]');
        var val = parseInt($input.val()) || 1;
        if (val > 1) $input.val(val - 1);
    });
    $(".qty-plus").on("click", function() {
        var $input = $(this).siblings('input[name="qty"]');
        var val = parseInt($input.val()) || 1;
        $input.val(val + 1);
    });

    // ── Size button selection ──
    $('.pdp-size-btn').on('click', function() {
        $('.pdp-size-btn').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
        var name = $(this).clone().children().remove().end().text().trim();
        $('#selectedSizeName').text(name);
    });

    // ── Variant pricing ──
    var basePrice = <?php echo e($details->new_price); ?>;
    var baseOldPrice = <?php echo e($details->old_price ?? 0); ?>;

    function updateVariantPrice() {
        var colorPrice = $('input[name="product_color"]:checked').data('price');
        var sizePrice = $('input[name="product_size"]:checked').data('price');
        var newPrice = basePrice;
        if (colorPrice && parseFloat(colorPrice) > 0) newPrice = parseFloat(colorPrice);
        if (sizePrice && parseFloat(sizePrice) > 0) newPrice = parseFloat(sizePrice);
        $('#pdpNewPrice').text('৳' + Number(newPrice).toLocaleString('en-IN'));
    }

    $('.size-variant').on('change', function() { updateVariantPrice(); });

    // Set first color name
    var firstColor = $('.pdp-color-swatch.active').attr('title');
    if (firstColor) $('#selectedColorName').text(firstColor);

    // ── Tabs ──
    $('.pdp-tab-btn').on('click', function() {
        var tab = $(this).data('tab');
        $('.pdp-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.pdp-tab-pane').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });

    if (window.location.hash === '#reviews') {
        $('.pdp-tab-btn[data-tab="reviews"]').trigger('click');
        setTimeout(function() {
            $('#pdpTabs')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    }
});
</script>


<script>
window.dataLayer = window.dataLayer || [];
dataLayer.push({
    event: "view_item",
    ecommerce: {
        items: [{
            item_name: "<?php echo e($details->name); ?>",
            item_id: "<?php echo e($details->id); ?>",
            price: "<?php echo e($details->new_price); ?>",
            item_brand: "<?php echo e($details->brand ? $details->brand->name : ''); ?>",
            item_category: "<?php echo e($details->category ? $details->category->name : ''); ?>",
            item_variant: "<?php echo e($details->pro_unit); ?>",
            currency: "BDT",
            quantity: <?php echo e($details->stock ?? 0); ?>

        }],
        impression: [
            <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                item_name: "<?php echo e($value->name); ?>",
                item_id: "<?php echo e($value->id); ?>",
                price: "<?php echo e($value->new_price); ?>",
                item_brand: "<?php echo e($details->brand ? $details->brand->name : ''); ?>",
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
<?php $__env->stopPush(); ?>


<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/pages/details.blade.php ENDPATH**/ ?>