@extends('frontEnd.layouts.master')
@section('title', $campaign_data->name ?? 'Special Campaign')
@push('css')
<style>
/* ───── Campaign Page Base & Relational Theme Tokens ───── */
:root {
    --cmp-primary: #047857;
    --cmp-primary-dark: #064e3b;
    --cmp-accent: #d97706;
    --cmp-bg-canvas: #f8f8f6;
    --cmp-card-bg: #e9e4d9;
    --cmp-text-dark: #111827;
    --cmp-text-muted: #4b5563;
    --cmp-border: #d6d0c4;
}

/* Page Outer Container */
.cmp-page-wrapper {
    background-color: var(--cmp-bg-canvas);
    padding: 30px 0 70px;
    min-height: 100vh;
}
.gani-product-card {
    background: #fff;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}


/* ───── Hero Header Section (Reference Design Match) ───── */
.cmp-hero-card {
    background: var(--cmp-card-bg);
    border-radius: 24px;
    padding: 45px 48px 36px;
    position: relative;
    overflow: hidden;
    margin-bottom: 35px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.04);
}

@if($campaign_data->banner)
.cmp-hero-card.has-banner-bg{
    background:none;
}
@endif

/* Breadcrumb Navigation */
.cmp-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 14px;
}
.cmp-breadcrumb i {
    font-size: 10px;
    color: #9ca3af;
}

/* Main Giant Heading */
.cmp-hero-title {
    font-size: 50px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -0.5px;
    color: var(--cmp-primary-dark);
    line-height: 1.1;
    margin-bottom: 14px;
    font-family: 'Jost', 'Roboto', sans-serif;
}

/* Description Text */
.cmp-hero-desc {
    font-size: 15.5px;
    color: var(--cmp-text-muted);
    max-width: 840px;
    line-height: 1.65;
    margin-bottom: 28px;
}

/* ───── Pill Controls Bar (Bottom of Hero Card) ───── */
.cmp-pills-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 10px;
}

.cmp-pill-select, .cmp-pill-input {
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 30px;
    padding: 9px 20px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--cmp-text-dark);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
    outline: none;
}

.cmp-pill-select:focus, .cmp-pill-input:focus {
    border-color: var(--cmp-primary);
    box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.15);
}

.cmp-pill-search {
    position: relative;
    flex-grow: 1;
    max-width: 280px;
}
.cmp-pill-search input {
    width: 100%;
    padding-left: 38px;
}
.cmp-pill-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
}

/* Countdown Timer Pill */
.cmp-timer-pill {
    background: var(--cmp-primary-dark);
    color: #ffffff;
    border-radius: 30px;
    padding: 6px 18px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 14px rgba(6, 78, 59, 0.25);
    margin-left: auto;
}
.cmp-timer-pill-title {
    font-size: 12px;
    font-weight: 700;
    color: #fde047;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.cmp-timer-digits {
    display: flex;
    align-items: center;
    gap: 6px;
}
.cmp-timer-num-box {
    background: rgba(255, 255, 255, 0.15);
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 800;
    font-size: 14px;
    color: #ffffff;
}



/* Quick Order Modal */
.modal-content.cmp-modal {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}
.cmp-modal-header {
    background: var(--cmp-primary-dark);
    color: #ffffff;
    padding: 16px 22px;
}
.cmp-modal-header h5 {
    color: #ffffff;
    font-weight: 700;
    margin: 0;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .cmp-hero-card {
        padding: 28px 20px 24px;
        border-radius: 18px;
    }
    .cmp-hero-title {
        font-size: 32px;
    }
    .cmp-pill-search {
        max-width: 100%;
        width: 100%;
    }
    .cmp-timer-pill {
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="cmp-page-wrapper">
    <div class="container">
        
        <!-- Hero Header Card (Reference Design Match) -->
        <div class="cmp-hero-card {{ $campaign_data->banner ? 'has-banner-bg' : '' }}">
            <!-- Breadcrumb Navigation -->
            <div class="cmp-breadcrumb">
                <span>HOME</span> <i class="fa fa-chevron-right"></i>
                <span>CAMPAIGN</span> <i class="fa fa-chevron-right"></i>
                <span class="text-dark fw-bold">{{ strtoupper($campaign_data->name) }}</span>
            </div>

            <!-- Main Heading Title -->
            <h1 class="cmp-hero-title">
                {{ $campaign_data->heading_1 ?? $campaign_data->name }}
            </h1>

            <!-- Description -->
            <p class="cmp-hero-desc">
                {!! nl2br(e($campaign_data->short_description ?? $campaign_data->description ?? 'Get authentic products at attractive offer prices. Order directly with Cash on Delivery.')) !!}
            </p>

            <!-- Bottom Control Pills Bar -->
            <div class="cmp-pills-bar">
                <!-- Category Select Pill -->
                @if(isset($categories) && $categories->count() > 0)
                <select class="cmp-pill-select" id="cmp-cat-select">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @endif

                <!-- Sort Select Pill -->
                <select class="cmp-pill-select" id="cmp-sort-select">
                    <option value="latest">Newest Products</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="oldest">Oldest Products</option>
                </select>

                <!-- Search Input Pill -->
                <div class="cmp-pill-search">
                    <i class="fa fa-search"></i>
                    <input type="text" id="cmp-search-input" class="cmp-pill-input" placeholder="Search products..." autocomplete="off">
                </div>

                <!-- Countdown Timer Pill -->
                @if($campaign_data->deadline)
                <div class="cmp-timer-pill">
                    <span class="cmp-timer-pill-title"><i class="fa fa-clock-o me-1"></i> Offer Ends In:</span>
                    <div class="cmp-timer-digits" id="cmp-timer" data-deadline="{{ $campaign_data->deadline }}">
                        <span class="cmp-timer-num-box" id="t-days">00</span>:
                        <span class="cmp-timer-num-box" id="t-hours">00</span>:
                        <span class="cmp-timer-num-box" id="t-mins">00</span>:
                        <span class="cmp-timer-num-box" id="t-secs">00</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Dynamic Product Catalog Grid Container -->
        <div id="campaign-grid-container">
            @include('frontEnd.layouts.pages.campaign._campaign_product_grid', ['products' => $products])
        </div>

    </div>
</div>

<!-- Quick Order Modal -->
<div class="modal fade" id="quickOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cmp-modal">
            <div class="cmp-modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title"><i class="fa fa-shopping-bag me-2"></i> Quick Order</h5>
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
                            <div class="text-success fw-bold" style="font-size: 15px;">Price: ৳<span id="modal_product_price"></span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">Your Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter your full name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-lg" placeholder="Enter 11-digit phone number" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">Full Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" placeholder="District, Thana, Road / Area name..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-6">Delivery Area <span class="text-danger">*</span></label>
                        <select name="area" class="form-select form-select-lg" required>
                            @foreach($shippingcharge as $shipping)
                                <option value="{{ $shipping->id }}">{{ $shipping->name }} (৳{{ $shipping->amount }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold py-3 text-uppercase rounded-3 shadow">
                        <i class="fa fa-check-circle me-2"></i> Confirm Order
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

    // Category Pill Dropdown Select Trigger
    $('#cmp-cat-select').on('change', function() {
        currentCategory = $(this).val();
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
        $('#cmp-cat-select').val('all');
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
