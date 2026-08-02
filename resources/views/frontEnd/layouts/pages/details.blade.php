@extends('frontEnd.layouts.master')
@section('title', $details->name)
@push('seo')
<meta name="app-url" content="{{ route('product', $details->slug) }}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{ $details->meta_description }}" />
<meta name="keywords" content="{{ $details->slug }}" />
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="{{ $details->name }}" />
<meta name="twitter:title" content="{{ $details->name }}" />
<meta name="twitter:description" content="{{ $details->meta_description }}" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="{{ route('product', $details->slug) }}" />
<meta name="twitter:image" content="{{ asset($details->image ? $details->image->image : '') }}" />
<meta property="og:title" content="{{ $details->name }}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{ route('product', $details->slug) }}" />
<meta property="og:image" content="{{ asset($details->image ? $details->image->image : '') }}" />
<meta property="og:description" content="{{ $details->meta_description }}" />
<meta property="og:site_name" content="{{ $details->name }}" />
@endpush

@push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/zoomsl.css') }}">
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
    display: none;
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

/* ── Alibaba Style Media Toggle Bar ── */
.pdp-media-toggle-wrapper {
    display: flex;
    justify-content: flex-end;
    margin-top: 12px;
}
.pdp-media-toggle-bar {
    display: inline-flex;
    background: #eef0f3;
    padding: 3px;
    border-radius: 20px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
}
.pdp-media-toggle-btn {
    border: none;
    background: transparent;
    padding: 6px 18px;
    border-radius: 18px;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}
.pdp-media-toggle-btn.active {
    background: #ffffff;
    color: #000000;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.pdp-media-toggle-btn:hover:not(.active) {
    color: #000000;
}

/* Video Thumbnail with Play Badge */
.pdp-thumb-video-item {
    position: relative;
    cursor: pointer;
}
.pdp-thumb-play-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.4);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 11px;
    transition: background 0.2s ease;
}
.pdp-thumb-video-item:hover .pdp-thumb-play-overlay {
    background: rgba(0,0,0,0.6);
}
.pdp-thumb-play-overlay i {
    background: rgba(0,0,0,0.7);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-left: 2px;
    color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}

.pdp-video-stage-wrapper {
    width: 100%;
    height: 100%;
    min-height: 400px;
    background: #000;
    border-radius: 8px;
    overflow: hidden;
}

