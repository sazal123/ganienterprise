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
@endpush 

@push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/owl.carousel.min.css') }}" />
<link rel="stylesheet" href="{{ asset('frontEnd/css/owl.theme.default.min.css') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css" rel="stylesheet" />
<style>
    .hero-banner { position: relative; overflow: hidden; }
    .hero-banner img { width: 100%; height: auto; display: block; }
    .hero-section-wrapper { padding: 0; }
    .section-title-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 0; }
    .section-title-name { font-size: 24px; font-weight: 600; color: #1c1c1c; text-transform: uppercase; letter-spacing: 0.1em; }
    .patchee-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; padding: 20px 0; }
    .category-item { text-align: center; padding: 10px; }
    .category-item img { width: 100%; border-radius: 8px; transition: transform 0.3s ease; }
    .category-item img:hover { transform: scale(1.05); }
    .category-item a { text-decoration: none; color: #1c1c1c; font-size: 14px; margin-top: 8px; display: block; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; padding: 20px 0; }
    .product-card { background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; transition: all 0.3s; }
    .product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .product-image { position: relative; width: 100%; padding-bottom: 100%; overflow: hidden; }
    .product-image img { position: absolute; width: 100%; height: 100%; object-fit: cover; }
    .product-discount { position: absolute; top: 10px; right: 10px; background: #c9a66b; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; z-index: 1; }
    .product-info { padding: 15px; }
    .product-name { font-size: 14px; font-weight: 500; margin-bottom: 8px; }
    .product-name a { color: #1c1c1c; text-decoration: none; }
    .product-price { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
    .product-price .new-price { color: #c9a66b; font-weight: 600; font-size: 16px; }
    .product-price .old-price { color: #999; text-decoration: line-through; font-size: 12px; }
    .product-btn { width: 100%; padding: 10px; background: #1c1c1c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; text-transform: uppercase; font-weight: 600; }
    .product-btn:hover { background: #c9a66b; }
    .timer { font-size: 14px; color: #c9a66b; font-weight: 600; }
    .section-divider { margin: 40px 0; }
    .cta-banner { background: linear-gradient(135deg, #c9a66b 0%, #c9a66b 100%); color: white; padding: 40px 20px; text-align: center; border-radius: 8px; margin: 30px 0; }
    .cta-banner h2 { font-size: 28px; margin-bottom: 15px; }
    .cta-button { display: inline-block; background: white; color: #c9a66b; padding: 12px 30px; border-radius: 4px; text-decoration: none; font-weight: 600; text-transform: uppercase; transition: all 0.3s; }
    .cta-button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
</style>
@endpush 

@section('content')
<!-- Hero Banner Section -->
<section class="hero-section-wrapper">
    <div class="hero-banner">
        <div class="main_slider owl-carousel">
            @foreach ($sliders as $key => $value)
                <div class="slider-item">
                    <img src="{{ asset($value->image) }}" alt="Slider" />
                </div>
            @endforeach
        </div>
    </div>
</section>


<!-- Top Categories Section -->
<section class="homeproduct">
    <div class="container">
        <div class="sec_title">
            <h3 class="section-title-header">
                <span class="section-title-name">Featured Categories</span>
            </h3>
        </div>

        <div class="patchee-grid">
            @foreach ($frontcategory as $category)
                <div class="category-item">
                    <a href="{{ route('category', $category->slug) }}">
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" />
                        <span>{{ $category->name }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>





@php
    $hotDealEndDate = $generalsetting->hot_deal_end_date.'T23:59:59';
    $flashSaleEndDate = $generalsetting->flash_sale_end_date.'T23:59:59';
    $isHotDealActive = $hotDealEndDate && \Carbon\Carbon::parse($hotDealEndDate)->isFuture(); // Check if the date is in the future
    $isFlashSaleActive = $flashSaleEndDate && \Carbon\Carbon::parse($flashSaleEndDate)->isFuture(); 
@endphp
<!--//Flash sales-->
@if($isFlashSaleActive)
<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <div class="timer_inner">
                            <div class="">
                                <span class="section-title-name">Flash Sales </span>
                            </div>

                            <div class="">
                                <div class="offer_timer" id="flash_sale_timer"></div>
                            </div>
                        </div>
                    </h3>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="flash_sale_slider owl-carousel">
                    @foreach ($flas_sales as $key => $value)
                        <div class="product_item wist_item">
                            <div class="product_item_inner">
                                @if($value->old_price)
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{ number_format($discount, 0) }}%</p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="pro_img">
                                    <a href="{{ route('product', $value->slug) }}">
                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                            alt="{{ $value->name }}" />
                                    </a>
                                    @if($value->stock < 1)
                                    <div class="stock-out-overlay">STOCK OUT</div>
                                    @endif
                                </div>
                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
                                    </div>
                                    
                                    <span style="background-color:#FFBCA5" class="px-3 py-1 rounded-pill">Sold {{$value->sold??0}}</span>
                                  
                                    <div class="pro_price">
                                        <p>
                                            @if ($value->old_price)
                                             <del>৳ {{ $value->old_price }}</del>
                                            @endif

                                            ৳ {{ $value->new_price }} 
                                           
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1))
                                <div class="pro_btn">
                                   
                                    <div class="cart_btn order_button">
                                        <a href="{{ route('product', $value->slug) }}"
                                            class="addcartbutton">অর্ডার করুন</a>
                                    </div>
                                </div>
                            @else
                                <div class="pro_btn">
                                    
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit">অর্ডার করুন</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-12">
               <a href="{{ route('flashsales') }}" class="view_more_btn" style="float:left">View More</a> 
            </div>
        </div>
    </div>
</section>
@endif
<!--//hot deals-->
@if($isHotDealActive)
<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <div class="timer_inner">
                            <div class="">
                                <span class="section-title-name">Hot Deal </span>
                            </div>

                            <div class="">
                                <div class="offer_timer" id="simple_timer"></div>
                            </div>
                        </div>
                    </h3>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="product_slider owl-carousel">
                    @foreach ($hotdeal_top as $key => $value)
                        <div class="product_item wist_item">
                            <div class="product_item_inner">
                                @if($value->old_price)
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{ number_format($discount, 0) }}%</p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="pro_img">
                                    <a href="{{ route('product', $value->slug) }}">
                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                            alt="{{ $value->name }}" />
                                    </a>
                                    @if($value->stock < 1)
                                    <div class="stock-out-overlay">STOCK OUT</div>
                                    @endif
                                </div>
                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a
                                            href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
                                    </div>
                                    <div class="pro_price">
                                        <p>
                                            @if ($value->old_price)
                                             <del>৳ {{ $value->old_price }}</del>
                                            @endif

                                            ৳ {{ $value->new_price }} 
                                           
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1))
                                <div class="pro_btn">
                                   
                                    <div class="cart_btn order_button">
                                        <a href="{{ route('product', $value->slug) }}"
                                            class="addcartbutton">অর্ডার করুন</a>
                                    </div>
                                </div>
                            @else
                                <div class="pro_btn">
                                    
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit">অর্ডার করুন</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-12">
               <a href="{{ route('hotdeals') }}" class="view_more_btn" style="float:left">View More</a> 
            </div>
        </div>
    </div>
</section>
@endif




@if($generalsetting->show_all_products)
<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <div class="timer_inner">
                            <div class="">
                                <span class="section-title-name">All Products</span>
                            </div>
                        </div>
                    </h3>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="category-product main_product_inner">
                    @foreach($all_products as $key=>$value)
                    <div class="product_item wist_item">
                        <div class="product_item_inner">
                             @if($value->old_price)
                            <div class="sale-badge">
                                <div class="sale-badge-inner">
                                    <div class="sale-badge-box">
                                        <span class="sale-badge-text">
                                           <p> @php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{number_format($discount,0)}}%</p>
                                            ছাড়
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="pro_img">
                                <a href="{{ route('product',$value->slug) }}">
                                    <img src="{{ asset($value->image ? $value->image->image : '') }}" alt="{{$value->name}}" />
                                </a>
                                @if($value->stock < 1)
                                <div class="stock-out-overlay">STOCK OUT</div>
                                @endif
                            </div>
                            <div class="pro_des">
                                <div class="pro_name">
                                    <a href="{{ route('product',$value->slug) }}">{{Str::limit($value->name,80)}}</a>
                                </div>
                                <div class="pro_price">
                                    <p>
                                        <del>৳ {{ $value->old_price}}</del>
                                        ৳ {{ $value->new_price}} @if($value->old_price) @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                         @if(! $value->prosizes->isEmpty() || ! $value->procolors->isEmpty() || ($value->stock < 1))
                        <div class="pro_btn">
                            
                            <div class="cart_btn order_button">
                                <a href="{{ route('product',$value->slug) }}" class="addcartbutton">অর্ডার করুন</a>
                            </div>
                            
                        </div>
                        @else

                        <div class="pro_btn">
                           
                            <form action="{{route('cart.store')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$value->id}}" />
                                <input type="hidden" name="qty" value="1" />
                                <button type="submit">অর্ডার করুন</button>
                            </form>
                        </div>
                        @endif
                        
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
@endif

@if($sliderbottomads)
<section class="mt-2">
    <div class="container">
        <div class="row">
            @foreach($sliderbottomads as $bottomAds)
            <div class="col-md-12">
                <a href="{{$bottomAds->link}}?sold=show">
                    <img class="img-fluid" src="{{$bottomAds->image}}"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif




@if($generalsetting->show_category_wise_products)
    @foreach ($homeproducts as $homecat)
        <section class="homeproduct">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="sec_title">
                            <h3 class="section-title-header">
                                <span class="section-title-name">{{ $homecat->name }}</span>
                                
                            </h3>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="product_sliders">
                            @foreach ($homecat->products as $key => $value)
                               <div class="product_item wist_item">
                                <div class="product_item_inner">
                                    @if($value->old_price)
                                    <div class="sale-badge">
                                        <div class="sale-badge-inner">
                                            <div class="sale-badge-box">
                                                <span class="sale-badge-text">
                                                    <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{ number_format($discount, 0) }}%</p>
                                                    ছাড়
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="pro_img">
                                        <a href="{{ route('product', $value->slug) }}">
                                            <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                                alt="{{ $value->name }}" />
                                        </a>
                                        @if($value->stock < 1)
                                        <div class="stock-out-overlay">STOCK OUT</div>
                                        @endif
                                    </div>
                                    <div class="pro_des">
                                        <div class="pro_name">
                                            <a
                                                href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
                                        </div>
                                        <div class="pro_price">
                                            <p>
                                                @if ($value->old_price)
                                                 <del>৳ {{ $value->old_price }}</del>
                                                @endif
    
                                                ৳ {{ $value->new_price }} 
                                               
                                            </p>
                                        </div>
                                    </div>
                                </div>
    
                                @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty() || ($value->stock < 1))
                                    <div class="pro_btn">
                                       
                                        <div class="cart_btn order_button">
                                            <a href="{{ route('product', $value->slug) }}"
                                                class="addcartbutton">অর্ডার করুন</a>
                                        </div>
                                    </div>
                                @else
                                    <div class="pro_btn">
                                        
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}" />
                                            <input type="hidden" name="qty" value="1" />
                                            <button type="submit">অর্ডার করুন</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="show_more_btn">
                            <a href="{{ route('category', $homecat->slug) }}" class="view_more_btn">View More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endforeach
@endif
@if($campaognads)
<section>
    <div class="container">
        <div class="row">
            @foreach($campaognads as $campaignAds)
            <div class="col-12">
                <a href="{{$campaignAds->link}}?sold=show">
                    <img class="img-fluid" src="{{$campaignAds->image}}"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


@if($reviews->count()>0)
<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h5 class="text-center text-light py-2" style="background-color:#3c7d17">
                        সম্মানীত কাষ্টমারদের পজিটিভ রিভিউ
                    </h5>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="customer-review owl-carousel">
                    @foreach ($reviews as $review)
                    <div class="border rounded">
                        <img class="img-fluid w-100" src="{{ asset($review->image) }}" />
                    </div>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
</section>
@endif
<section>
    <div class="container">
        <div class="row">
            @foreach($footertopads as $footerAds)
            <div class="col-md-12">
                <a href="{{$footerAds->link}}?sold=show">
                    <img class="img-fluid w-100" src="{{$footerAds->image}}"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection @push('script')
<script src="{{ asset('frontEnd/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('frontEnd/js/jquery.syotimer.min.js') }}"></script>

<script>
    $(document).ready(function() {
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

            navText: ["<i class='fa-solid fa-angle-left'></i>",
                "<i class='fa-solid fa-angle-right'></i>"
            ],
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".hotdeals-slider").owlCarousel({
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
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 6,
                    nav: true,
                    loop: false,
                },
            },
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".category-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 5,
                    nav: true,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 8,
                    nav: true,
                    loop: false,
                },
            },
        });

        $(".product_slider").owlCarousel({
            margin: 15,
            items: 6,
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
                    items: 5,
                    nav: false,
                },
                1000: {
                    items: 6,
                    nav: false,
                },
            },
        });
        
        $(".flash_sale_slider").owlCarousel({
            margin: 8,
            items: 6,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: false,
                },
                600: {
                    items: 6,
                    nav: false,
                },
                1000: {
                    items: 7,
                    nav: false,
                },
            },
        });
        
        $(".category-sliger").owlCarousel({
            margin: 8,
            items: 6,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: false,
                },
                600: {
                    items: 6,
                    nav: false,
                },
                1000: {
                    items: 7,
                    nav: false,
                },
            },
        });
        $(".customer-review").owlCarousel({
            margin: 8,
            items: 6,
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
</script>

<script>
    $("#simple_timer").syotimer({
        date: new Date("{{$generalsetting->hot_deal_end_date}}T23:59:59"), // November is month 10 (0-indexed)
        layout: "hms", // Hours, minutes, seconds
        doubleNumbers: false, // No leading zeros
        effectType: "opacity", // Opacity effect when changing numbers
        periodUnit: "d", // Period unit set to days
        periodic: false // Countdown only, no reset
    });
   $("#flash_sale_timer").syotimer({
        date: new Date("{{$generalsetting->flash_sale_end_date}}T23:59:59"), // Use the date from your Laravel model
        layout: "hms", // Hours, minutes, seconds
        doubleNumbers: false, // No leading zeros
        effectType: "opacity", // Opacity effect when changing numbers
        periodUnit: "d", // Period unit set to days
        periodic: false, // Countdown only, no reset
    });
</script>
@endpush
