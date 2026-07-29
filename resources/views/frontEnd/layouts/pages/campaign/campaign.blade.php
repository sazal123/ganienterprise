@extends('frontEnd.layouts.master')
@section('title', $campaign_data->name ?? 'বিশেষ ক্যাম্পেইন')
@push('css')
<style>
/* ───── Campaign Landing Tokens & Styling ───── */
:root {
    --cmp-primary: #059669;
    --cmp-primary-dark: #047857;
    --cmp-accent: #f59e0b;
    --cmp-dark-bg: #0f172a;
    --cmp-border: #e2e8f0;
    --cmp-text-main: #1e293b;
    --cmp-text-muted: #64748b;
}

/* ───── Hero Header Section ───── */
.cmp-hero {
    position: relative;
    padding: 85px 0 75px;
    color: #ffffff;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    overflow: hidden;
    min-height: 440px;
    display: flex;
    align-items: center;
}
.cmp-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.75) 50%, rgba(6, 78, 59, 0.85) 100%);
    z-index: 1;
}
.cmp-hero-content {
    position: relative;
    z-index: 2;
}
.cmp-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(245, 158, 11, 0.25);
    backdrop-filter: blur(8px);
    border: 1px solid var(--cmp-accent);
    color: #fde047;
    padding: 7px 22px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 18px;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
}
.cmp-hero h1 {
    font-size: 38px;
    font-weight: 800;
    line-height: 1.35;
    margin-bottom: 16px;
    color: #ffffff;
    text-shadow: 0 2px 12px rgba(0,0,0,0.4);
}
.cmp-hero p {
    font-size: 16px;
    color: #f1f5f9;
    max-width: 780px;
    line-height: 1.7;
    margin: 0 auto 26px;
    text-shadow: 0 1px 6px rgba(0,0,0,0.4);
}

/* Countdown Timer */
.cmp-countdown-box {
    display: flex;
    gap: 14px;
    margin-top: 10px;
    justify-content: center;
}
.cmp-timer-unit {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 10px 20px;
    border-radius: 12px;
    text-align: center;
    min-width: 80px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.25);
}
.cmp-timer-num {
    font-size: 24px;
    font-weight: 800;
    color: #fde047;
    display: block;
}
.cmp-timer-label {
    font-size: 12px;
    color: #e2e8f0;
    text-transform: uppercase;
    font-weight: 600;
}

/* ───── Category Navigation Tabs ───── */
.cmp-cat-section {
    background: #f8fafc;
    padding: 20px 0 14px;
    border-bottom: 1px solid var(--cmp-border);
    position: sticky;
    top: 60px;
    z-index: 99;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.cmp-cat-nav {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 6px;
    scrollbar-width: thin;
}
.cmp-cat-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    background: #ffffff;
    border: 1px solid var(--cmp-border);
    border-radius: 50px;
    color: var(--cmp-text-main);
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.25s ease;
}
.cmp-cat-tab:hover {
    border-color: var(--cmp-primary);
    color: var(--cmp-primary);
}
.cmp-cat-tab.active {
    background: var(--cmp-primary);
    color: #ffffff;
    border-color: var(--cmp-primary);
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
}

/* ───── Main Catalog Section ───── */
.cmp-catalog-section {
    padding: 30px 0 60px;
    background: #f8fafc;
    min-height: 500px;
}

/* Toolbar Controls */
.cmp-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 12px;
    border: 1px solid var(--cmp-border);
    margin-bottom: 24px;
}
.cmp-search-box {
    position: relative;
    min-width: 280px;
    flex-grow: 1;
    max-width: 400px;
}
.cmp-search-box input {
    width: 100%;
    padding: 9px 16px 9px 40px;
    border: 1px solid var(--cmp-border);
    border-radius: 50px;
    font-size: 13.5px;
    background: #f8fafc;
    transition: border-color 0.2s;
}
.cmp-search-box input:focus {
    outline: none;
    border-color: var(--cmp-primary);
    background: #ffffff;
}
.cmp-search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--cmp-text-muted);
}

.cmp-sort-box {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cmp-sort-box label {
    font-size: 13px;
    color: var(--cmp-text-muted);
    white-space: nowrap;
}
.cmp-sort-select {
    padding: 8px 30px 8px 14px;
    border: 1px solid var(--cmp-border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--cmp-text-main);
    background: #ffffff;
    cursor: pointer;
}

/* ───── Product Card Styling ───── */
.cmp-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid var(--cmp-border);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.cmp-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    border-color: #cbd5e1;
}
.cmp-card-img-wrap {
    position: relative;
    width: 100%;
    padding-top: 100%;
    background: #f1f5f9;
    overflow: hidden;
}
.cmp-card-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.cmp-card:hover .cmp-card-img {
    transform: scale(1.06);
}