@media (max-width: 991px) {
    .pdp-gallery, .pdp-info { flex: 0 0 100%; max-width: 100%; }
    .pdp-thumb-item { flex: 0 0 64px; width: 64px; height: 64px; }
    .pdp-main-swiper .swiper-button-prev,
    .pdp-main-swiper .swiper-button-next { opacity: 1; }
}
@media (max-width: 576px) {
    .pdp-gallery-badge { top: 10px; left: 10px; padding: 5px 12px; font-size: 11px; }
    .pdp-video-stage-wrapper { min-height: 280px; }
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
    background: #000; color: #fff;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.25s ease;
    text-transform: uppercase; letter-spacing: 1px;
    box-shadow: 0 3px 10px rgba(60,125,23,0.25);
}
.pdp-add-cart:hover {
    background: #000; color: #fff;
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
.pdp-call-btn { background: #000; color: #fff; }
.pdp-call-btn:hover { background: #000; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(60,125,23,0.25); }
.pdp-whatsapp-btn { background: #000; color: #fff; }
.pdp-whatsapp-btn:hover { background: #000; color: #fff; transform: translateY(-1px); }

/* ── Shipping Table ── */
.pdp-shipping-table { margin-bottom: 20px; }
.pdp-shipping-table table {
    width: 100%; border-collapse: collapse; font-size: 12px; border: 1px solid #eee; border-radius: 6px; overflow: hidden;
}
.pdp-shipping-table th {
    background: #000; color: #fff; padding: 8px 12px; text-align: center; font-weight: 600;
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
@endpush

@section('content')

{{-- ───── Page Header ───── --}}
<section class="pdp-header">
    <div class="container">
        <div class="pdp-header-inner">
            <div class="pdp-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                @if($details->category)
                <span>›</span>
                <a href="{{ route('category', $details->category->slug) }}">{{ $details->category->name }}</a>
                @endif
                @if($details->subcategory)
                <span>›</span>
                <a href="{{ url('subcategory/'.$details->subcategory->slug) }}">{{ $details->subcategory->subcategoryName }}</a>
                @endif
                <span>›</span>
                <strong>{{ Str::limit($details->name, 40) }}</strong>
            </div>
        </div>
    </div>
</section>

{{-- ───── Main Product ───── --}}
<section class="pdp-main">
    <div class="container">
        <div class="pdp-layout">
            {{-- Gallery --}}
            <div class="pdp-gallery">
                {{-- Main Image Stage --}}
                <div class="pdp-gallery-stage">
                    @php $discount = $details->old_price ? round((($details->old_price - $details->new_price) / $details->old_price) * 100) : 0; @endphp
                    @if($discount > 0)
                    <div class="pdp-gallery-badge">-{{ $discount }}%</div>
                    @endif

                    @php
                        $sliderImages = collect();

                        if ($productcolors->count() > 0) {
                            foreach ($productcolors as $key => $procolor) {
                                // Find image matching this color_id or fallback to general image at index or main image
                                $colorImg = $details->images->where('color_id', $procolor->color_id)->first();
                                if (!$colorImg) {
                                    $colorImg = $details->images->get($key) ?? $details->image;
                                }

                                if ($colorImg) {
                                    $sliderImages->push((object)[
                                        'image' => $colorImg->image,
                                        'color_id' => $procolor->color_id,
                                        'color_name' => $procolor->color ? $procolor->color->colorName : '',
                                    ]);
                                }
                            }
                        } else {
                            $images = $details->images->isEmpty() ? collect([$details->image]) : $details->images;
                            foreach ($images as $img) {
                                if ($img) {
                                    $sliderImages->push((object)[
                                        'image' => $img->image,
                                        'color_id' => $img->color_id ?? '',
                                        'color_name' => '',
                                    ]);
                                }
                            }
                        }
                    @endphp
                    <div class="swiper pdp-main-swiper" id="pdpMainSwiper">
                        <div class="swiper-wrapper" id="pdpSwiperWrapper">
                            @forelse($sliderImages as $key => $sImg)
                            <div class="swiper-slide dimage_item" data-color-id="{{ $sImg->color_id }}" data-index="{{ $key }}">
                                <img src="{{ asset($sImg->image) }}" alt="{{ $details->name }}" />
                            </div>
                            @empty
                            <div class="swiper-slide dimage_item" data-color-id="" data-index="0">
                                <img src="{{ asset('frontEnd/img/default-product.jpg') }}" alt="{{ $details->name }}" />
                            </div>
                            @endforelse
                        </div>
                        {{-- Zoom lens & result --}}
                        <div class="pdp-zoom-lens" id="pdpZoomLens"></div>
                        <div class="pdp-zoom-result" id="pdpZoomResult">
                            <div class="pdp-zoom-label">🔍 Zoom</div>
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-pagination pdp-swiper-pagination"></div>
                    </div>

                    {{-- Alibaba Style Video Stage --}}
                    @if($details->pro_video)
                    <div class="pdp-video-stage-wrapper" id="pdpVideoStageWrapper" style="display: none;">
                        @if(Str::startsWith($details->pro_video, 'http') || !str_contains($details->pro_video, '.'))
                            <iframe id="pdpIframeVideo" src="https://www.youtube.com/embed/{{ $details->pro_video }}?enablejsapi=1"
                                    title="Product Video" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen style="width: 100%; height: 100%; min-height: 400px; border-radius: 8px; border: none;"></iframe>
                        @else
                            <video id="pdpHtml5Video" controls style="width: 100%; height: 100%; max-height: 480px; border-radius: 8px; background: #000; display: block; margin: 0 auto;" preload="metadata">
                                <source src="{{ asset($details->pro_video) }}">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Alibaba Style Media Toggle Pills --}}
                @if($details->pro_video)
                <div class="pdp-media-toggle-wrapper">
                    <div class="pdp-media-toggle-bar">
                        <button type="button" class="pdp-media-toggle-btn active" id="pdpTogglePhotosBtn">
                            <i class="fa-regular fa-image me-1"></i> Photos
                        </button>
                        <button type="button" class="pdp-media-toggle-btn" id="pdpToggleVideoBtn">
                            <i class="fa-solid fa-circle-play me-1"></i> Video
                        </button>
                    </div>
                </div>
                @endif

                {{-- Thumbnails --}}
                <div class="pdp-thumb-strip">
                    <div class="pdp-thumb-scroll" id="pdpThumbScroll">
                        @forelse($sliderImages as $key => $sImg)
                        <div class="pdp-thumb-item {{ $key === 0 ? 'active' : '' }}"
                             data-index="{{ $key }}"
                             data-src="{{ asset($sImg->image) }}"
                             data-color-id="{{ $sImg->color_id }}">
                            <img src="{{ asset($sImg->image) }}" alt="" />
                        </div>
                        @empty
                        <div class="pdp-thumb-item active" data-index="0" data-src="{{ asset('frontEnd/img/default-product.jpg') }}" data-color-id="">
                            <img src="{{ asset('frontEnd/img/default-product.jpg') }}" alt="" />
                        </div>
                        @endforelse

                        @if($details->pro_video)
                        <div class="pdp-thumb-item pdp-thumb-video-item" id="pdpThumbVideoItem" title="Watch Product Video">
                            @php
                                $thumbImg = $sliderImages->first() ? asset($sliderImages->first()->image) : asset('frontEnd/img/default-product.jpg');
                            @endphp
                            <img src="{{ $thumbImg }}" alt="Video" />
                            <div class="pdp-thumb-play-overlay">
                                <i class="fa-solid fa-play"></i>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Color Swatches --}}
                @if($productcolors->count() > 0)
                <div class="pdp-gallery-colors">
                    <span class="pdp-gallery-colors-label">Colors:</span>
                    <div class="pdp-gallery-swatches" id="pdpGallerySwatches">
                        @foreach($productcolors as $procolor)
                        @php $hex = $procolor->color ? $procolor->color->color : '#ccc'; @endphp
                        <button class="pdp-gallery-swatch @if($loop->first) active @endif"
                                style="background-color: {{ $hex }};"
                                title="{{ $procolor->color ? $procolor->color->colorName : '' }}"
                                data-color-id="{{ $procolor->color_id }}">
                            <span class="swatch-tooltip">{{ $procolor->color ? $procolor->color->colorName : '' }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="pdp-info">
                <h1 class="pdp-name">{{ $details->name }}</h1>
                <div class="pdp-sku">SKU: {{ $details->product_code }}</div>

                {{-- Price --}}
                <div class="pdp-price-row">
                    <span class="pdp-current-price" id="pdpNewPrice">৳{{ number_format($details->new_price) }}</span>
                    @if($details->old_price)
                    <span class="pdp-old-price" id="pdpOldPrice">৳{{ number_format($details->old_price) }}</span>
                    <span class="pdp-save-badge">SAVE {{ $discount }}%</span>
                    @endif
                </div>
                <div class="pdp-shipping-info">
                    <i class="fa-solid fa-truck"></i>
                    <span>Estimated Delivery: 2-3 Days · Ship From: {{ $details->ship_from ?? 'Dhaka' }}</span>
                </div>

                {{-- Variants --}}
                <form action="{{ route('cart.store') }}" method="POST" id="pdpForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $details->id }}" />

                    {{-- Colors --}}
                    @if($productcolors->count() > 0)
                    <div class="pdp-variant-section">
                        <div class="pdp-variant-label">Color: <span class="attibute-name" id="selectedColorName">Select a color</span></div>
                        <div class="pdp-color-swatches">
                            @foreach($productcolors as $procolor)
                            <label class="pdp-color-swatch @if($loop->first) active @endif"
                                   style="background-color: {{ $procolor->color ? $procolor->color->color : '#ccc' }};"
                                   title="{{ $procolor->color ? $procolor->color->colorName : '' }}"
                                   data-price="{{ $procolor->price }}" data-stock="{{ $procolor->stock }}"
                                   data-color-id="{{ $procolor->color_id }}">
                                <input type="radio" name="product_color"
                                       value="{{ $procolor->color ? $procolor->color->colorName : '' }}"
                                       class="color-variant"
                                       data-price="{{ $procolor->price }}"
                                       data-stock="{{ $procolor->stock }}"
                                       data-color-id="{{ $procolor->color_id }}"
                                       @if($loop->first) checked @endif />
                            </label>
                            @endforeach
                        </div>
                        @if($productcolors->where('price', '!=', null)->count() > 0)
                        <small class="text-muted" style="font-size:11px;margin-top:4px;display:block;">* Price may vary by color</small>
                        @endif
                    </div>
                    @endif

                    {{-- Sizes --}}
                    @if($productsizes->count() > 0)
                    <div class="pdp-variant-section">
                        <div class="pdp-variant-label">Size: <span class="attibute-name" id="selectedSizeName">@if($productsizes->count() == 1){{ $productsizes->first()->size ? $productsizes->first()->size->sizeName : '' }}@else Select a size @endif</span></div>
                        <div class="pdp-size-options">
                            @foreach($productsizes as $prosize)
                            @php
                                $isSingle = ($productsizes->count() == 1);
                                $sizeName = $prosize->size ? $prosize->size->sizeName : '';
                            @endphp
                            <label class="pdp-size-btn @if($isSingle) active @endif">
                                <input type="radio" name="product_size"
                                       value="{{ $sizeName }}"
                                       class="size-variant"
                                       data-price="{{ $prosize->price }}"
                                       data-stock="{{ $prosize->stock }}"
                                       data-size-id="{{ $prosize->size_id }}"
                                       @if($isSingle) checked @endif />
                                {{ $sizeName }}
                                @if($prosize->price)
                                <span class="size-price">+৳{{ $prosize->price }}</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Brand / Unit --}}
                    @if($details->brand)
                    <div class="pdp-variant-label" style="margin-bottom:12px;">Brand: <strong>{{ $details->brand->name }}</strong></div>
                    @endif
                    @if($details->pro_unit)
                    <input type="hidden" name="pro_unit" value="{{ $details->pro_unit }}" />
                    @endif

                    {{-- Stock check --}}
                    @if($details->stock < 1)
                    <div class="pdp-stock-out"><i class="fa-solid fa-circle-exclamation"></i> স্টক আউট</div>
                    @else
                    {{-- Qty + Buttons --}}
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
                    @endif

                    {{-- Features --}}
                    @if(isset($features) && count($features) > 0)
                    <div class="pdp-features">
                        @foreach($features as $feature)
                        <span class="pdp-feature-item">
                            <i class="fa-solid fa-check-circle"></i> {{ $feature }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    @endif

                    {{-- Trust Badges --}}
                    <div class="pdp-trust">
                        <span class="pdp-trust-item"><i class="fa-solid fa-shield-halved"></i> 100% Authentic</span>
                        <span class="pdp-trust-item"><i class="fa-solid fa-lock"></i> Secure Checkout</span>
                        <span class="pdp-trust-item"><i class="fa-solid fa-rotate-left"></i> 48h Easy Return</span>
                        <span class="pdp-trust-item"><i class="fa-solid fa-truck-fast"></i> Guaranteed Delivery</span>
                    </div>
                </form>

                {{-- Contact Buttons --}}
                <div class="pdp-contact-row">
                    <a href="tel:{{ $contact->hotline }}" class="pdp-contact-btn pdp-call-btn">
                        <i class="fa-solid fa-phone"></i> Call {{ $contact->hotline }}
                    </a>
                    <a href="https://wa.me/{{ str_replace(['+',' ','-'], '', $contact->whatsapp) }}?text={{ urlencode('Hello, I am interested in: '.$details->name.' - '.url('/products/'.$details->slug)) }}"
                       target="_blank" class="pdp-contact-btn pdp-whatsapp-btn">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp {{ $contact->whatsapp }}
                    </a>
                </div>

                {{-- Shipping Table --}}
                @if($shippingcharge->count() > 0)
                <div class="pdp-shipping-table">
                    <table>
                        <tr><th colspan="2">কুরিয়ার ডেলিভারি খরচ</th></tr>
                        @foreach($shippingcharge as $charge)
                        <tr><td>{{ $charge->name }}</td><td style="text-align:right;">৳ {{ $charge->amount }}</td></tr>
                        @endforeach
                    </table>
                </div>
                @endif

                {{-- Note --}}
                @if($details->note)
                <div class="pdp-note"><strong>Note:</strong> {{ $details->note }}</div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ───── Tabs (Description / Order Policy / Reviews) ───── --}}
