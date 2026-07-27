@extends('frontEnd.layouts.master')
@section('title', 'স্কুল ব্যাগ ফেস্ট 2026 - প্রিমিয়াম কোয়ালিটি স্কুল ব্যাগ সংকলন')
@push('css')
<style>
/* ───── Design Tokens & Root Palette ───── */
:root {
    --sb-primary: #059669;
    --sb-primary-dark: #047857;
    --sb-accent: #f59e0b;
    --sb-dark-bg: #0f172a;
    --sb-card-bg: #ffffff;
    --sb-text-main: #1e293b;
    --sb-text-muted: #64748b;
    --sb-border: #e2e8f0;
}

/* ───── Hero Header Section ───── */
.sb-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #064e3b 100%);
    padding: 50px 0 45px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
}
.sb-hero::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -10%;
    width: 450px;
    height: 450px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, transparent 70%);
}
.sb-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(245, 158, 11, 0.2);
    border: 1px solid var(--sb-accent);
    color: var(--sb-accent);
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
}
.sb-hero h1 {
    font-size: 34px;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 14px;
    color: #ffffff;
}
.sb-hero p {
    font-size: 15px;
    color: #cbd5e1;
    max-width: 650px;
    line-height: 1.6;
    margin-bottom: 24px;
}

/* Feature Counters */
.sb-hero-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}
.sb-stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    padding: 10px 18px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.sb-stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: var(--sb-primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.sb-stat-text span {
    display: block;
    font-size: 11px;
    color: #94a3b8;
}
.sb-stat-text strong {
    font-size: 13px;
    color: #ffffff;
}

/* ───── Persona Filter Tabs ───── */
.sb-persona-section {
    background: #f8fafc;
    padding: 24px 0 16px;
    border-bottom: 1px solid var(--sb-border);
    position: sticky;
    top: 60px;
    z-index: 99;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.sb-persona-nav {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 6px;
    scrollbar-width: thin;
}
.sb-persona-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #ffffff;
    border: 1px solid var(--sb-border);
    border-radius: 50px;
    color: var(--sb-text-main);
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.25s ease;
}
.sb-persona-tab:hover {
    border-color: var(--sb-primary);
    color: var(--sb-primary);
    transform: translateY(-1px);
}
.sb-persona-tab.active {
    background: var(--sb-primary);
    color: #ffffff;
    border-color: var(--sb-primary);
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
}

/* ───── Spotlight Best Sellers ───── */
.sb-spotlight-section {
    padding: 40px 0 20px;
    background: #ffffff;
}
.sb-section-header {
    text-align: center;
    margin-bottom: 30px;
}
.sb-section-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--sb-text-main);
    position: relative;
    display: inline-block;
}
.sb-section-title::after {
    content: '';
    display: block;
    width: 50%;
    height: 3px;
    background: var(--sb-primary);
    margin: 8px auto 0;
    border-radius: 2px;
}
.sb-section-desc {
    font-size: 14px;
    color: var(--sb-text-muted);
    margin-top: 6px;
}

/* ───── Main Product Catalog Section ───── */
.sb-catalog-section {
    padding: 30px 0 60px;
    background: #f8fafc;
    min-height: 500px;
}

/* Toolbar Controls */
.sb-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 12px;
    border: 1px solid var(--sb-border);
    margin-bottom: 24px;
}
.sb-search-box {
    position: relative;
    min-width: 280px;
    flex-grow: 1;
    max-width: 400px;
}
.sb-search-box input {
    width: 100%;
    padding: 9px 16px 9px 40px;
    border: 1px solid var(--sb-border);
    border-radius: 50px;
    font-size: 13.5px;
    background: #f8fafc;
    transition: border-color 0.2s;
}
.sb-search-box input:focus {
    outline: none;
    border-color: var(--sb-primary);
    background: #ffffff;
}
.sb-search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--sb-text-muted);
}

.sb-sort-box {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-sort-box label {
    font-size: 13px;
    color: var(--sb-text-muted);
    white-space: nowrap;
}
.sb-sort-select {
    padding: 8px 30px 8px 14px;
    border: 1px solid var(--sb-border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--sb-text-main);
    background: #ffffff;
    cursor: pointer;
}

/* ───── Product Card Styling ───── */
.sb-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid var(--sb-border);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.sb-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    border-color: #cbd5e1;
}
.sb-card-img-wrap {
    position: relative;
    width: 100%;
    padding-top: 100%; /* 1:1 Aspect Ratio */
    background: #f1f5f9;
    overflow: hidden;
}
.sb-card-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.sb-card:hover .sb-card-img {
    transform: scale(1.06);
}

