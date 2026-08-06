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


/* ───── Full Width Banner Box (Reference Match) ───── */
.cmp-banner-card {
    position: relative;
    border-radius: 20px;
    padding: 45px 50px;
    margin-bottom: 24px;
    overflow: hidden;
    color: #ffffff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    background-color: #0f172a;
    @if($campaign_data->banner)
    background-image: linear-gradient(90deg, rgba(15, 23, 42, 0.94) 0%, rgba(15, 23, 42, 0.82) 45%, rgba(15, 23, 42, 0.45) 100%), url('{{ asset($campaign_data->banner) }}');
    background-position: center center;
    background-size: cover;
    background-repeat: no-repeat;
    @endif
}

.cmp-banner-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35);
    margin-bottom: 16px;
}

.cmp-banner-title {
    font-size: clamp(26px, 3.8vw, 42px);
    font-weight: 800;
    color: #ffffff;
    line-height: 1.25;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.cmp-banner-subtitle {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 22px;
    max-width: 580px;
    line-height: 1.5;
}

/* Translucent Glassmorphic Timer Box */
.cmp-glass-timer {
    background: rgba(255, 255, 255, 0.07);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    padding: 12px 22px;
    display: inline-flex;
    align-items: center;
    gap: 16px;
}

.cmp-timer-unit {
    text-align: center;
}

.cmp-timer-unit-val {
    font-size: 26px;
    font-weight: 800;
    color: #facc15;
    line-height: 1;
    display: block;
    min-width: 38px;
}

.cmp-timer-unit-lbl {
    font-size: 9.5px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.55);
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-top: 4px;
    display: block;
}

.cmp-timer-colon {
    font-size: 20px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.4);
    margin-bottom: 12px;
}

/* ───── White Filter Subbar (Below Banner) ───── */
.cmp-subbar {
    background: #ffffff;
    border-radius: 12px;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.03);
}

.cmp-subbar-text {
    font-size: 13.5px;
    color: #64748b;
}

.cmp-subbar-text strong {
    color: #0f172a;
    font-weight: 700;
}

.cmp-subbar-controls {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.cmp-pill-select, .cmp-pill-input {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    outline: none;
    transition: all 0.2s ease;
}
.cmp-pill-select{
    display:none;
}

.cmp-pill-select:focus, .cmp-pill-input:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
}

.cmp-pill-search {
    position: relative;
    width: 220px;
}

.cmp-pill-search input {
    width: 100%;
    padding-left: 36px;
}

.cmp-pill-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 12px;
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
    .cmp-banner-card {
        padding: 30px 20px;
        border-radius: 16px;
    }
    .cmp-glass-timer {
        padding: 10px 14px;
        gap: 10px;
        width: 100%;
        justify-content: center;
    }
    .cmp-timer-unit-val {
        font-size: 20px;
    }
    .cmp-subbar {
        flex-direction: column;
        align-items: flex-start;
    }
    .cmp-subbar-controls {
        width: 100%;
    }
    .cmp-pill-search {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="cmp-page-wrapper">
    <div class="container">
        
        <!-- Hero Banner Card (Reference Design Match) -->
        <div class="cmp-banner-card">
            <!-- Badge Tag -->
            <span class="cmp-banner-badge">
                <i class="fa fa-bolt"></i> {{ strtoupper($campaign_data->top_title_1 ?? 'UP TO 30% OFF') }}
            </span>

            <!-- Title -->
            <h1 class="cmp-banner-title">
                {{ $campaign_data->heading_1 ?? $campaign_data->name }}
            </h1>

            <!-- Subtitle / Short Description -->
            <p class="cmp-banner-subtitle">
                {!! nl2br(e(Str::limit($campaign_data->short_description ?? $campaign_data->description ?? 'Get authentic products at attractive offer prices. Order directly with Cash on Delivery.', 120))) !!}
            </p>

            <!-- Translucent Glassmorphic Timer Box -->
            @if($campaign_data->deadline)
            <div class="cmp-glass-timer" id="cmp-timer" data-deadline="{{ $campaign_data->deadline }}">
                <div class="cmp-timer-unit">
                    <span class="cmp-timer-unit-val" id="t-days">00</span>
                    <span class="cmp-timer-unit-lbl">DAYS</span>
                </div>
                <span class="cmp-timer-colon">:</span>
                <div class="cmp-timer-unit">
                    <span class="cmp-timer-unit-val" id="t-hours">00</span>
                    <span class="cmp-timer-unit-lbl">HOURS</span>
                </div>
                <span class="cmp-timer-colon">:</span>
                <div class="cmp-timer-unit">
                    <span class="cmp-timer-unit-val" id="t-mins">00</span>
                    <span class="cmp-timer-unit-lbl">MINS</span>
                </div>
                <span class="cmp-timer-colon">:</span>
                <div class="cmp-timer-unit">
                    <span class="cmp-timer-unit-val" id="t-secs">00</span>
                    <span class="cmp-timer-unit-lbl">SECS</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Subbar: Products Count & Sorting / Search Controls -->
        <div class="cmp-subbar">
            <div class="cmp-subbar-text">
                Showing <strong>{{ $products->total() }}</strong> offer products
            </div>

            <div class="cmp-subbar-controls">
                @if(isset($categories) && $categories->count() > 0)
                <select class="cmp-pill-select" id="cmp-cat-select">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @endif

                <select class="cmp-pill-select" id="cmp-sort-select">
                    <option value="latest">Sort By: Default Order</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="oldest">Oldest Products</option>
                </select>

                <div class="cmp-pill-search">
                    <i class="fa fa-search"></i>
                    <input type="text" id="cmp-search-input" class="cmp-pill-input" placeholder="Search products..." autocomplete="off">
                </div>
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