.cmp-badge-discount {
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
.cmp-badge-new {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--cmp-primary);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    z-index: 2;
}

.cmp-card-actions {
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
.cmp-card:hover .cmp-card-actions {
    bottom: 0;
}
.cmp-quick-order-btn {
    width: 100%;
    background: var(--cmp-primary);
    color: #ffffff;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.cmp-quick-order-btn:hover {
    background: var(--cmp-primary-dark);
}

.cmp-card-body {
    padding: 14px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.cmp-card-title-link {
    text-decoration: none;
    color: inherit;
}
.cmp-card-title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--cmp-text-main);
    line-height: 1.4;
    margin-bottom: 6px;
    min-height: 38px;
}
.cmp-card-rating {
    font-size: 11px;
    margin-bottom: 8px;
}
.cmp-review-count {
    color: var(--cmp-text-muted);
    font-size: 11px;
    margin-left: 4px;
}
.cmp-card-price-wrap {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 12px;
}
.cmp-card-price {
    font-size: 16px;
    font-weight: 800;
    color: var(--cmp-primary);
}
.cmp-card-old-price {
    font-size: 12.5px;
    color: var(--cmp-text-muted);
}
.cmp-btn-details {
    margin-top: auto;
    display: block;
    text-align: center;
    padding: 7px 12px;
    background: #f1f5f9;
    color: var(--cmp-text-main);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.cmp-btn-details:hover {
    background: var(--cmp-primary);
    color: #ffffff;
}

/* ───── Review Gallery Section ───── */
.cmp-review-section {
    background: #ffffff;
    padding: 40px 0;
    border-top: 1px solid var(--cmp-border);
}
.cmp-review-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid var(--cmp-border);
    transition: transform 0.3s;
}
.cmp-review-img:hover {
    transform: scale(1.03);
}

/* ───── Quick Order Modal ───── */
.modal-content.cmp-modal {
    border-radius: 16px;
    border: none;
    overflow: hidden;
}
.cmp-modal-header {
    background: var(--cmp-primary);
    color: #ffffff;
    padding: 16px 20px;
}
.cmp-modal-header h5 {
    color: #ffffff;
    font-weight: 700;
    margin: 0;
}
</style>
@endpush

@section('content')
@php
    $heroBanner = $campaign_data->banner 
        ? asset($campaign_data->banner) 
        : asset('frontEnd/img/default-product.jpg');
@endphp

<!-- Dynamic Full-Width Hero Banner Section -->
<section class="cmp-hero" style="background-image: url('{{ $heroBanner }}');">
    <div class="cmp-hero-overlay"></div>
    <div class="container cmp-hero-content">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9 col-xl-8">
                <span class="cmp-hero-badge mx-auto">
                    <i class="fa fa-fire"></i> {{ $campaign_data->top_title_1 ?? 'বিশেষ ডিসকাউন্ট অফার' }}
                </span>

                <h1>{{ $campaign_data->heading_1 ?? $campaign_data->name }}</h1>

                <p>
                    {!! nl2br(e($campaign_data->short_description ?? $campaign_data->description ?? 'সেরা কোয়ালিটির আসল পণ্য এখন পান আকর্ষনীয় অফার মূল্যে। ক্যাশ অন ডেলিভারিতে সরাসরি অর্ডার করুন।')) !!}
                </p>

                @if($campaign_data->deadline)
                <div class="d-flex flex-column align-items-center gap-2 mt-3">
                    <span class="fw-bold text-warning" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fa fa-clock-o me-1"></i> অফারের বাকী সময়:
                    </span>
                    <div class="cmp-countdown-box" id="cmp-timer" data-deadline="{{ $campaign_data->deadline }}">
                        <div class="cmp-timer-unit"><span class="cmp-timer-num" id="t-days">00</span><span class="cmp-timer-label">দিন</span></div>
                        <div class="cmp-timer-unit"><span class="cmp-timer-num" id="t-hours">00</span><span class="cmp-timer-label">ঘন্টা</span></div>
                        <div class="cmp-timer-unit"><span class="cmp-timer-num" id="t-mins">00</span><span class="cmp-timer-label">মিনিট</span></div>
                        <div class="cmp-timer-unit"><span class="cmp-timer-num" id="t-secs">00</span><span class="cmp-timer-label">সেকেন্ড</span></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Category Tabs -->