.sb-badge-discount {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    z-index: 2;
}
.sb-badge-new {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--sb-primary);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    z-index: 2;
}

.sb-card-actions {
    position: absolute;
    bottom: -50px;
    left: 0;
    right: 0;
    padding: 10px;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(4px);
    transition: bottom 0.3s ease;
    display: flex;
    justify-content: center;
}
.sb-card:hover .sb-card-actions {
    bottom: 0;
}
.sb-quick-order-btn {
    width: 100%;
    background: var(--sb-primary);
    color: #ffffff;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.sb-quick-order-btn:hover {
    background: var(--sb-primary-dark);
}

.sb-card-body {
    padding: 14px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.sb-card-title-link {
    text-decoration: none;
    color: inherit;
}
.sb-card-title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--sb-text-main);
    line-height: 1.4;
    margin-bottom: 6px;
    min-height: 38px;
}
.sb-card-rating {
    font-size: 11px;
    margin-bottom: 8px;
}
.sb-review-count {
    color: var(--sb-text-muted);
    font-size: 11px;
    margin-left: 4px;
}
.sb-card-price-wrap {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 12px;
}
.sb-card-price {
    font-size: 16px;
    font-weight: 800;
    color: var(--sb-primary);
}
.sb-card-old-price {
    font-size: 12.5px;
    color: var(--sb-text-muted);
}
.sb-btn-details {
    margin-top: auto;
    display: block;
    text-align: center;
    padding: 7px 12px;
    background: #f1f5f9;
    color: var(--sb-text-main);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.sb-btn-details:hover {
    background: var(--sb-primary);
    color: #ffffff;
}

/* ───── Trust Badges Bar ───── */
.sb-trust-section {
    background: #ffffff;
    padding: 45px 0;
    border-top: 1px solid var(--sb-border);
}
.sb-trust-card {
    text-align: center;
    padding: 20px 15px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid var(--sb-border);
    height: 100%;
}
.sb-trust-icon {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: rgba(5, 150, 105, 0.1);
    color: var(--sb-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
}
.sb-trust-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--sb-text-main);
    margin-bottom: 6px;
}
.sb-trust-desc {
    font-size: 12.5px;
    color: var(--sb-text-muted);
    line-height: 1.5;
    margin: 0;
}

/* ───── Quick Order Modal ───── */
.modal-content.sb-modal {
    border-radius: 16px;
    border: none;
    overflow: hidden;
}
.sb-modal-header {
    background: var(--sb-primary);
    color: #ffffff;
    padding: 16px 20px;
}
.sb-modal-header h5 {
    color: #ffffff;
    font-weight: 700;
    margin: 0;
}

