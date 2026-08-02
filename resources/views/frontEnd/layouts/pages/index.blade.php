@extends('frontEnd.layouts.master')
@section('title', 'Home')
@push('seo')
<meta name="description" content="{!! $generalsetting->meta_description !!}" />
<meta name="keyword" content="{!! $generalsetting->meta_keyword !!}" />
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO SLIDER — Folks-style banner carousel (manageable from admin) --}}
{{-- ============================================================ --}}
<section class="gani-hero-slider" id="ganiHeroSlider">
    @forelse($sliders as $index => $slider)
    @php
        // Strip 'public/' prefix from image path for asset()
        $sliderImage = $slider->image;
        if (str_starts_with($sliderImage, 'public/')) {
            $sliderImage = substr($sliderImage, 7);
        }
        $subtitle = $slider->subtitle ?: 'গ্রীষ্মকালীন অফার';
        $title = $slider->title ?: '৮০%';
        $btnText = $slider->btn_text ?: 'অফার উপভোগ করুন';
    @endphp
    <div class="gani-hero-slide {{ $index === 0 ? 'active' : '' }}"
         style="background-image: url('{{ asset($sliderImage) }}');">
        <div class="gani-hero-gradient"></div>
        <div class="gani-hero-content">
            <h2 class="gani-hero-subtitle">{!! nl2br(e($subtitle)) !!}</h2>
            <h1 class="gani-hero-title">{{ e($title) }} <span class="gani-hero-title-accent">OFF</span></h1>
            <a href="{{ $slider->link ?? route('shop') }}" class="gani-hero-btn">{{ $btnText }}</a>
        </div>
    </div>
    @empty
    <div class="gani-hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1548036328-c9fa89d128fa?q=80&w=2069&auto=format&fit=crop');">
        <div class="gani-hero-gradient"></div>
        <div class="gani-hero-content">
            <h2 class="gani-hero-subtitle">Summer<br>Offer</h2>
            <h1 class="gani-hero-title">80% <span class="gani-hero-title-accent">OFF</span></h1>
            <a href="{{ route('shop') }}" class="gani-hero-btn">Enjoy Offer</a>
        </div>
    </div>
    @endforelse

    {{-- Slider dots --}}
    @if($sliders->count() > 1)
    <div class="gani-slider-dots">
        @foreach($sliders as $index => $slider)
        <button class="gani-slider-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></button>
        @endforeach
    </div>
    @endif
</section>

{{-- ============================================================ --}}
{{-- SLIDING NOTICE BAR — dynamic from admin --}}
{{-- ============================================================ --}}
@if($notices->count() > 0)
<section class="gani-notice-bar">
    <div class="gani-notice-track" id="ganiNoticeTrack">
        {{-- Duplicate notices for seamless infinite scroll --}}
        @for($r = 0; $r < 3; $r++)
            @foreach($notices as $notice)
                @if($notice->link)
                    <a href="{{ $notice->link }}" class="gani-notice-item" target="_blank">{{ $notice->text }}</a>
                @else
                    <span class="gani-notice-item">{{ $notice->text }}</span>
                @endif
            @endforeach
        @endfor
    </div>
</section>
@endif



{{-- ============================================================ --}}
{{-- TRENDING NOW SECTION — 4-column product grid --}}
{{-- ============================================================ --}}
@if($trendingProducts->count() > 0)
<section class="gani-section gani-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">Trending Collection</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            @foreach($trendingProducts as $product)
                <div class="col-6 col-md-g5">
                    @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif



