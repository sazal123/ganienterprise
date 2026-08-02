<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

		<title>@yield('title') - {{$generalsetting->name}}</title>

        <!-- App favicon -->

        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}} Favicon" />
        <meta name="author" content="Creative Design" />
        <link rel="canonical" href="https://creativedesign.com.bd" />
        @stack('seo')
        @stack('css')
        <link rel="stylesheet" href="{{asset('frontEnd/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/animate.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/all.min.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/owl.carousel.min.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/owl.theme.default.min.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/mobile-menu.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/select2.min.css')}}" />
        <!-- toastr css -->
        <link rel="stylesheet" href="{{asset('backEnd/assets/css/toastr.min.css')}}" />

        <link rel="stylesheet" href="{{asset('frontEnd/css/style.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/responsive.css')}}" />
        <link rel="stylesheet" href="{{asset('frontEnd/css/main.css')}}" />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <meta name="facebook-domain-verification" content="{{$generalsetting->facebook_verification}}" />
        <meta name="google-site-verification" content="{{$generalsetting->google_verification}}" />





        @foreach($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
            !(function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = "2.0";
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");
            fbq("init", "{{{$pixel->code}}}");
            fbq("track", "PageView");
        </script>
        <noscript>
            <img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id={{{$pixel->code}}}&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
        @endforeach

        @foreach($gtm_code as $gtm)
        <!-- Google tag (gtag.js) -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-{{ $gtm->code }}');</script>
        <!-- End Google Tag Manager -->
        @endforeach
        <style>
            .whatsapp-float {
                position: fixed;
                bottom: 20px; /* Adjust vertical position */
                left: 20px; /* Adjust horizontal position */
                z-index: 1000;
                background-color: #25D366;
                color: white;
                border-radius: 50%;
                padding: 15px;
                font-size: 24px; /* Icon size */
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .whatsapp-float:hover {
                color: white;
                box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            }
            /* Hide the .whatsapp-float element on mobile devices */
            @media (max-width: 768px) {
                .whatsapp-float {
                    display: none;
                }
            }
            .stock-out-overlay {
                position: absolute;
                top: 50%;
                left: 0;
                transform: translateY(-50%);
                width: 100%;
                background-color: white;
                color: black;
                font-size: 1em;
                opacity:0.8;
                font-weight: bold;
                text-align: center;
                padding: 10px 0;
                overflow: hidden;
                white-space: nowrap;
            }
            /* Facebook icon */
            .social_list .fa-facebook-f {
                padding:5px 8px;
                color:white;
                background-color: #3b5998;

            }

            .social_list .fa-facebook-f:hover {
                background-color: #2d4373;  /* Darker Facebook blue on hover */
            }

            /* Twitter icon */
            .social_list .fa-twitter {
                padding:5px 8px;
                color:white;
                background-color: #1da1f2;  /* Twitter blue */
            }

            .social_list .fa-twitter:hover {
                padding:5px 8px;
                color:white;
                background-color: #0c85d0;  /* Darker Twitter blue on hover */
            }

            /* Instagram icon */
            .social_list .fa-instagram {
                padding:5px 8px;
                color:white;
                background-color: #e4405f;  /* Instagram pink */
            }

            .social_list .fa-instagram:hover {
                padding:5px 8px;
                color:white;
                background-color: #bc2a8d;  /* Darker Instagram purple-pink on hover */
            }

            /* LinkedIn icon */
            .social_list .fa-linkedin {
                padding:5px 8px;
                color:white;
                background-color: #0077b5;  /* LinkedIn blue */
            }

            .social_list .fa-linkedin:hover {
                background-color: #005983;  /* Darker LinkedIn blue on hover */
            }

            /* WhatsApp icon */
            .social_list .fa-whatsapp {
                padding:5px 8px;
                color:white;
                background-color: #25d366;  /* WhatsApp green */
            }

            .social_list .fa-whatsapp:hover {
                background-color: #128c7e;  /* Darker WhatsApp green on hover */
            }

            /* YouTube icon */
            .social_list .fa-youtube {
                padding:5px 8px;
                color:white;
                background-color: #ff0000;  /* YouTube red */
            }

            .social_list .fa-youtube:hover {
                background-color: #cc0000;  /* Darker YouTube red on hover */
            }

        </style>
        {!! $generalsetting->header_code !!}
    </head>
    <body class="gotop">

        @php $subtotal = Cart::instance('shopping')->subtotal(); @endphp
        <div class="mobile-menu">
                <div class="mobile-menu-logo">
                    <div class="logo-image">
                        <img src="{{asset($generalsetting->white_logo)}}" alt="" />
                    </div>
                    <div class="mobile-menu-close">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <ul class="first-nav">
                    @foreach($menucategories as $scategory)
                    <li class="parent-category">
                        <a href="{{url('category/'.$scategory->slug)}}" class="menu-category-name">
                            <img src="{{asset($scategory->image)}}" alt="" class="side_cat_img" />
                            {{$scategory->name}}
                        </a>
                        @if($scategory->subcategories->count() > 0)
                        <span class="menu-category-toggle">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                        @endif
                        <ul class="second-nav" style="display: none;">
                            @foreach($scategory->subcategories as $subcategory)
                            <li class="parent-subcategory">
                                <a href="{{url('subcategory/'.$subcategory->slug)}}" class="menu-subcategory-name">{{$subcategory->subcategoryName}}</a>
                                @if($subcategory->childcategories->count() > 0)
                                <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                                @endif
                                <ul class="third-nav" style="display: none;">
                                    @foreach($subcategory->childcategories as $childcat)
                                    <li class="childcategory"><a href="{{url('products/'.$childcat->slug)}}" class="menu-childcategory-name">{{$childcat->childcategoryName}}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                </ul>
            </div>
        {{-- ============================================================ --}}
        {{-- FOLKS-INSPIRED HEADER — Gold announcement + Black bar + Sticky Nav --}}
        {{-- ============================================================ --}}

        {{-- Gold Announcement Bar --}}
        <div class="gani-top-announcement">
            <div class="container d-flex align-items-center justify-content-center gap-2 gap-md-4 flex-wrap">
                <span class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock fa-fade"></i>
                    Hurry! Free Shipping Ends Soon!
                </span>
                <div id="gani-countdown-timer" class="d-flex align-items-center gap-2 gap-md-3 fw-bold">
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-days" class="gani-cd-num">00</span> Days</div>
                    <span class="d-none d-md-inline">:</span>
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-hours" class="gani-cd-num">21</span> Hours</div>
                    <span class="d-none d-md-inline">:</span>
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-minutes" class="gani-cd-num">00</span> Mins</div>
                    <span class="d-none d-md-inline">:</span>
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-seconds" class="gani-cd-num">50</span> Secs</div>
                </div>
            </div>
        </div>

        {{-- Black Secondary Bar --}}
        <div class="gani-top-bar">
            <div class="container d-flex align-items-center justify-content-center justify-content-md-between flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <a href="tel:{{ $contact->hotline }}" class="text-white text-decoration-none"><i class="fa-solid fa-phone me-1"></i> {{ $contact->hotline }}</a>
                </div>
                <div class="fw-bold gani-top-shipping-text">Your Trusted Partner For Imported Products</div>
                <div class="d-flex align-items-center gap-3">
                    @if(Auth::guard('customer')->user())
                        <a href="{{route('customer.account')}}" class="gani-top-link"><i class="fa-regular fa-user me-1"></i> {{ Str::limit(Auth::guard('customer')->user()->name, 14) }}</a>
                        <a href="{{ route('logout') }}" class="gani-top-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    @else
                        <a href="{{route('customer.login')}}" class="gani-top-link"><i class="fa-regular fa-user me-1"></i> Login / Register</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Mobile Header --}}
        <div class="gani-mobile-header">
            <div class="container d-flex align-items-center justify-content-between py-2">
                <button class="gani-mobile-toggle" id="ganiMobileToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <a href="{{route('home')}}" class="gani-mobile-logo-link">
                    <img src="{{asset($generalsetting->dark_logo ?? $generalsetting->white_logo)}}" alt="{{$generalsetting->name}}" class="gani-mobile-logo-img" />
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{route('customer.order_track')}}" class="gani-mobile-icon"><i class="fa-solid fa-truck"></i></a>
                    <a href="{{route('customer.checkout')}}" class="gani-mobile-icon position-relative">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="gani-mobile-cart-badge">{{Cart::instance('shopping')->count()}}</span>
                    </a>
                </div>
            </div>
            <div class="gani-mobile-search">
                <form action="{{route('search')}}">
                    <input type="text" placeholder="Search Product..." class="msearch_keyword" name="keyword" />
                    <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>
        </div>

        {{-- Desktop Sticky Header --}}
        <header class="gani-desktop-header" id="ganiHeader">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('home')}}" class="gani-header-logo">
                            @if(isset($generalsetting) && ($generalsetting->dark_logo || $generalsetting->white_logo))
                                <img src="{{asset($generalsetting->dark_logo ?? $generalsetting->white_logo)}}" alt="{{$generalsetting->name ?? ''}}" style="max-height: 45px;" />
                            @else
                                Gani<span class="text-gold">.</span>
                            @endif
                        </a>
                    </div>
                    <div class="col">
                        <nav class="gani-nav">
                            {{-- Apnar bag khujun — Dropdown for all categories --}}
                            <div class="gani-nav-dropdown">
                                <button class="gani-nav-link dropdown-toggle">
                                    Category
                                </button>
                                <div class="gani-dropdown-menu">
                                    @foreach($menucategories->take(12) as $cat)
                                        @if($cat->subcategories->count() > 0)
                                        <div class="gani-dropdown-sub">
                                            <a href="{{ url('category/'.$cat->slug) }}" class="gani-dropdown-item d-flex justify-content-between align-items-center">
                                                {{ $cat->name }}
                                                <i class="fa-solid fa-chevron-right gani-sub-arrow"></i>
                                            </a>
                                            <div class="gani-sub-menu">
                                                @foreach($cat->subcategories as $sub)
                                                    @if($sub->childcategories->count() > 0)
                                                    <div class="gani-dropdown-sub-child">
                                                        <a href="{{ url('subcategory/'.$sub->slug) }}" class="gani-dropdown-item d-flex justify-content-between align-items-center">
                                                            {{ $sub->subcategoryName }}
                                                            <i class="fa-solid fa-chevron-right gani-sub-arrow"></i>
                                                        </a>
                                                        <div class="gani-child-menu">
                                                            @foreach($sub->childcategories as $child)
                                                            <a href="{{ url('products/'.$child->slug) }}" class="gani-dropdown-item">{{ $child->childcategoryName }}</a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @else
                                                    <a href="{{ url('subcategory/'.$sub->slug) }}" class="gani-dropdown-item">{{ $sub->subcategoryName }}</a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @else
                                        <a href="{{ url('category/'.$cat->slug) }}" class="gani-dropdown-item">{{ $cat->name }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Quick links to first few categories --}}
                            @foreach($menucategories->take(6) as $cat)
                            <a href="{{ url('category/'.$cat->slug) }}" class="gani-nav-link">{{ $cat->name }}</a>
                            @endforeach

                            <a href="{{ route('offers') }}" class="gani-nav-link gani-nav-highlight"><i class="fa-solid fa-fire-flame-curved me-1 text-warning"></i> Offer</a>
                            <a href="{{ route('shop') }}" class="gani-nav-link">All Products</a>
                        </nav>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-3">
                            <button class="gani-icon-btn" id="ganiSearchToggle">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <a href="{{route('customer.order_track')}}" class="gani-icon-btn gani-track-link">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span class="d-none d-lg-inline gani-icon-label">Track Order</span>
                            </a>
                            <a href="{{route('customer.checkout')}}" class="gani-icon-btn position-relative">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <span class="gani-cart-count">{{Cart::instance('shopping')->count()}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Search dropdown --}}
            <div class="gani-search-dropdown" id="ganiSearchDropdown">
                <div class="container">
                    <form action="{{route('search')}}" class="gani-search-form">
                        <input type="text" name="keyword" placeholder="Search Products..." class="gani-search-input" autocomplete="off" />
                        <button type="submit" class="gani-search-submit"><i class="fa-solid fa-search"></i></button>
                        <button type="button" class="gani-search-close" id="ganiSearchClose"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Mobile Side Menu (Overlay) --}}
        <div class="gani-mobile-overlay" id="ganiMobileOverlay"></div>
        <div class="gani-mobile-sidebar" id="ganiMobileSidebar">
            <div class="gani-sidebar-header">
                <a href="{{route('home')}}" class="gani-sidebar-logo">
                    <img src="{{asset($generalsetting->dark_logo ?? $generalsetting->white_logo)}}" alt="" class="gani-sidebar-logo-img" />
                </a>
                <button class="gani-sidebar-close" id="ganiSidebarClose"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="gani-sidebar-body">
                @if(Auth::guard('customer')->user())
                <a href="{{route('customer.account')}}" class="gani-sidebar-user">
                    <i class="fa-regular fa-user-circle fs-5 me-2"></i> {{ Auth::guard('customer')->user()->name }}
                </a>
                @else
                <a href="{{route('customer.login')}}" class="gani-sidebar-user">
                    <i class="fa-regular fa-user-circle fs-5 me-2"></i> Login / Register
                </a>
                @endif
                <hr class="my-2" />
                <nav class="gani-sidebar-nav">
                    <a href="{{route('home')}}" class="gani-sidebar-link"><i class="fa-solid fa-home me-2"></i> Home</a>
                    <a href="{{route('shop')}}" class="gani-sidebar-link"><i class="fa-solid fa-store me-2"></i> Shop All</a>
                    @foreach($menucategories as $cat)
                    <div class="gani-sidebar-accordion">
                        <button class="gani-sidebar-acc-btn">
                            {{ $cat->name }}
                            @if($cat->subcategories->count() > 0)
                            <i class="fa-solid fa-chevron-down"></i>
                            @endif
                        </button>
                        @if($cat->subcategories->count() > 0)
                        <div class="gani-sidebar-acc-content">
                            @foreach($cat->subcategories as $sub)
                            <a href="{{ url('subcategory/'.$sub->slug) }}" class="gani-sidebar-sub-link">{{ $sub->subcategoryName }}</a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                    <a href="{{route('customer.order_track')}}" class="gani-sidebar-link"><i class="fa-solid fa-truck me-2"></i> Track Order</a>
                </nav>
            </div>
        </div>

        {{-- Old mobile menu sidebar kept for compatibility (hidden) --}}
        <div class="mobile-menu" style="display:none !important;">
            <div class="mobile-menu-logo">
                <div class="logo-image">
                    <img src="{{asset($generalsetting->white_logo)}}" alt="" />
                </div>
                <div class="mobile-menu-close">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <ul class="first-nav">
                @foreach($menucategories as $scategory)
                <li class="parent-category">
                    <a href="{{url('category/'.$scategory->slug)}}" class="menu-category-name">
                        <img src="{{asset($scategory->image)}}" alt="" class="side_cat_img" />
                        {{$scategory->name}}
                    </a>
                    @if($scategory->subcategories->count() > 0)
                    <span class="menu-category-toggle">
                        <i class="fa fa-chevron-down"></i>
                    </span>
                    @endif
                    <ul class="second-nav" style="display: none;">
                        @foreach($scategory->subcategories as $subcategory)
                        <li class="parent-subcategory">
                            <a href="{{url('subcategory/'.$subcategory->slug)}}" class="menu-subcategory-name">{{$subcategory->subcategoryName}}</a>
                            @if($subcategory->childcategories->count() > 0)
                            <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                            @endif
                            <ul class="third-nav" style="display: none;">
                                @foreach($subcategory->childcategories as $childcat)
                                <li class="childcategory"><a href="{{url('products/'.$childcat->slug)}}" class="menu-childcategory-name">{{$childcat->childcategoryName}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
            </ul>
        </div>
        <div id="content">
            @yield('content')
        </div>
            <!-- content end -->
        <footer>
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <div class="footer-about">
                                <a href="{{route('home')}}">
                                    <img src="{{asset($generalsetting->white_logo ?? $generalsetting->dark_logo)}}" alt="{{$generalsetting->name}}" />
                                </a>
                                <p><i class="fa-solid fa-location-dot me-2 text-warning"></i>{{$contact->address}}</p>
                                <a href="tel:{{$contact->hotline}}" class="footer-hotlint"><i class="fa-solid fa-phone me-2"></i>{{$contact->hotline}}</a>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-3 mb-3 mb-sm-0 col-6">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title"><a>Useful Link</a></li>
                                    <li>
                                        <a href="{{route('contact')}}"> Contact Us</a>
                                    </li>
                                    @foreach($pages as $page)
                                    <li><a href="{{route('page',['slug'=>$page->slug])}}">{{$page->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-2 mb-3 mb-sm-0 col-6">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title"><a>Link</a></li>
                                     <li>
                                        <a href="{{route('shop')}}">All Products</a>
                                    </li>
                                    @foreach($pagesright as $key=>$value)
                                    <li>
                                        <a href="{{route('page',['slug'=>$value->slug])}}">{{$value->name}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- col end -->
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title stay_conn"><a>Follow Us</a></li>
                                </ul>
                                <ul class="social_link">
                                    @foreach($socialicons as $value)
                                    <li class="social_list">
                                        <a class="mobile-social-link" href="{{$value->link}}"><i class="{{$value->icon}}"></i></a>
                                    </li>
                                    @endforeach
                                </ul>

                            </div>
                        </div>
                        <!-- col end -->
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="copyright">
                                <p>© {{ date('Y') }} {{$generalsetting->name}}. All Rights Reserved. | <span style="color: white;">Developed By : <a href="https://www.hostmcw.com"><span style="color: white;">HOSTMCW</span></a></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <div class="footer_nav">
            <ul>
                <li>
                    <a id="ganiMobileToggle" style="cursor:pointer;">
                        <span>Category</span>
                    </a>
                </li>

                <li>
                    <a href="https://wa.me/{{str_replace(['+', ' ', '-'], '', $contact->whatsapp)}}">
                        <span>
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <span>Whatsapp</span>
                    </a>
                </li>

                <li class="mobile_home">
                    <a href="{{route('home')}}">
                        <span><i class="fa-solid fa-home"></i></span> <span>Home</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('customer.checkout')}}">
                        <span>
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <span>Cart (<b class="mobilecart-qty">{{Cart::instance('shopping')->count()}}</b>)</span>
                    </a>
                </li>
                @if(Auth::guard('customer')->user())
                <li>
                    <a href="{{route('customer.account')}}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Account</span>
                    </a>
                </li>
                @else
                <li>
                    <a href="{{route('customer.login')}}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Login</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <a href="https://wa.me/{{str_replace(['+', ' ', '-'], '', $contact->whatsapp)}}?text=Hello, I would like to inquire about..." target="_blank" class="whatsapp-float">
            <i class="fa-brands fa-whatsapp"></i>
        </a>

        <div class="scrolltop" style="">
            <div class="scroll">
                <i class="fa fa-angle-up"></i>
            </div>
        </div>

        <!-- /. fixed sidebar -->

        <div id="custom-modal"></div>
        <div id="page-overlay"></div>
        <div id="loading"><div class="custom-loader"></div></div>

        <script src="{{asset('frontEnd/js/jquery-3.6.3.min.js')}}"></script>
        <script src="{{asset('frontEnd/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('frontEnd/js/owl.carousel.min.js')}}"></script>
        <script src="{{asset('frontEnd/js/mobile-menu.js')}}"></script>
        <script src="{{asset('frontEnd/js/mobile-menu-init.js')}}"></script>
        <script src="{{asset('frontEnd/js/wow.min.js')}}"></script>
        <script>
            new WOW().init();
        </script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- feather icon -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
        <script>
            feather.replace();
        </script>
        <script src="{{asset('backEnd/assets/js/toastr.min.js')}}"></script>
        {!! Toastr::message() !!} @stack('script')
        <script>
            $(".quick_view").on("click", function () {
                var id = $(this).data("id");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('quickview')}}",
                        success: function (data) {
                            if (data) {
                                $("#custom-modal").html(data);
                                $("#custom-modal").show();
                                $("#loading").hide();
                                $("#page-overlay").show();
                            }
                        },
                    });
                }
            });
        </script>
        <!-- quick view end -->
        <!-- cart js start -->
        <script>
            $(".addcartbutton").on("click", function () {
                var id = $(this).data("id");
                var qty = 1;
                if (id) {
                    $.ajax({
                        cache: "false",
                        type: "GET",
                        url: "{{url('add-to-cart')}}/" + id + "/" + qty,
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart successfully');
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });
            $(".cart_store").on("click", function () {
                var id = $(this).data("id");
                var qty = $(this).parent().find("input").val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, qty: qty ? qty : 1 },
                        url: "{{route('cart.store')}}",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart succfully');
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            $(".cart_remove").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.remove')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart() + cart_summary();
                            }
                        },
                    });
                }
            });

            $(".cart_increment").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.increment')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            $(".cart_decrement").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.decrement')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            function cart_count() {
                $.ajax({
                    type: "GET",
                    url: "{{route('cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $("#cart-qty").html(data);
                        } else {
                            $("#cart-qty").empty();
                        }
                    },
                });
            }
            function mobile_cart() {
                $.ajax({
                    type: "GET",
                    url: "{{route('mobile.cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $(".mobilecart-qty").html(data);
                        } else {
                            $(".mobilecart-qty").empty();
                        }
                    },
                });
            }
            function cart_summary() {
                $.ajax({
                    type: "GET",
                    url: "{{route('shipping.charge')}}",
                    dataType: "html",
                    success: function (response) {
                        $(".cart-summary").html(response);
                    },
                });
            }
        </script>
        <!-- cart js end -->
        <script>
            $(".search_click").on("keyup change", function () {
                var keyword = $(".search_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
            $(".msearch_click").on("keyup change", function () {
                var keyword = $(".msearch_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $("#loading").hide();
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
        </script>
        <!-- search js start -->
        <script></script>
        <script></script>
        <script>
            $(document).on("change", ".district", function () {
                var id = $(this).val();
                var $areaSelect = $(this).closest('form').find('.area');
                if (!$areaSelect.length) {
                    $areaSelect = $('.area');
                }
                var selectedAreaId = $areaSelect.data('selected') || $areaSelect.val();

                if (id) {
                    var districtsUrl = "{{ route('districts', [], false) }}";
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: districtsUrl,
                        dataType: "json",
                        success: function (res) {
                            $areaSelect.empty();
                            $areaSelect.append('<option value="">Select..</option>');
                            if (res && Object.keys(res).length > 0) {
                                $.each(res, function (key, value) {
                                    var isSelected = (selectedAreaId && selectedAreaId == key) ? 'selected' : '';
                                    $areaSelect.append('<option value="' + key + '" ' + isSelected + '>' + value + "</option>");
                                });
                            }
                            $areaSelect.trigger('change');
                        },
                        error: function () {
                            $.ajax({
                                type: "GET",
                                data: { id: id },
                                url: "/districts",
                                dataType: "json",
                                success: function (res) {
                                    $areaSelect.empty();
                                    $areaSelect.append('<option value="">Select..</option>');
                                    if (res && Object.keys(res).length > 0) {
                                        $.each(res, function (key, value) {
                                            var isSelected = (selectedAreaId && selectedAreaId == key) ? 'selected' : '';
                                            $areaSelect.append('<option value="' + key + '" ' + isSelected + '>' + value + "</option>");
                                        });
                                    }
                                    $areaSelect.trigger('change');
                                }
                            });
                        }
                    });
                } else {
                    $areaSelect.empty();
                    $areaSelect.append('<option value="">Select..</option>');
                    $areaSelect.trigger('change');
                }
            });
        </script>
        <script>
            $(".toggle").on("click", function () {
                $("#page-overlay").show();
                $(".mobile-menu").addClass("active");
            });

            $("#page-overlay").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
                $(".feature-products").removeClass("active");
            });

            $(".mobile-menu-close").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
            });

            $(".mobile-filter-toggle").on("click", function () {
                $("#page-overlay").show();
                $(".feature-products").addClass("active");
            });
        </script>
        <script>
            $(document).ready(function () {
                $(".parent-category").each(function () {
                    const menuCatToggle = $(this).find(".menu-category-toggle");
                    const secondNav = $(this).find(".second-nav");

                    menuCatToggle.on("click", function () {
                        menuCatToggle.toggleClass("active");
                        secondNav.slideToggle("fast");
                        $(this).closest(".parent-category").toggleClass("active");
                    });
                });
                $(".parent-subcategory").each(function () {
                    const menuSubcatToggle = $(this).find(".menu-subcategory-toggle");
                    const thirdNav = $(this).find(".third-nav");

                    menuSubcatToggle.on("click", function () {
                        menuSubcatToggle.toggleClass("active");
                        thirdNav.slideToggle("fast");
                        $(this).closest(".parent-subcategory").toggleClass("active");
                    });
                });
            });
        </script>

        {{-- MmenuLight removed — header redesigned with custom mobile sidebar --}}

        <script>
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 200) {
            //             document.getElementById("navbar_top").classList.add("fixed-top");
            //         } else {
            //             document.getElementById("navbar_top").classList.remove("fixed-top");
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 0) {
            //             document.getElementById("m_navbar_top").classList.add("fixed-top");
            //             // add padding top to show content behind navbar
            //             navbar_height = document.querySelector(".navbar").offsetHeight;
            //             document.body.style.paddingTop = navbar_height + "px";
            //         } else {
            //             document.getElementById("m_navbar_top").classList.remove("fixed-top");
            //             // remove padding top from body
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */

            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) {
                    $(".scrolltop:hidden").stop(true, true).fadeIn();
                } else {
                    $(".scrolltop").stop(true, true).fadeOut();
                }
            });
            $(function () {
                $(".scroll").click(function () {
                    $("html,body").animate({ scrollTop: $(".gotop").offset().top }, "1000");
                    return false;
                });
            });
        </script>
        <script>
            $(".filter_btn").click(function(){
               $(".filter_sidebar").addClass('active');
               $("body").css("overflow-y", "hidden");
            })
            $(".filter_close").click(function(){
               $(".filter_sidebar").removeClass('active');
               $("body").css("overflow-y", "auto");
            })
        </script>
        <!--search ANIMAtion end-->

        {{-- ============================================================ --}}
        {{-- FOLKS-INSPIRED HEADER SCRIPTS --}}
        {{-- ============================================================ --}}
        <script>
            // Header shadow on scroll
            document.addEventListener('DOMContentLoaded', function() {
                var header = document.getElementById('ganiHeader');
                if (header) {
                    window.addEventListener('scroll', function() {
                        if (window.scrollY > 50) {
                            header.classList.add('gani-header-shadow');
                        } else {
                            header.classList.remove('gani-header-shadow');
                        }
                    });
                }

                // Mobile sidebar toggle
                var toggleBtn = document.getElementById('ganiMobileToggle');
                var sidebar = document.getElementById('ganiMobileSidebar');
                var overlay = document.getElementById('ganiMobileOverlay');
                var closeBtn = document.getElementById('ganiSidebarClose');

                function openSidebar() {
                    if (sidebar) sidebar.classList.add('active');
                    if (overlay) overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
                function closeSidebar() {
                    if (sidebar) sidebar.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }

                if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
                if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
                if (overlay) overlay.addEventListener('click', closeSidebar);

                // Sidebar accordion
                document.querySelectorAll('.gani-sidebar-acc-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var content = this.nextElementSibling;
                        if (content && content.classList.contains('gani-sidebar-acc-content')) {
                            content.classList.toggle('open');
                            this.classList.toggle('active');
                        }
                    });
                });

                // Search toggle
                var searchToggle = document.getElementById('ganiSearchToggle');
                var searchDropdown = document.getElementById('ganiSearchDropdown');
                var searchClose = document.getElementById('ganiSearchClose');

                if (searchToggle && searchDropdown) {
                    searchToggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        searchDropdown.classList.toggle('active');
                        if (searchDropdown.classList.contains('active')) {
                            setTimeout(function() {
                                searchDropdown.querySelector('.gani-search-input').focus();
                            }, 100);
                        }
                    });
                }
                if (searchClose && searchDropdown) {
                    searchClose.addEventListener('click', function() {
                        searchDropdown.classList.remove('active');
                    });
                }
                // Close search on click outside
                document.addEventListener('click', function(e) {
                    if (searchDropdown && searchDropdown.classList.contains('active')) {
                        if (!e.target.closest('#ganiSearchToggle') && !e.target.closest('.gani-search-dropdown')) {
                            searchDropdown.classList.remove('active');
                        }
                    }
                });

                // Countdown timer
                var totalSeconds = (21 * 3600) + 50;
                var daysEl = document.getElementById('gani-cd-days');
                var hoursEl = document.getElementById('gani-cd-hours');
                var minutesEl = document.getElementById('gani-cd-minutes');
                var secondsEl = document.getElementById('gani-cd-seconds');

                function toBanglaNum(str) {
                    var bn = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
                    return str.replace(/[0-9]/g, function(w) { return bn[w]; });
                }

                function pad(n) { return n < 10 ? '0' + n : '' + n; }

                function updateTimer() {
                    if (totalSeconds <= 0) return;
                    var days = Math.floor(totalSeconds / 86400);
                    var hours = Math.floor((totalSeconds % 86400) / 3600);
                    var mins = Math.floor((totalSeconds % 3600) / 60);
                    var secs = totalSeconds % 60;
                    if (daysEl) daysEl.textContent = toBanglaNum(pad(days));
                    if (hoursEl) hoursEl.textContent = toBanglaNum(pad(hours));
                    if (minutesEl) minutesEl.textContent = toBanglaNum(pad(mins));
                    if (secondsEl) secondsEl.textContent = toBanglaNum(pad(secs));
                    totalSeconds--;
                }
                updateTimer();
                setInterval(updateTimer, 1000);
            });

            // Product Card Image Hover Swap (2nd image & color swatches)
            $(document).on('mouseenter', '.gani-product-card .gani-product-img-wrap', function() {
                var $img = $(this).find('.gani-product-img');
                var hoverImg = $img.attr('data-hover-img');
                if ($img.length && hoverImg) {
                    $img.attr('src', hoverImg);
                }
            });

            $(document).on('mouseleave', '.gani-product-card .gani-product-img-wrap', function() {
                var $img = $(this).find('.gani-product-img');
                var mainImg = $img.attr('data-main-img');
                if ($img.length && mainImg) {
                    $img.attr('src', mainImg);
                }
            });

            $(document).on('mouseenter', '.gani-swatch-btn', function() {
                var $card = $(this).closest('.gani-product-card');
                var $img = $card.find('.gani-product-img');
                var swapUrl = $(this).attr('data-swap-img');
                if ($img.length && swapUrl) {
                    $img.attr('src', swapUrl);
                }
            });

            $(document).on('mouseleave', '.gani-swatch-btn', function() {
                var $card = $(this).closest('.gani-product-card');
                var $img = $card.find('.gani-product-img');
                var mainImg = $img.attr('data-main-img');
                if ($img.length && mainImg) {
                    $img.attr('src', mainImg);
                }
            });
        </script>

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm->code }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    </body>
</html>