<section class="pdp-tabs" id="pdpTabs">
    <div class="container">
        <div class="pdp-tab-nav">
            <button class="pdp-tab-btn active" data-tab="description">Description</button>
            <button class="pdp-tab-btn" data-tab="orderpolicy">Order Policy</button>
            <button class="pdp-tab-btn" data-tab="reviews">Reviews ({{ $totalReviews }})</button>
            @if($details->pro_video)
            <button class="pdp-tab-btn" data-tab="video">Video</button>
            @endif
        </div>
        <div class="pdp-tab-content">
            {{-- Description --}}
            <div class="pdp-tab-pane active" id="tab-description">
                <h3>Product Description</h3>
                <p>{!! $details->description !!}</p>
            </div>

            {{-- Order Policy --}}
            <div class="pdp-tab-pane" id="tab-orderpolicy">
                <h3>Order Policy</h3>
                <p>{!! $generalsetting->order_policy !!}</p>
            </div>

            {{-- Reviews --}}
            <div class="pdp-tab-pane" id="tab-reviews">
                <div class="pdp-review-summary">
                    <div class="pdp-review-score">
                        <div class="score">{{ number_format($avgRating, 1) }}</div>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                <i class="fa-solid fa-star"></i>
                                @else
                                <i class="fa-regular fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="total">{{ $totalReviews }} review(s)</div>
                    </div>
                    <div style="flex:1;text-align:right;">
                        <button class="pdp-write-review-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="fa-regular fa-pen-to-square"></i> Write a Review
                        </button>
                    </div>
                </div>

                @if($reviews->count() > 0)
                <div class="pdp-review-list">
                    @foreach($reviews as $review)
                    <div class="pdp-review-card">
                        <div>
                            <span class="reviewer">{{ $review->name }}</span>
                            <span class="review-date">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->ratting)
                                <i class="fa-solid fa-star"></i>
                                @else
                                <i class="fa-regular fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="review-text">{{ $review->review }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5" style="color:#aaa;">
                    <i class="fa-regular fa-message" style="font-size:36px;display:block;margin-bottom:12px;"></i>
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
                @endif
            </div>

            {{-- Video --}}
            @if($details->pro_video)
            <div class="pdp-tab-pane" id="tab-video">
                <div class="pdp-video">
                    <h3>Product Video</h3>
                    <iframe src="https://www.youtube.com/embed/{{ $details->pro_video }}"
                            title="Product Video" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ───── Review Modal ───── --}}
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0;padding:16px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:16px;">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                @if(Auth::guard('customer')->user())
                <form action="{{ route('customer.review') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $details->id }}" />
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Your Rating</label>
                        <div class="star-rating">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="ratting" value="{{ $i }}" @if($i === 5) required @endif />
                            <label for="star{{ $i }}" title="{{ $i }} stars"><i class="fa-solid fa-star"></i></label>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewMessage" class="form-label fw-semibold" style="font-size:13px;">Your Review</label>
                        <textarea class="form-control" id="reviewMessage" name="review" rows="4" required
                                  style="border:1px solid #e0e0e0;border-radius:6px;font-size:13px;resize:vertical;"></textarea>
                    </div>
                    <button type="submit" class="pdp-write-review-btn w-100 justify-content-center">Submit Review</button>
                </form>
                @else
                <div class="text-center py-4">
                    <p class="mb-3" style="color:#666;">Please login to write a review.</p>
                    <a href="{{ route('customer.login') }}" class="pdp-add-cart" style="display:inline-block;text-decoration:none;padding:10px 30px;">Login</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ───── Related Products ───── --}}