{{-- ============================================================ --}}
{{-- PRIME / NEW COLLECTION — Tabbed product grid --}}
{{-- ============================================================ --}}
@if($primeProducts->count() > 0 || $newProducts->count() > 0)
<section class="gani-section gani-section-white">
    <div class="container">
        {{-- Tabs --}}
        <div class="gani-collection-tabs">
            <button class="gani-collection-tab active" data-tab="prime">
                PRIME BAGS
            </button>
            <button class="gani-collection-tab" data-tab="new">
                NEW IN TREND
            </button>
        </div>

        {{-- Prime Products --}}
        <div class="gani-collection-panel active" id="panel-prime">
            @if($primeProducts->count() > 0)
            <div class="row g-4">
                @foreach($primeProducts as $product)
                <div class="col-6 col-md-g5">
                    @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('collection.prime') }}" class="gani-view-all-btn">VIEW ALL</a>
            </div>
            @else
            <p class="text-center text-muted py-4">কোনো প্রাইম পণ্য পাওয়া যায়নি</p>
            @endif
        </div>

        {{-- New Products --}}
        <div class="gani-collection-panel" id="panel-new">
            @if($newProducts->count() > 0)
            <div class="row g-4">
                @foreach($newProducts as $product)
                <div class="col-6 col-md-g5">
                    @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('collection.new') }}" class="gani-view-all-btn">VIEW ALL</a>
            </div>
            @else
            <p class="text-center text-muted py-4">কোনো নতুন পণ্য পাওয়া যায়নি</p>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- STORIES THAT LEAD — Short video reels from admin --}}
{{-- ============================================================ --}}
@if($stories->count() > 0)
<section class="gani-section gani-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">STORIES THAT LEAD</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            @foreach($stories as $story)
            @php
                $thumb = $story->thumbnail;
                if ($thumb && str_starts_with($thumb, 'public/')) { $thumb = substr($thumb, 7); }
                $storyProduct = $story->product;
                $prodImg = $storyProduct && $storyProduct->image ? $storyProduct->image->image : '';
            @endphp
            <div class="col-6 col-md-g5">
                <div class="gani-story-card"
                     data-video="{{ asset($story->video) }}"
                     data-thumb="{{ $thumb ? asset($thumb) : asset('frontEnd/img/default-product.jpg') }}"
                     data-prod-img="{{ $prodImg ? asset($prodImg) : '' }}"
                     data-prod-name="{{ $storyProduct ? $storyProduct->name : '' }}"
                     data-prod-price="{{ $storyProduct ? number_format($storyProduct->new_price) : '0' }}"
                     data-prod-old="{{ $storyProduct && $storyProduct->old_price ? number_format($storyProduct->old_price) : '' }}"
                     data-prod-slug="{{ $storyProduct ? $storyProduct->slug : '#' }}"
                     data-prod-id="{{ $storyProduct ? $storyProduct->id : '' }}"
                     data-prod-stock="{{ $storyProduct ? $storyProduct->stock : 0 }}"
                     data-prod-link="{{ $storyProduct ? route('product', $storyProduct->slug) : '#' }}"
                     data-add-to-cart="{{ $storyProduct && $storyProduct->procolors->isEmpty() && $storyProduct->prosizes->isEmpty() && $storyProduct->stock > 0 ? route('cart.store') : '' }}">
                    <div class="gani-story-thumb-wrap">
                        <video src="{{ asset($story->video) }}" class="gani-story-video" muted playsinline loop preload="auto" poster="{{ $thumb ? asset($thumb) : asset('frontEnd/img/default-product.jpg') }}"></video>
                        <div class="gani-story-play-indicator"><i class="fa-solid fa-volume-xmark"></i></div>
                    </div>
                    <div class="gani-story-info">
                        @if($storyProduct)
                            <div class="gani-story-product-row">
                                @if($prodImg)
                                <img src="{{ asset($prodImg) }}" class="gani-story-prod-img" />
                                @endif
                                <div class="gani-story-prod-details">
                                    <h6 class="gani-story-prod-name">{{ Str::limit($storyProduct->name, 35) }}</h6>
                                    <span class="gani-story-prod-price">৳{{ number_format($storyProduct->new_price) }}</span>
                                </div>
                            </div>
                            @if($storyProduct->procolors->isEmpty() && $storyProduct->prosizes->isEmpty() && $storyProduct->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $storyProduct->id }}" />
                                <input type="hidden" name="qty" value="1" />
                                <button type="submit" class="gani-story-cart-btn">Add To Cart</button>
                            </form>
                            @else
                            <a href="{{ route('product', $storyProduct->slug) }}" class="gani-story-cart-btn">Add To Cart</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- STORY PRODUCT MODAL — Quick view with video --}}
{{-- ============================================================ --}}
<div class="gani-video-modal" id="ganiStoryModal">
    <button class="gani-video-close" onclick="closeStoryModal()">&times;</button>
    <div class="gani-story-modal-inner">
        <div class="gani-story-modal-left">
            <video id="ganiStoryVideo" controls playsinline></video>
        </div>
        <div class="gani-story-modal-right">
            <h3 class="gani-sm-title" id="ganiSmTitle">Product Name</h3>
            <div class="gani-sm-price-row">
                <span class="gani-sm-price" id="ganiSmPrice">৳0</span>
                <span class="gani-sm-old-price" id="ganiSmOldPrice"></span>
            </div>
            <div class="gani-sm-colors" id="ganiSmColors">
                <span class="gani-sm-color-label">Color: <strong id="ganiSmColorName">Brown</strong></span>
                <div class="gani-sm-color-swatches" id="ganiSmColorSwatches"></div>
            </div>
            <form id="ganiSmCartForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="ganiSmProdId" value="" />
                <input type="hidden" name="qty" value="1" />
                <button type="submit" class="gani-sm-cart-btn" id="ganiSmCartBtn">Add To Cart</button>
            </form>
            <a href="#" class="gani-sm-view-link" id="ganiSmViewLink">View Full Details →</a>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- PRIME DROP BANNER — Manageable from admin (Category: Prime Drop Banner) --}}
{{-- ============================================================ --}}
@if($primeDropBanner)
@php
    $pdbImg = $primeDropBanner->image;
    if (str_starts_with($pdbImg, 'public/')) { $pdbImg = substr($pdbImg, 7); }
    $pdbTitle = $primeDropBanner->title ?: 'PATCHEE TOP PICKS';
    $pdbSubtitle = $primeDropBanner->subtitle ?: 'THE PRIME DROP';
    $pdbBtnText = $primeDropBanner->btn_text ?: 'View All';
    $pdbLink = $primeDropBanner->link ?: route('shop');