@if(isset($categories) && $categories->count() > 0)
<section class="cmp-cat-section">
    <div class="container">
        <div class="cmp-cat-nav">
            <button type="button" class="cmp-cat-tab active" data-category="all">
                <i class="fa fa-th-large"></i> সকল পণ্য (All Products)
            </button>
            @foreach($categories as $cat)
                <button type="button" class="cmp-cat-tab" data-category="{{ $cat->id }}">
                    <i class="fa fa-tag"></i> {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Main Dynamic Product Catalog -->
<section class="cmp-catalog-section">
    <div class="container">
        <!-- Live Toolbar -->
        <div class="cmp-toolbar">
            <div class="cmp-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="cmp-search-input" placeholder="পণ্য বা মডেল দিয়ে খুঁজুন..." autocomplete="off">
            </div>

            <div class="cmp-sort-box">
                <label for="cmp-sort-select"><i class="fa fa-sort"></i> সাজান:</label>
                <select id="cmp-sort-select" class="cmp-sort-select">
                    <option value="latest">নতুন কালেকশন</option>
                    <option value="price_low">দাম: কম থেকে বেশি</option>
                    <option value="price_high">দাম: বেশি থেকে কম</option>
                    <option value="oldest">পুরাতন কালেকশন</option>
                </select>
            </div>
        </div>

        <!-- AJAX Dynamic Product Grid -->
        <div id="campaign-grid-container">
            @include('frontEnd.layouts.pages.campaign._campaign_product_grid', ['products' => $products])
        </div>
    </div>
</section>

<!-- Quick Order Modal -->
<div class="modal fade" id="quickOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cmp-modal">
            <div class="cmp-modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title"><i class="fa fa-shopping-bag me-2"></i> সরাসরি অর্ডার করুন</h5>
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
                            @foreach($shippingcharge as $shipping)
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
    let currentCategory = 'all';
    let currentSearch = '';
    let currentSort = 'latest';

    // Category Tab Click
    $('.cmp-cat-tab').on('click', function() {
        $('.cmp-cat-tab').removeClass('active');
        $(this).addClass('active');
        currentCategory = $(this).data('category');
        fetchCampaignProducts(1);
    });

    // Search Input Trigger
    let searchTimeout;
    $('#cmp-search-input').on('keyup input', function() {
        clearTimeout(searchTimeout);
        currentSearch = $(this).val();
        searchTimeout = setTimeout(function() {
            fetchCampaignProducts(1);
        }, 350);
    });

    // Sort Dropdown Trigger
    $('#cmp-sort-select').on('change', function() {
        currentSort = $(this).val();
        fetchCampaignProducts(1);
    });

    // AJAX Fetch Products Function
    function fetchCampaignProducts(page = 1) {
        $('#campaign-grid-container').css('opacity', '0.5');

        $.ajax({
            url: "{{ route('campaign', $campaign_data->slug) }}",
            type: "GET",
            data: {
                category_id: currentCategory,
                search: currentSearch,
                sort: currentSort,
                page: page
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#campaign-grid-container').html(response.html).css('opacity', '1');
                }
            },
            error: function() {
                $('#campaign-grid-container').css('opacity', '1');
            }
        });
    }

    // Pagination AJAX Click
    $(document).on('click', '.cmp-pagination-wrap .pagination a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetchCampaignProducts(page);
        $('html, body').animate({
            scrollTop: $('#campaign-grid-container').offset().top - 140
        }, 400);
    });

    // Reset Filters
    $(document).on('click', '.btn-reset-filters', function() {
        currentCategory = 'all';
        currentSearch = '';
        currentSort = 'latest';
        $('#cmp-search-input').val('');
        $('#cmp-sort-select').val('latest');
        $('.cmp-cat-tab').removeClass('active');
        $('.cmp-cat-tab[data-category="all"]').addClass('active');
        fetchCampaignProducts(1);
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

    // Countdown Timer Logic
    const timerElem = document.getElementById('cmp-timer');
    if (timerElem && timerElem.dataset.deadline) {
        const deadline = new Date(timerElem.dataset.deadline).getTime();
        const updateTimer = function() {
            const now = new Date().getTime();
            const diff = deadline - now;

            if (diff <= 0) {
                $('#t-days').text('00');
                $('#t-hours').text('00');
                $('#t-mins').text('00');
                $('#t-secs').text('00');
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((diff % (1000 * 60)) / 1000);

            $('#t-days').text(days < 10 ? '0' + days : days);
            $('#t-hours').text(hours < 10 ? '0' + hours : hours);
            $('#t-mins').text(mins < 10 ? '0' + mins : mins);
            $('#t-secs').text(secs < 10 ? '0' + secs : secs);
        };
        updateTimer();
        setInterval(updateTimer, 1000);
    }
});
</script>
@endpush
