@extends('frontEnd.layouts.master') 
@section('title', $activeOffer ? $activeOffer->title : 'Exclusive Special Offers') 

@push('css')
<style>
    /* Senior Designer Offer Page Styles */
    .gani-offer-hero {
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 20px;
        overflow: hidden;
        margin-top: 20px;
        margin-bottom: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    .gani-offer-hero-overlay {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 41, 59, 0.82) 100%);
        padding: 60px 40px;
        color: #ffffff;
    }
    .gani-offer-badge {
        display: inline-block;
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 6px 18px;
        border-radius: 50px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
    }
    .gani-offer-title {
        font-size: 2.75rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }
    .gani-offer-subtitle {
        font-size: 1.15rem;
        color: #cbd5e1;
        max-width: 650px;
        margin-bottom: 30px;
        line-height: 1.6;
    }
    
    /* Countdown Timer */
    .gani-timer-wrap {
        display: inline-flex;
        gap: 16px;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 16px 24px;
        border-radius: 16px;
    }
    .gani-timer-unit {
        text-align: center;
        min-width: 65px;
    }
    .gani-timer-num {
        font-size: 2rem;
        font-weight: 800;
        color: #fef08a;
        line-height: 1;
        font-family: monospace, monospace;
    }
    .gani-timer-lbl {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin-top: 6px;
        font-weight: 600;
    }
    .gani-timer-colon {
        font-size: 1.75rem;
        font-weight: 800;
        color: #cbd5e1;
        display: flex;
        align-items: center;
    }

    /* Tabs / Filter Toolbar */
    .gani-offer-tabs {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 30px;
    }
    .gani-offer-tab-btn {
        padding: 10px 22px;
        border-radius: 50px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.25s ease;
        white-space: nowrap;
        border: 1px solid #e2e8f0;
    }
    .gani-offer-tab-btn:hover, .gani-offer-tab-btn.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }

    .gani-offer-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }
    .gani-offer-count {
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 500;
    }
    .gani-offer-count strong {
        color: #0f172a;
    }

    @media (max-width: 768px) {
        .gani-offer-hero-overlay {
            padding: 35px 20px;
        }
        .gani-offer-title {
            font-size: 1.85rem;
        }
        .gani-offer-subtitle {
            font-size: 0.95rem;
        }
        .gani-timer-wrap {
            gap: 10px;
            padding: 12px 16px;
        }
        .gani-timer-unit {
            min-width: 48px;
        }
        .gani-timer-num {
            font-size: 1.4rem;
        }
        .gani-offer-toolbar {
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }
    }
</style>
@endpush 