@endphp
<section class="gani-prime-drop" @if($pdbImg) style="background-image: url('{{ asset($pdbImg) }}');" @endif>
    <div class="gani-prime-drop-overlay"></div>
    <div class="container position-relative gani-prime-drop-inner">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="gani-pd-badge">UPTO 50% OFF</span>
                <h3 class="gani-pd-title">{{ $pdbTitle }}</h3>
                <h2 class="gani-pd-subtitle">{{ $pdbSubtitle }}</h2>
                <a href="{{ $pdbLink }}" class="gani-pd-btn">{{ $pdbBtnText }}</a>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end gap-3">
                @foreach($primeDropProducts as $pdp)
                    <a href="{{ route('product', $pdp->slug) }}" class="gani-pd-product">
                        <img src="{{ asset($pdp->image ? $pdp->image->image : 'frontEnd/img/default-product.jpg') }}"
                             alt="{{ $pdp->name }}" class="gani-pd-product-img" />
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- SHOP BY CATEGORY — Attractive 5-column grid --}}
{{-- ============================================================ --}}
@if($homeCategories->count() > 0)
<section class="gani-section gani-section-white category-wise">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">Trending Categories</h2>
            <div class="gani-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            @foreach($homeCategories->take(5) as $category)
            @php
                $catImg = $category->image;
                if ($catImg && str_starts_with($catImg, 'public/')) {
                    $catImg = substr($catImg, 7);
                }
            @endphp
            <div class="col-6 col-md-g5">
                <a href="{{ route('category', $category->slug) }}" class="gani-cat-card">
                    <div class="gani-cat-img-wrap">
                        @if($category->image)
                            <img src="{{ asset($catImg) }}" alt="{{ $category->name }}" class="w-100 h-100 object-fit-cover gani-cat-img" />
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #C9A84C, #8B6914);">
                                <span class="text-white fw-bold" style="font-size:32px; font-family:'Playfair Display',serif;">{{ mb_substr($category->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="gani-cat-overlay">
                            <span class="gani-cat-shop-btn">শপিং করুন</span>
                        </div>
                    </div>
                    <h5 class="gani-cat-name">{{ $category->name }}</h5>
                </a>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('shop') }}" class="gani-view-all-btn">View All</a>
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- SCHOOL BAG & LADIES BAG TABBED SECTION --}}
{{-- ============================================================ --}}
<style>
.bag-section-wrapper {
    background: #ffffff;
    padding: 50px 0 60px;
}
.bag-tab-nav-bar {
    position: relative;
    border-bottom: 1px solid #eaeaea;
    margin-bottom: 40px;
}
.bag-tab-nav {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    margin: 0 auto;
}
.bag-tab-btn {
    background: none;
    border: none;
    font-family: 'Syne', sans-serif;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #999999;
    padding: 12px 16px;
    position: relative;
    cursor: pointer;
    transition: all 0.25s ease;
}
.bag-tab-btn:hover {
    color: #111111;
}
.bag-tab-btn.active {
    color: #111111;
}
.bag-tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #C9A84C;
}
</style>