@if($relatedProducts->count() > 0)
<section class="pdp-section" style="background:#fff;">
    <div class="container">
        <h2 class="pdp-section-title">You May Also Like</h2>
        <div class="pdp-section-divider"></div>
        <div class="row g-4">
            @foreach($relatedProducts as $product)
            <div class="col-6 col-md-3 col-lg-2">
                @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ───── Pair It & Shine! ───── --}}
@if($pairProducts->count() > 0)
<section class="pdp-section" style="background:#f8f8f6;">
    <div class="container">
        <h2 class="pdp-section-title">Pair It &amp; Shine!</h2>
        <div class="pdp-section-divider"></div>
        <div class="row g-4">
            @foreach($pairProducts as $product)
            <div class="col-6 col-md-3">
                @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ───── Color Preview Modal ───── --}}
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

@endsection

@push('script')
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

                    // Auto-select corresponding color swatch on product details page
                    $('.pdp-gallery-swatch').removeClass('active');
                    $('.pdp-gallery-swatch[data-color-id="' + colorId + '"]').addClass('active');

                    var $colorSwatch = $('.pdp-color-swatch[data-color-id="' + colorId + '"]');
                    if ($colorSwatch.length) {
                        $('.pdp-color-swatch').removeClass('active');
                        $colorSwatch.addClass('active');
                        $colorSwatch.find('input[type="radio"]').prop('checked', true);
                        var colorName = $colorSwatch.attr('title') || '';
                        if (colorName) $('#selectedColorName').text(colorName);
                        if (typeof updateVariantPrice === 'function') updateVariantPrice();
                    }
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

    // ═══════════════════════════════════════════
    //  ALIBABA STYLE VIDEO & PHOTOS TOGGLE
    // ═══════════════════════════════════════════

    function showPhotosStage() {
        $('#pdpVideoStageWrapper').hide();
        $('#pdpMainSwiper').show();
        $('#pdpTogglePhotosBtn').addClass('active');
        $('#pdpToggleVideoBtn').removeClass('active');
        $('#pdpThumbVideoItem').removeClass('active');

        var v = document.getElementById('pdpHtml5Video');
        if (v && typeof v.pause === 'function') {
            v.pause();
        }
    }

    function showVideoStage() {
        $('#pdpMainSwiper').hide();
        if (typeof $zoomLens !== 'undefined') $zoomLens.removeClass('show');
        if (typeof $zoomResult !== 'undefined') $zoomResult.removeClass('show');
        $('#pdpVideoStageWrapper').show();

        $('#pdpToggleVideoBtn').addClass('active');
        $('#pdpTogglePhotosBtn').removeClass('active');

        $('.pdp-thumb-item').removeClass('active');
        $('#pdpThumbVideoItem').addClass('active');

        var v = document.getElementById('pdpHtml5Video');
        if (v && typeof v.play === 'function') {
            v.play().catch(function(e) { console.log('Autoplay prevented:', e); });
        }
    }

    $('#pdpTogglePhotosBtn').on('click', function() {
        showPhotosStage();
        $('.pdp-thumb-item:not(#pdpThumbVideoItem)').first().addClass('active');
    });

    $('#pdpToggleVideoBtn, #pdpThumbVideoItem').on('click', function() {
        showVideoStage();
    });

    // ── Thumbnail click ──
    $('.pdp-thumb-item:not(#pdpThumbVideoItem)').on('click', function() {
        showPhotosStage();
        var idx = $(this).data('index');
        if (typeof mainSwiper !== 'undefined' && mainSwiper.slideTo && typeof idx !== 'undefined') {
            mainSwiper.slideTo(idx);
        }
    });

    $('.pdp-gallery-swatch, .pdp-color-swatch').on('click', function() {
        showPhotosStage();
    });

    function selectColorById(colorId) {
        if (!colorId) return;

        // Slide main swiper to matching color image slide
        var $matchingSlide = $('.dimage_item[data-color-id="' + colorId + '"]').first();
        if ($matchingSlide.length) {
            var slideIdx = $matchingSlide.index();
            if (typeof mainSwiper !== 'undefined' && mainSwiper.slideTo) {
                mainSwiper.slideTo(slideIdx);
            }
        }

        // Sync active states
        $('.pdp-gallery-swatch').removeClass('active');
        $('.pdp-gallery-swatch[data-color-id="' + colorId + '"]').addClass('active');

        var $colorSwatch = $('.pdp-color-swatch[data-color-id="' + colorId + '"]');
        if ($colorSwatch.length) {
            $('.pdp-color-swatch').removeClass('active');
            $colorSwatch.addClass('active');
            $colorSwatch.find('input[type="radio"]').prop('checked', true);
            var name = $colorSwatch.attr('title') || '';
            if (name) $('#selectedColorName').text(name);
            if (typeof updateVariantPrice === 'function') updateVariantPrice();
        }
    }

    $('.pdp-gallery-swatch').on('click', function(e) {
        e.preventDefault();
        var colorId = $(this).data('color-id');
        selectColorById(colorId);
    });

    $('.pdp-color-swatch').on('click', function(e) {
        var colorId = $(this).data('color-id');
        selectColorById(colorId);
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

    // ── Size Validation on Form Submit ──
    var totalSizesAvailable = {{ $productsizes->count() }};
    $('#pdpForm').on('submit', function(e) {
        if (totalSizesAvailable > 0) {
            var selectedSizeVal = $('input[name="product_size"]:checked').val();
            if (!selectedSizeVal) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please select a size before adding to cart.');
                } else {
                    alert('Please select a size before adding to cart.');
                }
                var $sizeSec = $('.pdp-variant-section').first();
                if ($sizeSec.length) {
                    $('html, body').animate({
                        scrollTop: $sizeSec.offset().top - 100
                    }, 300);
                }
                return false;
            }
        }
    });

    // ── Variant pricing ──
    var basePrice = {{ $details->new_price }};
    var baseOldPrice = {{ $details->old_price ?? 0 }};

    function updateVariantPrice() {
        var colorPrice = $('input[name="product_color"]:checked').data('price');
        var sizePrice = $('input[name="product_size"]:checked').data('price');
        var newPrice = basePrice;
        if (colorPrice && parseFloat(colorPrice) > 0) newPrice = parseFloat(colorPrice);
        if (sizePrice && parseFloat(sizePrice) > 0) newPrice = parseFloat(sizePrice);
        $('#pdpNewPrice').text('৳' + Number(newPrice).toLocaleString('en-IN'));
    }

    $('.size-variant').on('change', function() { updateVariantPrice(); });

    if (totalSizesAvailable === 1) {
        updateVariantPrice();
    }

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

{{-- Data Layer --}}
<script>
window.dataLayer = window.dataLayer || [];
dataLayer.push({
    event: "view_item",
    ecommerce: {
        items: [{
            item_name: "{{ $details->name }}",
            item_id: "{{ $details->id }}",
            price: "{{ $details->new_price }}",
            item_brand: "{{ $details->brand ? $details->brand->name : '' }}",
            item_category: "{{ $details->category ? $details->category->name : '' }}",
            item_variant: "{{ $details->pro_unit }}",
            currency: "BDT",
            quantity: {{ $details->stock ?? 0 }}
        }],
        impression: [
            @foreach($relatedProducts as $value)
            {
                item_name: "{{ $value->name }}",
                item_id: "{{ $value->id }}",
                price: "{{ $value->new_price }}",
                item_brand: "{{ $details->brand ? $details->brand->name : '' }}",
                item_category: "{{ $value->category ? $value->category->name : '' }}",
                item_variant: "{{ $value->pro_unit }}",
                currency: "BDT",
                quantity: {{ $value->stock ?? 0 }}
            },
            @endforeach
        ]
    }
});
</script>
@endpush