/* Mobile Sticky Bar */
.sb-mobile-sticky {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    padding: 10px 16px;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.1);
    z-index: 999;
    display: none;
}
@media (max-width: 768px) {
    .sb-mobile-sticky {
        display: flex;
        gap: 10px;
    }
    .sb-hero h1 {
        font-size: 24px;
    }
    .sb-hero {
        padding: 35px 0 30px;
    }
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="sb-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="sb-hero-badge">
                    <i class="fa fa-graduation-cap"></i> ব্যাক টু স্কুল ফেস্ট 2026
                </span>
                <h1>প্রিমিয়াম কোয়ালিটি স্কুল ব্যাগ সংকলন</h1>
                <p>
                    বাচ্চাদের মেরুদণ্ড সুরক্ষা, ওয়াটারপ্রুফ টেকসই ফেব্রিক এবং মাল্টি-লেয়ার স্টোরেজ স্পেস! সব বয়সের শিক্ষার্থীদের জন্য সেরা দামে সবচেয়ে আকর্ষণীয় স্কুল ব্যাগের সংগ্রহ।
                </p>

                <!-- Hero Stats Badges -->
                <div class="sb-hero-stats">
                    <div class="sb-stat-item">
                        <div class="sb-stat-icon"><i class="fa fa-shield-alt"></i></div>
                        <div class="sb-stat-text">
                            <span>কোয়ালিটি গ্যারান্টি</span>
                            <strong>১ বছরের ওয়ারেন্টি</strong>
                        </div>
                    </div>
                    <div class="sb-stat-item">
                        <div class="sb-stat-icon"><i class="fa fa-tint"></i></div>
                        <div class="sb-stat-text">
                            <span>ওয়াটারপ্রুফ মেটেরিয়াল</span>
                            <strong>১০০% বৃষ্টির পানি রোধী</strong>
                        </div>
                    </div>
                    <div class="sb-stat-item">
                        <div class="sb-stat-icon"><i class="fa fa-truck"></i></div>
                        <div class="sb-stat-text">
                            <span>দ্রুত হোম ডেলিভারি</span>
                            <strong>সারাদেশে ক্যাশ অন ডেলিভারি</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <img src="{{ asset('public/uploads/category/cat-school.jpg') }}" 
                     alt="School Bags" 
                     class="img-fluid rounded-4 shadow-lg" 
                     style="max-height: 320px; border: 4px solid rgba(255,255,255,0.2);"
                     onerror="this.src='{{ asset('frontEnd/img/default-product.jpg') }}'">
            </div>
        </div>
    </div>
</section>

<!-- Persona / Category Filter Navigation -->
<section class="sb-persona-section">
    <div class="container">
        <div class="sb-persona-nav">
            <button type="button" class="sb-persona-tab active" data-persona="all">
                <i class="fa fa-th-large"></i> সকল ব্যাগ (All Bags)
            </button>
            <button type="button" class="sb-persona-tab" data-persona="kids">
                <i class="fa fa-child"></i> প্রিস্কুল (বয়স ৩-৬)
            </button>
            <button type="button" class="sb-persona-tab" data-persona="primary">
                <i class="fa fa-book-reader"></i> প্রাইমারি স্কুল (১ম - ৫ম)
            </button>
            <button type="button" class="sb-persona-tab" data-persona="high">
                <i class="fa fa-user-graduate"></i> হাইস্কুল ও কলেজ
            </button>
            <button type="button" class="sb-persona-tab" data-persona="trolley">
                <i class="fa fa-suitcase-rolling"></i> ট্রলি ও হুইল ব্যাগ
            </button>
        </div>
    </div>
</section>

<!-- Main Catalog Container -->
<section class="sb-catalog-section">
    <div class="container">
        <!-- Live Toolbar -->
        <div class="sb-toolbar">
            <div class="sb-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="sb-search-input" placeholder="মডেল, কালার বা নাম দিয়ে খুঁজুন..." autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="sb-sort-box">
                    <label for="sb-sort-select"><i class="fa fa-sort"></i> সাজান:</label>
                    <select id="sb-sort-select" class="sb-sort-select">
                        <option value="latest">নতুন কালেকশন</option>
                        <option value="price_low">দাম: কম থেকে বেশি</option>
                        <option value="price_high">দাম: বেশি থেকে কম</option>
                        <option value="oldest">পুরাতন কালেকশন</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Dynamic Product Grid Loaded via AJAX -->
        <div id="school-bag-grid-container">
            @include('frontEnd.layouts.pages._school_bag_grid', ['products' => $products])
        </div>
    </div>
</section>

<!-- Trust & Benefits Showcase -->
<section class="sb-trust-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="sb-trust-card">
                    <div class="sb-trust-icon"><i class="fa fa-heartbeat"></i></div>
                    <h4 class="sb-trust-title">মেরুদণ্ড সুরক্ষা</h4>
                    <p class="sb-trust-desc">Ergonomic padded backpanel যা শিশুর পিঠের ওপর থেকে অতিরিক্ত চাপ কমায়।</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="sb-trust-card">
                    <div class="sb-trust-icon"><i class="fa fa-tint-slash"></i></div>
                    <h4 class="sb-trust-title">ওয়াটারপ্রুফ মেটেরিয়াল</h4>
                    <p class="sb-trust-desc">প্রিমিয়াম নাইলন ও পলিয়েস্টার কাপড় যা ব্যাগ ও ভেতরের বই খাতাকে ভেজা থেকে রক্ষা করে।</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="sb-trust-card">
                    <div class="sb-trust-icon"><i class="fa fa-layer-group"></i></div>
                    <h4 class="sb-trust-title">মাল্টি-লেয়ার জিপার</h4>
                    <p class="sb-trust-desc">ল্যাপটপ, বোতল ও টিফিন বক্স রাখার জন্য সুবিধাজনক আলাদা চেম্বার।</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="sb-trust-card">
                    <div class="sb-trust-icon"><i class="fa fa-hand-holding-usd"></i></div>
                    <h4 class="sb-trust-title">ক্যাশ অন ডেলিভারি</h4>
                    <p class="sb-trust-desc">পণ্য হাতে পেয়ে দেখে শুনে মূল্য পরিশোধের নিশ্চিন্ত সুবিধা।</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Order Modal -->
<div class="modal fade" id="quickOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sb-modal">
            <div class="sb-modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title"><i class="fa fa-shopping-bag me-2"></i> দ্রুত অর্ডার করুন</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('customer.ordersave') }}" method="POST" id="quick-order-form">
                    @csrf
                    <input type="hidden" name="product_id" id="modal_product_id">
                    <input type="hidden" name="qty" value="1" id="modal_product_qty">

                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3 border">
                        <img src="" id="modal_product_img" style="width: 60px; height: 60px; object-fit: cover;" class="rounded-2">
                        <div>
                            <h6 id="modal_product_name" class="fw-bold mb-1 text-dark" style="font-size: 14px;"></h6>
                            <div class="text-success fw-bold" style="font-size: 15px;">মূল্য: ৳<span id="modal_product_price"></span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">আপনার নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="আপনার সম্পূর্ণ নাম লিখুন" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">মোবাইল নাম্বার <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-lg" placeholder="১১ ডিজিটের মোবাইল নাম্বার" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">সম্পূর্ণ ঠিকানা <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" placeholder="জেলা, থানা, গ্রাম/রোড নাম..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">ডেলিভারি এরিয়া <span class="text-danger">*</span></label>
                        <select name="area" class="form-select form-select-lg" required>
                            @foreach($shipping_charge as $shipping)
                                <option value="{{ $shipping->id }}">{{ $shipping->name }} (৳{{ $shipping->amount }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold py-3 text-uppercase rounded-3 shadow">
                        <i class="fa fa-check-circle me-2"></i> অর্ডার কনফার্ম করুন
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document.body).ready(function() {
    let currentPersona = 'all';
    let currentSearch = '';
    let currentSort = 'latest';

    // Persona Tab Click
    $('.sb-persona-tab').on('click', function() {
        $('.sb-persona-tab').removeClass('active');
        $(this).addClass('active');
        currentPersona = $(this).data('persona');
        fetchProducts(1);
    });

    // Search Input Trigger (Debounced)
    let searchTimeout;
    $('#sb-search-input').on('keyup input', function() {
        clearTimeout(searchTimeout);
        currentSearch = $(this).val();
        searchTimeout = setTimeout(function() {
            fetchProducts(1);
        }, 350);
    });

    // Sort Dropdown Trigger
    $('#sb-sort-select').on('change', function() {
        currentSort = $(this).val();
        fetchProducts(1);
    });

    // AJAX Fetch Function
    function fetchProducts(page = 1) {
        $('#school-bag-grid-container').css('opacity', '0.5');

        $.ajax({
            url: "{{ route('schoolbags.landing') }}",
            type: "GET",
            data: {
                persona: currentPersona,
                search: currentSearch,
                sort: currentSort,
                page: page
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#school-bag-grid-container').html(response.html).css('opacity', '1');
                }
            },
            error: function() {
                $('#school-bag-grid-container').css('opacity', '1');
            }
        });
    }

    // Handle AJAX Pagination Click
    $(document).on('click', '.sb-pagination-wrap .pagination a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetchProducts(page);
        $('html, body').animate({
            scrollTop: $('#school-bag-grid-container').offset().top - 140
        }, 400);
    });

    // Reset Filters
    $(document).on('click', '.btn-reset-filters', function() {
        currentPersona = 'all';
        currentSearch = '';
        currentSort = 'latest';
        $('#sb-search-input').val('');
        $('#sb-sort-select').val('latest');
        $('.sb-persona-tab').removeClass('active');
        $('.sb-persona-tab[data-persona="all"]').addClass('active');
        fetchProducts(1);
    });

    // Quick Order Modal trigger
    $(document).on('click', '.open-quick-modal', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = $(this).data('price');
        let img = $(this).data('img');

        $('#modal_product_id').val(id);
        $('#modal_product_name').text(name);
        $('#modal_product_price').text(price);
        $('#modal_product_img').attr('src', img);

        let modal = new bootstrap.Modal(document.getElementById('quickOrderModal'));
        modal.show();
    });
});
</script>
@endpush