@section('content')
<div class="container">
    {{-- Hero Offer Banner --}}
    @php
        $bgImage = $activeOffer && $activeOffer->banner ? asset($activeOffer->banner) : 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=2070&auto=format&fit=crop';
    @endphp
    <div class="gani-offer-hero" style="background-image: url('{{ $bgImage }}');">
        <div class="gani-offer-hero-overlay">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="gani-offer-badge">
                        <i class="fa-solid fa-bolt me-1"></i> {{ $activeOffer ? ($activeOffer->discount_tag ?? 'LIMITED TIME OFFER') : 'SPECIAL PROMOTION' }}
                    </span>
                    <h1 class="gani-offer-title">{{ $activeOffer ? $activeOffer->title : 'Exclusive Hot Deals & Offers' }}</h1>
                    <p class="gani-offer-subtitle">
                        {{ $activeOffer ? ($activeOffer->subtitle ?: 'Grab our highest-rated products at unbeatable promotional prices before stocks run out!') : 'Shop top quality products with special discount pricing available for a limited time.' }}
                    </p>
                    
                    @if($activeOffer && $activeOffer->end_date)
                    <div class="gani-timer-wrap" id="ganiOfferTimer" data-endtime="{{ \Carbon\Carbon::parse($activeOffer->end_date)->format('Y-m-d H:i:s') }}">
                        <div class="gani-timer-unit">
                            <div class="gani-timer-num" id="timerDays">00</div>
                            <div class="gani-timer-lbl">Days</div>
                        </div>
                        <div class="gani-timer-colon">:</div>
                        <div class="gani-timer-unit">
                            <div class="gani-timer-num" id="timerHours">00</div>
                            <div class="gani-timer-lbl">Hours</div>
                        </div>
                        <div class="gani-timer-colon">:</div>
                        <div class="gani-timer-unit">
                            <div class="gani-timer-num" id="timerMinutes">00</div>
                            <div class="gani-timer-lbl">Mins</div>
                        </div>
                        <div class="gani-timer-colon">:</div>
                        <div class="gani-timer-unit">
                            <div class="gani-timer-num" id="timerSeconds">00</div>
                            <div class="gani-timer-lbl">Secs</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Offers Tabs Navigation (if multiple active offers exist) --}}
    @if(isset($allOffers) && $allOffers->count() > 1)
    <div class="gani-offer-tabs">
        @foreach($allOffers as $off)
        <a href="{{ route('offers', ['offer' => $off->slug]) }}" class="gani-offer-tab-btn {{ ($activeOffer && $activeOffer->id == $off->id) ? 'active' : '' }}">
            <i class="fa-solid fa-tag me-1"></i> {{ $off->title }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- Filter Toolbar --}}
    <div class="gani-offer-toolbar">
        <div class="gani-offer-count">
            Showing <strong>{{ $products->total() }}</strong> offer products
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="sortOffer" class="text-muted small text-nowrap fw-semibold">Sort By:</label>
            <form action="{{ route('offers') }}" method="GET" class="d-inline" id="sortOfferForm">
                @if(request('offer')) <input type="hidden" name="offer" value="{{ request('offer') }}"> @endif
                <select name="sort" class="form-select form-select-sm border-secondary-subtle" id="sortOffer" onchange="document.getElementById('sortOfferForm').submit()">
                    <option value="">Default Order</option>
                    <option value="low_high" {{ request('sort') == 'low_high' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="high_low" {{ request('sort') == 'high_low' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrival</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="row g-3 g-md-4">
        @forelse($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="p-5 bg-light rounded-4">
                    <i class="fa-solid fa-gift text-muted display-4 mb-3"></i>
                    <h4>No Offer Products Found</h4>
                    <p class="text-muted">Check back soon for new special promotions and discounts!</p>
                    <a href="{{ route('shop') }}" class="btn btn-dark rounded-pill px-4 mt-2">Explore Shop</a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links() }}
    </div>

    {{-- Trust Features Section --}}
    <div class="row py-5 mt-4 border-top">
        <div class="col-6 col-md-3 text-center mb-3 mb-md-0">
            <i class="fa-solid fa-truck-fast text-gold fs-2 mb-2"></i>
            <h6 class="fw-bold mb-1">Fast Delivery</h6>
            <small class="text-muted">Fast nationwide shipping</small>
        </div>
        <div class="col-6 col-md-3 text-center mb-3 mb-md-0">
            <i class="fa-solid fa-shield-check text-gold fs-2 mb-2"></i>
            <h6 class="fw-bold mb-1">100% Authentic</h6>
            <small class="text-muted">Guaranteed quality products</small>
        </div>
        <div class="col-6 col-md-3 text-center mb-3 mb-md-0">
            <i class="fa-solid fa-rotate-left text-gold fs-2 mb-2"></i>
            <h6 class="fw-bold mb-1">Easy Return</h6>
            <small class="text-muted">Hassle-free return policy</small>
        </div>
        <div class="col-6 col-md-3 text-center mb-3 mb-md-0">
            <i class="fa-solid fa-headset text-gold fs-2 mb-2"></i>
            <h6 class="fw-bold mb-1">24/7 Support</h6>
            <small class="text-muted">Dedicated customer helpline</small>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timerElem = document.getElementById('ganiOfferTimer');
        if (timerElem) {
            const endTimeStr = timerElem.getAttribute('data-endtime');
            const endTime = new Date(endTimeStr.replace(/-/g, "/")).getTime();

            function updateTimer() {
                const now = new Date().getTime();
                const diff = endTime - now;

                if (diff <= 0) {
                    document.getElementById('timerDays').innerText = '00';
                    document.getElementById('timerHours').innerText = '00';
                    document.getElementById('timerMinutes').innerText = '00';
                    document.getElementById('timerSeconds').innerText = '00';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                document.getElementById('timerDays').innerText = String(days).padStart(2, '0');
                document.getElementById('timerHours').innerText = String(hours).padStart(2, '0');
                document.getElementById('timerMinutes').innerText = String(minutes).padStart(2, '0');
                document.getElementById('timerSeconds').innerText = String(seconds).padStart(2, '0');
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        }
    });
</script>
@endpush