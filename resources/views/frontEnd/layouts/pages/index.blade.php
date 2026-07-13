@extends('frontEnd.layouts.master')
@section('title', 'Home')
@push('seo')

<meta name="description" content="{!! $generalsetting->meta_description !!}" />
<meta name="keyword" content="{!! $generalsetting->meta_keyword !!}" />

		<!-- Open Graph data -->
<meta property="og:title" content="{{$generalsetting->name}}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ URL::to('/') }}" />
<meta property="og:image" content="{{asset($generalsetting->og_baner)}}" />
<meta property="og:description" content="{!! $generalsetting->meta_description !!}" />
@endpush @push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/owl.carousel.min.css') }}" />
<link rel="stylesheet" href="{{ asset('frontEnd/css/owl.theme.default.min.css') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css" rel="stylesheet" />
@endpush @section('content')

<!-- Main Slider Section -->
<section class="hero-slider-section">
    <div class="main_slider owl-carousel">
        @foreach ($sliders as $key => $value)
            <div class="slider-item">
                <img src="{{ asset($value->image) }}" alt="" />
            </div>
        @endforeach
    </div>
</section>
<!-- slider end -->

<!-- Top Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="section-title mb-4">Top Categories</h3>
            </div>
            <div class="col-12">
                <div class="category-carousel owl-carousel">
                    @foreach ($menucategories as $key => $value)
                        <div class="category-item">
                            <a href="{{ route('category', $value->slug) }}" class="category-link">
                                <img src="{{ asset($value->image) }}" alt="{{ $value->name }}" class="category-img" />
                                <p class="category-name">{{ $value->name }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $hotDealEndDate = $generalsetting->hot_deal_end_date.'T23:59:59';
    $flashSaleEndDate = $generalsetting->flash_sale_end_date.'T23:59:59';
    $isHotDealActive = $hotDealEndDate && \Carbon\Carbon::parse($hotDealEndDate)->isFuture();
    $isFlashSaleActive = $flashSaleEndDate && \Carbon\Carbon::parse($flashSaleEndDate)->isFuture();
@endphp

<!-- Flash Sales Section -->
@if($isFlashSaleActive)
<section class="flash-sales-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header-wrapper">
                    <h2 class="section-title">Flash Sales</h2>
                    <div class="offer_timer" id="flash_sale_timer"></div>
                </div>
            </div>
            <div class="col-12">
                <div class="products-grid">
                    @foreach ($flas_sales as $key => $value)
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                @if($value->old_price)
                                <div class="discount-badge">
                                    @php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp
                                    {{ number_format($discount, 0) }}%
                                </div>
                                @endif
                                <a href="{{ route('product', $value->slug) }}" class="product-img-link">
                                    <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                        alt="{{ $value->name }}" class="product-image" />
                                </a>
                                @if($value->stock < 1)
                                <div class="stock-out-overlay">STOCK OUT</div>
                                @endif
                            </div>
                            <div class="product-info">
                                <h5 class="product-name">
                                    <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 60) }}</a>
                                </h5>
                                <p class="product-sold">Sold {{$value->sold??0}}</p>
                                <div class="product-price">
                                    @if ($value->old_price)
                                     <span class="old-price">৳ {{ $value->old_price }}</span>
                                    @endif
                                    <span class="new-price">৳ {{ $value->new_price }}</span>
                                </div>
                            </div>
                            <div class="product-action">
                                @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1))
                                    <a href="{{ route('product', $value->slug) }}" class="btn-order">অর্ডার করুন</a>
                                @else
                                    <form action="{{ route('cart.store') }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-12 text-center mt-4">
               <a href="{{ route('flashsales') }}" class="btn-view-more">View More</a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Hot Deals Section -->
@if($isHotDealActive)
<section class="hot-deals-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header-wrapper">
                    <h2 class="section-title">Hot Deals</h2>
                    <div class="offer_timer" id="simple_timer"></div>
                </div>
            </div>
            <div class="col-12">
                <div class="products-grid">
                    @foreach ($hotdeal_top as $key => $value)
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                @if($value->old_price)
                                <div class="discount-badge">
                                    @php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp
                                    {{ number_format($discount, 0) }}%
                                </div>
                                @endif
                                <a href="{{ route('product', $value->slug) }}" class="product-img-link">
                                    <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                        alt="{{ $value->name }}" class="product-image" />
                                </a>
                                @if($value->stock < 1)
                                <div class="stock-out-overlay">STOCK OUT</div>
                                @endif
                            </div>
                            <div class="product-info">
                                <h5 class="product-name">
                                    <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 60) }}</a>
                                </h5>
                                <div class="product-price">
                                    @if ($value->old_price)
                                     <span class="old-price">৳ {{ $value->old_price }}</span>
                                    @endif
                                    <span class="new-price">৳ {{ $value->new_price }}</span>
                                </div>
                            </div>
                            <div class="product-action">
                                @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1))
                                    <a href="{{ route('product', $value->slug) }}" class="btn-order">অর্ডার করুন</a>
                                @else
                                    <form action="{{ route('cart.store') }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-12 text-center mt-4">
               <a href="{{ route('hotdeals') }}" class="btn-view-more">View More</a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Featured Ads -->
@if($sliderbottomads)
<section class="ads-section py-5">
    <div class="container">
        <div class="row">
            @foreach($sliderbottomads as $bottomAds)
            <div class="col-12 mb-4">
                <a href="{{$bottomAds->link}}?sold=show" class="ads-link">
                    <img class="img-fluid w-100" src="{{$bottomAds->image}}" alt="Advertisement"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- All Products Section -->