<section class="bag-section-wrapper">
    {{-- Centered Tab Bar with Full-Width Gray Bottom Line --}}
    <div class="bag-tab-nav-bar">
        <div class="container">
            <div class="bag-tab-nav">
                <button class="bag-tab-btn active" data-target="#tab-school-bag">
                    SCHOOL BAG
                </button>
                <button class="bag-tab-btn" data-target="#tab-ladies-bag">
                    LADIES BAG
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Tab Content Panes --}}
        <div class="tab-content">
            {{-- School Bag Tab --}}
            <div class="tab-pane fade show active" id="tab-school-bag">
                @if($schoolBagProducts->count() > 0)
                <div class="row g-4">
                    @foreach($schoolBagProducts as $product)
                    <div class="col-6 col-md-g5">
                        @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-5">
                    <a href="{{ $schoolBagCat ? route('category', $schoolBagCat->slug) : route('shop') }}" class="gani-view-all-btn">VIEW ALL</a>
                </div>
                @else
                <p class="text-center text-muted py-4">No School Bag products found</p>
                @endif
            </div>

            {{-- Ladies Bag Tab --}}
            <div class="tab-pane fade" id="tab-ladies-bag">
                @if($ladiesBagProducts->count() > 0)
                <div class="row g-4">
                    @foreach($ladiesBagProducts as $product)
                    <div class="col-6 col-md-g5">
                        @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-5">
                    <a href="{{ $ladiesBagCat ? route('category', $ladiesBagCat->slug) : route('shop') }}" class="gani-view-all-btn">VIEW ALL</a>
                </div>
                @else
                <p class="text-center text-muted py-4">No Ladies Bag products found</p>
                @endif
            </div>
        </div>
    </div>
</section>

@push('script')
<script>
$(document).ready(function() {
    $('.bag-tab-btn').on('click', function() {
        $('.bag-tab-btn').removeClass('active');
        $(this).addClass('active');

        var target = $(this).data('target');
        $('.tab-pane').removeClass('show active');
        $(target).addClass('show active');
    });
});
</script>
@endpush


{{-- ============================================================ --}}
{{-- TRUST BADGES --}}
{{-- ============================================================ --}}
<section class="gani-section gani-trust-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-truck gani-trust-icon"></i>
                    <h5 class="gani-trust-title">Free Shipping</h5>
                    <p class="gani-trust-text">On orders over BDT 5000</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-rotate-left gani-trust-icon"></i>
                    <h5 class="gani-trust-title">Free Return</h5>
                    <p class="gani-trust-text">30 Day Return Policy</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-shield-halved gani-trust-icon"></i>
                    <h5 class="gani-trust-title">Secure Payment</h5>
                    <p class="gani-trust-text">100% Secure Payment</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gani-trust-item">
                    <i class="fa-solid fa-headset gani-trust-icon"></i>
                    <h5 class="gani-trust-title">Customer Support</h5>
                    <p class="gani-trust-text">Dedicated Customer Service</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script src="{{ asset('frontEnd/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('frontEnd/js/jquery.syotimer.min.js') }}"></script>

