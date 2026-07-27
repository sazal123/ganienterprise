<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

		<title><?php echo $__env->yieldContent('title'); ?> - <?php echo e($generalsetting->name); ?></title>

        <!-- App favicon -->

        <link rel="shortcut icon" href="<?php echo e(asset($generalsetting->favicon)); ?>" alt="<?php echo e($generalsetting->name); ?> Favicon" />
        <meta name="author" content="Creative Design" />
        <link rel="canonical" href="https://creativedesign.com.bd" />
        <?php echo $__env->yieldPushContent('seo'); ?>
        <?php echo $__env->yieldPushContent('css'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/bootstrap.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/animate.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/all.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/owl.carousel.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/owl.theme.default.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/mobile-menu.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/select2.min.css')); ?>" />
        <!-- toastr css -->
        <link rel="stylesheet" href="<?php echo e(asset('backEnd/assets/css/toastr.min.css')); ?>" />

        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/style.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/responsive.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('frontEnd/css/main.css')); ?>" />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <meta name="facebook-domain-verification" content="<?php echo e($generalsetting->facebook_verification); ?>" />
        <meta name="google-site-verification" content="<?php echo e($generalsetting->google_verification); ?>" />





        <?php $__currentLoopData = $pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pixel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
            fbq("init", "<?php echo e($pixel->code); ?>");
            fbq("track", "PageView");
        </script>
        <noscript>
            <img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id=<?php echo e($pixel->code); ?>&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php $__currentLoopData = $gtm_code; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gtm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!-- Google tag (gtag.js) -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-<?php echo e($gtm->code); ?>');</script>
        <!-- End Google Tag Manager -->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php echo $generalsetting->header_code; ?>

    </head>
    <body class="gotop">

        <?php $subtotal = Cart::instance('shopping')->subtotal(); ?>
        <div class="mobile-menu">
                <div class="mobile-menu-logo">
                    <div class="logo-image">
                        <img src="<?php echo e(asset($generalsetting->white_logo)); ?>" alt="" />
                    </div>
                    <div class="mobile-menu-close">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <ul class="first-nav">
                    <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="parent-category">
                        <a href="<?php echo e(url('category/'.$scategory->slug)); ?>" class="menu-category-name">
                            <img src="<?php echo e(asset($scategory->image)); ?>" alt="" class="side_cat_img" />
                            <?php echo e($scategory->name); ?>

                        </a>
                        <?php if($scategory->subcategories->count() > 0): ?>
                        <span class="menu-category-toggle">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                        <?php endif; ?>
                        <ul class="second-nav" style="display: none;">
                            <?php $__currentLoopData = $scategory->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="parent-subcategory">
                                <a href="<?php echo e(url('subcategory/'.$subcategory->slug)); ?>" class="menu-subcategory-name"><?php echo e($subcategory->subcategoryName); ?></a>
                                <?php if($subcategory->childcategories->count() > 0): ?>
                                <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                                <?php endif; ?>
                                <ul class="third-nav" style="display: none;">
                                    <?php $__currentLoopData = $subcategory->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="childcategory"><a href="<?php echo e(url('products/'.$childcat->slug)); ?>" class="menu-childcategory-name"><?php echo e($childcat->childcategoryName); ?></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        
        
        

        
        <div class="gani-top-announcement">
            <div class="container d-flex align-items-center justify-content-center gap-2 gap-md-4 flex-wrap">
                <span class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock fa-fade"></i>
                    সময় শেষ হয়ে আসছে... ফ্রি শিপিং শেষ হতে বাকি!
                </span>
                <div id="gani-countdown-timer" class="d-flex align-items-center gap-2 gap-md-3 fw-bold">
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-days" class="gani-cd-num">০০</span> দিন</div>
                    <span class="d-none d-md-inline">:</span>
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-hours" class="gani-cd-num">২১</span> ঘণ্টা</div>
                    <span class="d-none d-md-inline">:</span>
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-minutes" class="gani-cd-num">০০</span> মিনিট</div>
                    <span class="d-none d-md-inline">:</span>
                    <div class="d-flex align-items-center gap-1"><span id="gani-cd-seconds" class="gani-cd-num">৫০</span> সেকেন্ড</div>
                </div>
            </div>
        </div>

        
        <div class="gani-top-bar">
            <div class="container d-flex align-items-center justify-content-center justify-content-md-between flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fa-solid fa-phone me-1"></i> <?php echo e($contact->hotline); ?></span>
                </div>
                <div class="fw-bold gani-top-shipping-text">৫০০০ টাকার বেশি অর্ডারে ফ্রি শিপিং</div>
                <div class="d-flex align-items-center gap-3">
                    <?php if(Auth::guard('customer')->user()): ?>
                        <a href="<?php echo e(route('customer.account')); ?>" class="gani-top-link"><i class="fa-regular fa-user me-1"></i> <?php echo e(Str::limit(Auth::guard('customer')->user()->name, 14)); ?></a>
                        <a href="<?php echo e(route('logout')); ?>" class="gani-top-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">লগআউট</a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"><?php echo csrf_field(); ?></form>
                    <?php else: ?>
                        <a href="<?php echo e(route('customer.login')); ?>" class="gani-top-link"><i class="fa-regular fa-user me-1"></i> লগইন / রেজিস্টার</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="gani-mobile-header">
            <div class="container d-flex align-items-center justify-content-between py-2">
                <button class="gani-mobile-toggle" id="ganiMobileToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <a href="<?php echo e(route('home')); ?>" class="gani-mobile-logo-link">
                    <img src="<?php echo e(asset($generalsetting->white_logo)); ?>" alt="<?php echo e($generalsetting->name); ?>" class="gani-mobile-logo-img" />
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?php echo e(route('customer.order_track')); ?>" class="gani-mobile-icon"><i class="fa-solid fa-truck"></i></a>
                    <a href="<?php echo e(route('customer.checkout')); ?>" class="gani-mobile-icon position-relative">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="gani-mobile-cart-badge"><?php echo e(Cart::instance('shopping')->count()); ?></span>
                    </a>
                </div>
            </div>
            <div class="gani-mobile-search">
                <form action="<?php echo e(route('search')); ?>">
                    <input type="text" placeholder="Search Product..." class="msearch_keyword" name="keyword" />
                    <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>
        </div>

        
        <header class="gani-desktop-header" id="ganiHeader">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="<?php echo e(route('home')); ?>" class="gani-header-logo">Gani<span class="text-gold">.</span></a>
                    </div>
                    <div class="col">
                        <nav class="gani-nav">
                            
                            <div class="gani-nav-dropdown">
                                <button class="gani-nav-link dropdown-toggle">
                                    ক্যাটাগরি
                                    <i class="fa-solid fa-chevron-down gani-nav-arrow"></i>
                                </button>
                                <div class="gani-dropdown-menu">
                                    <?php $__currentLoopData = $menucategories->take(12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($cat->subcategories->count() > 0): ?>
                                        <div class="gani-dropdown-sub">
                                            <a href="<?php echo e(url('category/'.$cat->slug)); ?>" class="gani-dropdown-item d-flex justify-content-between align-items-center">
                                                <?php echo e($cat->name); ?>

                                                <i class="fa-solid fa-chevron-right gani-sub-arrow"></i>
                                            </a>
                                            <div class="gani-sub-menu">
                                                <?php $__currentLoopData = $cat->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($sub->childcategories->count() > 0): ?>
                                                    <div class="gani-dropdown-sub-child">
                                                        <a href="<?php echo e(url('subcategory/'.$sub->slug)); ?>" class="gani-dropdown-item d-flex justify-content-between align-items-center">
                                                            <?php echo e($sub->subcategoryName); ?>

                                                            <i class="fa-solid fa-chevron-right gani-sub-arrow"></i>
                                                        </a>
                                                        <div class="gani-child-menu">
                                                            <?php $__currentLoopData = $sub->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <a href="<?php echo e(url('products/'.$child->slug)); ?>" class="gani-dropdown-item"><?php echo e($child->childcategoryName); ?></a>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    </div>
                                                    <?php else: ?>
                                                    <a href="<?php echo e(url('subcategory/'.$sub->slug)); ?>" class="gani-dropdown-item"><?php echo e($sub->subcategoryName); ?></a>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <a href="<?php echo e(url('category/'.$cat->slug)); ?>" class="gani-dropdown-item"><?php echo e($cat->name); ?></a>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            
                            <?php $__currentLoopData = $menucategories->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(url('category/'.$cat->slug)); ?>" class="gani-nav-link"><?php echo e($cat->name); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <a href="<?php echo e(route('shop')); ?>" class="gani-nav-link gani-nav-highlight">সকল পণ্য</a>
                        </nav>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-3">
                            <button class="gani-icon-btn" id="ganiSearchToggle">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <a href="<?php echo e(route('customer.order_track')); ?>" class="gani-icon-btn gani-track-link">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span class="d-none d-lg-inline gani-icon-label">ট্র্যাক</span>
                            </a>
                            <a href="<?php echo e(route('customer.checkout')); ?>" class="gani-icon-btn position-relative">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <span class="gani-cart-count"><?php echo e(Cart::instance('shopping')->count()); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="gani-search-dropdown" id="ganiSearchDropdown">
                <div class="container">
                    <form action="<?php echo e(route('search')); ?>" class="gani-search-form">
                        <input type="text" name="keyword" placeholder="পণ্য সার্চ করুন..." class="gani-search-input" autocomplete="off" />
                        <button type="submit" class="gani-search-submit"><i class="fa-solid fa-search"></i></button>
                        <button type="button" class="gani-search-close" id="ganiSearchClose"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                </div>
            </div>
        </header>

        
        <div class="gani-mobile-overlay" id="ganiMobileOverlay"></div>
        <div class="gani-mobile-sidebar" id="ganiMobileSidebar">
            <div class="gani-sidebar-header">
                <a href="<?php echo e(route('home')); ?>" class="gani-sidebar-logo">
                    <img src="<?php echo e(asset($generalsetting->white_logo)); ?>" alt="" class="gani-sidebar-logo-img" />
                </a>
                <button class="gani-sidebar-close" id="ganiSidebarClose"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="gani-sidebar-body">
                <?php if(Auth::guard('customer')->user()): ?>
                <a href="<?php echo e(route('customer.account')); ?>" class="gani-sidebar-user">
                    <i class="fa-regular fa-user-circle fs-5 me-2"></i> <?php echo e(Auth::guard('customer')->user()->name); ?>

                </a>
                <?php else: ?>
                <a href="<?php echo e(route('customer.login')); ?>" class="gani-sidebar-user">
                    <i class="fa-regular fa-user-circle fs-5 me-2"></i> লগইন / রেজিস্টার
                </a>
                <?php endif; ?>
                <hr class="my-2" />
                <nav class="gani-sidebar-nav">
                    <a href="<?php echo e(route('home')); ?>" class="gani-sidebar-link"><i class="fa-solid fa-home me-2"></i> হোম</a>
                    <a href="<?php echo e(route('shop')); ?>" class="gani-sidebar-link"><i class="fa-solid fa-store me-2"></i> সকল পণ্য</a>
                    <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="gani-sidebar-accordion">
                        <button class="gani-sidebar-acc-btn">
                            <?php echo e($cat->name); ?>

                            <?php if($cat->subcategories->count() > 0): ?>
                            <i class="fa-solid fa-chevron-down"></i>
                            <?php endif; ?>
                        </button>
                        <?php if($cat->subcategories->count() > 0): ?>
                        <div class="gani-sidebar-acc-content">
                            <?php $__currentLoopData = $cat->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(url('subcategory/'.$sub->slug)); ?>" class="gani-sidebar-sub-link"><?php echo e($sub->subcategoryName); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('customer.order_track')); ?>" class="gani-sidebar-link"><i class="fa-solid fa-truck me-2"></i> অর্ডার ট্র্যাক</a>
                </nav>
            </div>
        </div>

        
        <div class="mobile-menu" style="display:none !important;">
            <div class="mobile-menu-logo">
                <div class="logo-image">
                    <img src="<?php echo e(asset($generalsetting->white_logo)); ?>" alt="" />
                </div>
                <div class="mobile-menu-close">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <ul class="first-nav">
                <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="parent-category">
                    <a href="<?php echo e(url('category/'.$scategory->slug)); ?>" class="menu-category-name">
                        <img src="<?php echo e(asset($scategory->image)); ?>" alt="" class="side_cat_img" />
                        <?php echo e($scategory->name); ?>

                    </a>
                    <?php if($scategory->subcategories->count() > 0): ?>
                    <span class="menu-category-toggle">
                        <i class="fa fa-chevron-down"></i>
                    </span>
                    <?php endif; ?>
                    <ul class="second-nav" style="display: none;">
                        <?php $__currentLoopData = $scategory->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="parent-subcategory">
                            <a href="<?php echo e(url('subcategory/'.$subcategory->slug)); ?>" class="menu-subcategory-name"><?php echo e($subcategory->subcategoryName); ?></a>
                            <?php if($subcategory->childcategories->count() > 0): ?>
                            <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                            <?php endif; ?>
                            <ul class="third-nav" style="display: none;">
                                <?php $__currentLoopData = $subcategory->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="childcategory"><a href="<?php echo e(url('products/'.$childcat->slug)); ?>" class="menu-childcategory-name"><?php echo e($childcat->childcategoryName); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div id="content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
            <!-- content end -->
        <footer>
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <div class="footer-about">
                                <a href="<?php echo e(route('home')); ?>">
                                    <img src="<?php echo e(asset($generalsetting->white_logo)); ?>" alt="" />
                                </a>
                                <p><?php echo e($contact->address); ?></p>
                                <a href="tel:<?php echo e($contact->hotline); ?>" class="footer-hotlint"><?php echo e($contact->hotline); ?></a>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-3 mb-3 mb-sm-0 col-6">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title"><a>Useful Link</a></li>
                                    <li>
                                        <a href="<?php echo e(route('contact')); ?>"> Contact Us</a>
                                    </li>
                                    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><a href="<?php echo e(route('page',['slug'=>$page->slug])); ?>"><?php echo e($page->name); ?></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-2 mb-3 mb-sm-0 col-6">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title"><a>Link</a></li>
                                     <li>
                                        <a href="<?php echo e(route('shop')); ?>">All Products</a>
                                    </li>
                                    <?php $__currentLoopData = $pagesright; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('page',['slug'=>$value->slug])); ?>"><?php echo e($value->name); ?></a>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                    <?php $__currentLoopData = $socialicons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="social_list">
                                        <a class="mobile-social-link" href="<?php echo e($value->link); ?>"><i class="<?php echo e($value->icon); ?>"></i></a>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                <p>© <?php echo e(date('Y')); ?> <?php echo e($generalsetting->name); ?>. All Rights Reserved. | <span style="color: white;">Developed By : <a href="https://www.hostmcw.com"><span style="color: white;">HOSTMCW</span></a></span></p>
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
                    <a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', $contact->whatsapp)); ?>">
                        <span>
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <span>Whatsapp</span>
                    </a>
                </li>

                <li class="mobile_home">
                    <a href="<?php echo e(route('home')); ?>">
                        <span><i class="fa-solid fa-home"></i></span> <span>Home</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('customer.checkout')); ?>">
                        <span>
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <span>Cart (<b class="mobilecart-qty"><?php echo e(Cart::instance('shopping')->count()); ?></b>)</span>
                    </a>
                </li>
                <?php if(Auth::guard('customer')->user()): ?>
                <li>
                    <a href="<?php echo e(route('customer.account')); ?>">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Account</span>
                    </a>
                </li>
                <?php else: ?>
                <li>
                    <a href="<?php echo e(route('customer.login')); ?>">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Login</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', $contact->whatsapp)); ?>?text=Hello, I would like to inquire about..." target="_blank" class="whatsapp-float">
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

        <script src="<?php echo e(asset('frontEnd/js/jquery-3.6.3.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontEnd/js/bootstrap.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontEnd/js/owl.carousel.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontEnd/js/mobile-menu.js')); ?>"></script>
        <script src="<?php echo e(asset('frontEnd/js/mobile-menu-init.js')); ?>"></script>
        <script src="<?php echo e(asset('frontEnd/js/wow.min.js')); ?>"></script>
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
        <script src="<?php echo e(asset('backEnd/assets/js/toastr.min.js')); ?>"></script>
        <?php echo Toastr::message(); ?> <?php echo $__env->yieldPushContent('script'); ?>
        <script>
            $(".quick_view").on("click", function () {
                var id = $(this).data("id");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "<?php echo e(route('quickview')); ?>",
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
                        url: "<?php echo e(url('add-to-cart')); ?>/" + id + "/" + qty,
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
                        url: "<?php echo e(route('cart.store')); ?>",
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
                        url: "<?php echo e(route('cart.remove')); ?>",
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
                        url: "<?php echo e(route('cart.increment')); ?>",
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
                        url: "<?php echo e(route('cart.decrement')); ?>",
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
                    url: "<?php echo e(route('cart.count')); ?>",
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
                    url: "<?php echo e(route('mobile.cart.count')); ?>",
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
                    url: "<?php echo e(route('shipping.charge')); ?>",
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
                    url: "<?php echo e(route('livesearch')); ?>",
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
                    url: "<?php echo e(route('livesearch')); ?>",
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
            $(".district").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "<?php echo e(route('districts')); ?>",
                    success: function (res) {
                        if (res) {
                            $(".area").empty();
                            $(".area").append('<option value="">Select..</option>');
                            $.each(res, function (key, value) {
                                $(".area").append('<option value="' + key + '" >' + value + "</option>");
                            });
                        } else {
                            $(".area").empty();
                        }
                    },
                });
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
        </script>

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($gtm->code); ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    </body>
</html>
<?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/frontEnd/layouts/master.blade.php ENDPATH**/ ?>