@if($generalsetting->show_all_products)
<section class="all-products-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title mb-4">All Products</h2>
            </div>
            <div class="col-12">
                <div class="products-grid">
                    @foreach($all_products as $key=>$value)
                    <div class="product-card">
                        <div class="product-image-wrapper">
                             @if($value->old_price)
                            <div class="discount-badge">
                               @php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp
                                {{ number_format($discount,0) }}%
                            </div>
                            @endif
                            <a href="{{ route('product',$value->slug) }}" class="product-img-link">
                                <img src="{{ asset($value->image ? $value->image->image : '') }}" alt="{{$value->name}}" class="product-image" />
                            </a>
                            @if($value->stock < 1)
                            <div class="stock-out-overlay">STOCK OUT</div>
                            @endif
                        </div>
                        <div class="product-info">
                            <h5 class="product-name">
                                <a href="{{ route('product',$value->slug) }}">{{Str::limit($value->name,60)}}</a>
                            </h5>
                            <div class="product-price">
                                @if($value->old_price)
                                <span class="old-price">৳ {{ $value->old_price}}</span>
                                @endif
                                <span class="new-price">৳ {{ $value->new_price}}</span>
                            </div>
                        </div>
                        <div class="product-action">
                             @if(! $value->prosizes->isEmpty() || ! $value->procolors->isEmpty() || ($value->stock < 1))
                            <a href="{{ route('product',$value->slug) }}" class="btn-order">অর্ডার করুন</a>
                            @else
                            <form action="{{route('cart.store')}}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="id" value="{{$value->id}}" />
                                <input type="hidden" name="qty" value="1" />
                                <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Category-wise Products -->
@if($generalsetting->show_category_wise_products)
    @foreach ($homeproducts as $homecat)
        <section class="category-products-section py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="section-title mb-4">{{ $homecat->name }}</h2>
                    </div>
                    <div class="col-12">
                        <div class="products-grid">
                            @foreach ($homecat->products as $key => $value)
                               <div class="product-card">
                                <div class="product-image-wrapper">
                                    @if($value->old_price)
                                    <div class="discount-badge">
                                        @php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp
                                        {{ number_format($discount, 0) }}%
                                    </div>
                                    @endif
                                    <a href="{{ route('product', $value->slug) }}" class="product-img-link">
                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                            alt="{{ $value->name }}" class="product-image" />
                                    </a>
                                    @if($value->stock < 1)
                                    <div class="stock-out-overlay">STOCK OUT</div>
                                    @endif
                                </div>
                                <div class="product-info">
                                    <h5 class="product-name">
                                        <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 60) }}</a>
                                    </h5>
                                    <div class="product-price">
                                        @if ($value->old_price)
                                         <span class="old-price">৳ {{ $value->old_price }}</span>
                                        @endif
                                        <span class="new-price">৳ {{ $value->new_price }}</span>
                                    </div>
                                </div>
                                <div class="product-action">
                                    @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1))
                                        <a href="{{ route('product', $value->slug) }}" class="btn-order">অর্ডার করুন</a>
                                    @else
                                        <form action="{{ route('cart.store') }}" method="POST" class="w-100">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}" />
                                            <input type="hidden" name="qty" value="1" />
                                            <button type="submit" class="btn-order w-100">অর্ডার করুন</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <a href="{{ route('category', $homecat->slug) }}" class="btn-view-more">View More</a>
                    </div>
                </div>
            </div>
        </section>
    @endforeach
@endif

<!-- Campaign Ads -->
@if($campaognads)
<section class="campaign-ads-section py-5">
    <div class="container">
        <div class="row">
            @foreach($campaognads as $campaignAds)
            <div class="col-12 mb-4">
                <a href="{{$campaignAds->link}}?sold=show" class="ads-link">
                    <img class="img-fluid w-100" src="{{$campaignAds->image}}" alt="Campaign"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Customer Reviews Section -->
@if($reviews->count()>0)
<section class="reviews-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">সম্মানীত কাষ্টমারদের পজিটিভ রিভিউ</h2>
            </div>
            <div class="col-12">
                <div class="reviews-carousel owl-carousel">
                    @foreach ($reviews as $review)
                    <div class="review-item">
                        <img class="img-fluid w-100" src="{{ asset($review->image) }}" alt="Customer Review"/>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Footer Ads -->
<section class="footer-ads-section py-5">
    <div class="container">
        <div class="row">
            @foreach($footertopads as $footerAds)
            <div class="col-12 mb-4">
                <a href="{{$footerAds->link}}?sold=show" class="ads-link">
                    <img class="img-fluid w-100" src="{{$footerAds->image}}" alt="Footer Advertisement"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('script')
<script src="{{ asset('frontEnd/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('frontEnd/js/jquery.syotimer.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Main slider
        $(".main_slider").owlCarousel({
            items: 1,
            loop: true,
            dots: false,
            autoplay: true,
            nav: true,
            autoplayHoverPause: true,
            margin: 0,
            mouseDrag: true,
            smartSpeed: 8000,
            autoplayTimeout: 3000,
            animateOut: "fadeOutRight",
            animateIn: "slideInLeft",
            navText: ["<i class='fa-solid fa-angle-left'></i>", "<i class='fa-solid fa-angle-right'></i>"],
        });

        // Category carousel
        $(".category-carousel").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true,
                },
                600: {
                    items: 4,
                    nav: false,
                },
                1000: {
                    items: 7,
                    nav: true,
                },
            },
        });

        // Reviews carousel
        $(".reviews-carousel").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: false,
                },
            },
        });
    });

    // Flash sale timer
    $("#flash_sale_timer").syotimer({
        date: new Date("{{$generalsetting->flash_sale_end_date}}T23:59:59"),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: false,
    });

    // Hot deal timer
    $("#simple_timer").syotimer({
        date: new Date("{{$generalsetting->hot_deal_end_date}}T23:59:59"),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: false,
    });
</script>
@endpush