<script>
    // Story product modal
    function openStoryModal(card) {
        var modal = document.getElementById('ganiStoryModal');
        var video = document.getElementById('ganiStoryVideo');
        var videoSrc = card.getAttribute('data-video');
        var thumbSrc = card.getAttribute('data-thumb');
        video.src = videoSrc;
        // If video fails to load, show thumbnail as poster
        video.setAttribute('poster', thumbSrc);
        video.onerror = function() {
            video.setAttribute('poster', thumbSrc);
        };
        document.getElementById('ganiSmTitle').textContent = card.getAttribute('data-prod-name');
        document.getElementById('ganiSmPrice').textContent = '৳' + card.getAttribute('data-prod-price');
        var oldEl = document.getElementById('ganiSmOldPrice');
        var oldPrice = card.getAttribute('data-prod-old');
        oldEl.textContent = oldPrice ? '৳' + oldPrice : '';
        oldEl.style.display = oldPrice ? 'inline' : 'none';
        document.getElementById('ganiSmProdId').value = card.getAttribute('data-prod-id');
        var cartForm = document.getElementById('ganiSmCartForm');
        var cartAction = card.getAttribute('data-add-to-cart');
        if (cartAction) {
            cartForm.action = cartAction;
            document.getElementById('ganiSmCartBtn').style.display = 'block';
        } else {
            cartForm.action = '';
            document.getElementById('ganiSmCartBtn').style.display = 'none';
        }
        document.getElementById('ganiSmViewLink').href = card.getAttribute('data-prod-link');
        modal.classList.add('active');
        video.load();
        video.play().catch(function() {});
    }
    function closeStoryModal() {
        var modal = document.getElementById('ganiStoryModal');
        var video = document.getElementById('ganiStoryVideo');
        modal.classList.remove('active');
        video.pause();
        video.src = '';
    }
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('ganiStoryModal');
        if (modal && e.target === modal) closeStoryModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeStoryModal();
    });
    // Bind story card clicks — open modal
    document.querySelectorAll('.gani-story-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.gani-story-cart-btn') || e.target.closest('form')) return;
            openStoryModal(this);
        });
    });

    // Auto-play videos when in viewport
    (function() {
        var videos = document.querySelectorAll('.gani-story-video');
        if (!videos.length || !window.IntersectionObserver) return;
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                var video = entry.target;
                if (entry.isIntersecting) {
                    video.play().catch(function(){});
                } else {
                    video.pause();
                }
            });
        }, { threshold: 0.5 });
        videos.forEach(function(v) { observer.observe(v); });
    })();

    // Hero Slider — vanilla JS
    (function() {
        var slider = document.getElementById('ganiHeroSlider');
        if (!slider) return;
        var slides = slider.querySelectorAll('.gani-hero-slide');
        var dots = slider.querySelectorAll('.gani-slider-dot');
        if (slides.length === 0) return;
        var current = 0;
        var interval;

        // Ensure first slide is active
        slides.forEach(function(s, i) {
            s.classList.toggle('active', i === 0);
        });
        dots.forEach(function(d, i) {
            d.classList.toggle('active', i === 0);
        });

        function goToSlide(index) {
            if (index < 0 || index >= slides.length) return;
            slides.forEach(function(s) { s.classList.remove('active'); });
            dots.forEach(function(d) { d.classList.remove('active'); });
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
            current = index;
        }

        function nextSlide() {
            if (slides.length < 2) return;
            goToSlide((current + 1) % slides.length);
        }

        function startAutoPlay() {
            stopAutoPlay();
            if (slides.length < 2) return;
            interval = setInterval(nextSlide, 5000);
        }

        function stopAutoPlay() {
            if (interval) { clearInterval(interval); interval = null; }
        }

        // Dot clicks
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                var idx = parseInt(this.getAttribute('data-slide'));
                goToSlide(idx);
                startAutoPlay();
            });
        });

        // Pause on hover
        slider.addEventListener('mouseenter', stopAutoPlay);
        slider.addEventListener('mouseleave', startAutoPlay);

        startAutoPlay();
    })();

    $(document).ready(function() {
        // Reviews carousel
        if ($(".reviews-carousel").length) {
            $(".reviews-carousel").owlCarousel({
                margin: 15,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: { items: 2, nav: false },
                    600: { items: 3, nav: false },
                    1000: { items: 5, nav: false },
                },
            });
        }
    });

    // Color image swap on hover/click
    document.querySelectorAll('.gani-swatch-btn').forEach(function(btn) {
        btn.addEventListener('mouseenter', function() {
            var card = this.closest('.gani-product-card');
            var mainImg = card ? card.querySelector('.gani-product-img') : null;
            var swapUrl = this.getAttribute('data-swap-img');
            if (mainImg && swapUrl) {
                mainImg.setAttribute('src', swapUrl);
            }
        });
        btn.addEventListener('mouseleave', function() {
            var card = this.closest('.gani-product-card');
            var mainImg = card ? card.querySelector('.gani-product-img') : null;
            var origUrl = mainImg ? mainImg.getAttribute('data-main-img') : null;
            if (mainImg && origUrl) {
                mainImg.setAttribute('src', origUrl);
            }
        });
    });

    // Collection tabs
    document.querySelectorAll('.gani-collection-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            document.querySelectorAll('.gani-collection-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.gani-collection-panel').forEach(function(p) { p.classList.remove('active'); });
            this.classList.add('active');
            var panel = document.getElementById('panel-' + target);
            if (panel) panel.classList.add('active');
        });
    });

</script>
@endpush
