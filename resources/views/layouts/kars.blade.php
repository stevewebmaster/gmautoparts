<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'G&M Auto Parts') - Quality Used Car Parts | NZ</title>
    <meta name="description" content="@yield('meta_description', 'G&M Auto Parts - quality used automotive parts in New Zealand.')">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Play:wght@400;700&display=swap" rel="stylesheet">

    <!-- Kars Template CSS (all from /kars/css/) -->
    <link rel="stylesheet" href="/kars/css/bootstrap.min.css">
    <link rel="stylesheet" href="/kars/css/fontawesome.min.css">
    <link rel="stylesheet" href="/kars/css/magnific-popup.min.css">
    <link rel="stylesheet" href="/kars/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/kars/css/style.css">
    <style>
        .gm-site-logo {
            display: block;
            width: clamp(160px, 14vw, 240px);
            height: auto;
            max-width: 100%;
        }

        .sticky-wrapper.sticky .gm-site-logo {
            width: clamp(150px, 12vw, 220px);
        }

        .mobile-logo .gm-site-logo {
            width: clamp(140px, 46vw, 210px);
        }

        .logo-top .gm-site-logo {
            width: clamp(170px, 16vw, 250px);
        }
    </style>

    @livewireStyles
    @stack('head_styles')
</head>

<body class="">

    <!-- Preloader -->
    <div class="preloader">
        <button class="th-btn preloaderCls">Cancel Preloader</button>
        <div class="preloader-inner">
            <div class="loader"></div>
        </div>
    </div>

    <!-- Popup Search Box -->
    <div class="popup-search-box d-none d-lg-block">
        <button class="searchClose"><i class="fal fa-times"></i></button>
        <form action="{{ route('parts.index') }}" method="GET">
            <input type="text" name="keyword" placeholder="Search parts, make, model...">
            <button type="submit"><i class="fal fa-search"></i></button>
        </form>
    </div>

    <!-- Mobile Menu -->
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="mobile-logo">
                <a href="{{ route('home') }}"><img class="gm-site-logo" src="/gm-parts-logo.svg" alt="G&M Auto Parts"></a>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li class="{{ request()->routeIs('home') || request()->routeIs('home.v2') ? 'active' : '' }}">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="{{ request()->routeIs('about') ? 'active' : '' }}">
                        <a href="{{ route('about') }}">About Us</a>
                    </li>
                    <li class="{{ request()->routeIs('parts.*') ? 'active' : '' }}">
                        <a href="{{ route('parts.index') }}">Inventory</a>
                    </li>
                    <li class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                        <a href="{{ route('vehicles.index') }}">Now Dismantling</a>
                    </li>
                    <li class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                        <a href="{{ route('contact') }}">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="th-header header-default">
        <div class="header-top">
            <div class="th-container">
                <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-links">
                            <ul>
                                <li><i class="fal fa-location-dot"></i> <a href="https://www.google.com/maps/search/?api=1&query=2+Bruce+Berquist+Drive,+Te+Awamutu">2 Bruce Berquist Drive, Te Awamutu</a></li>
                                <li><i class="fa-regular fa-phone"></i> <a href="tel:+6478718575">(07) 871-8575</a></li>
                                <li><i class="fa-regular fa-fax"></i> <span>Fax: 871 8245</span></li>
                                <li><i class="fa-sharp fa-regular fa-envelope"></i> <a href="mailto:gmautospares@xtra.co.nz">gmautospares@xtra.co.nz</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="header-links">
                            <ul>
                                <li>
                                    <div class="social-links">
                                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-wrapper">
            <div class="menu-area">
                <div class="th-container">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="header-logo">
                                <a href="{{ route('home') }}"><img class="gm-site-logo" src="/gm-parts-logo.svg" alt="G&M Auto Parts"></a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <nav class="main-menu sapace-left d-none d-lg-inline-block">
                                <ul>
                                    <li class="{{ request()->routeIs('home') || request()->routeIs('home.v2') ? 'active' : '' }}">
                                        <a href="{{ route('home') }}">Home</a>
                                    </li>
                                    <li class="{{ request()->routeIs('about') ? 'active' : '' }}">
                                        <a href="{{ route('about') }}">About Us</a>
                                    </li>
                                    <li class="{{ request()->routeIs('parts.*') ? 'active' : '' }}">
                                        <a href="{{ route('parts.index') }}">Inventory</a>
                                    </li>
                                    <li class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                                        <a href="{{ route('vehicles.index') }}">Now Dismantling</a>
                                    </li>
                                    <li class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                                        <a href="{{ route('contact') }}">Contact Us</a>
                                    </li>
                                </ul>
                            </nav>
                            <button type="button" class="th-menu-toggle d-block d-lg-none"><i class="far fa-bars"></i></button>
                        </div>
                        <div class="col-auto d-none d-xl-block">
                            <div class="header-button">
                                <button type="button" class="simple-icon searchBoxToggler"><i class="far fa-search"></i></button>
                                <a href="{{ route('parts.index') }}" class="th-btn">Browse Parts <i class="fa-solid fa-circle-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="footer-wrapper footer-default bg-footer-color">
        <div class="footer-top">
            <div class="container">
                <div class="footer-top-border">
                    <div class="row gy-4 justify-content-between align-items-center">
                        <div class="col-lg-3">
                            <div class="logo-top">
                                <a href="{{ route('home') }}"><img class="gm-site-logo" src="/gm-parts-logo.svg" alt="G&M Auto Parts"></a>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="payment-wrap">
                                <div class="info">
                                    <h4 class="text-white">Quality Used Parts</h4>
                                    <h6>Serving New Zealand with quality auto parts</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-area">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-md-6 col-xl-3">
                        <div class="widget footer-widget">
                            <h3 class="widget_title">About G&M Auto Parts</h3>
                            <div class="th-widget-about">
                                <p class="about-text">Quality used automotive parts from dismantled vehicles. Browse our catalogue to find what you need at fair prices across New Zealand.</p>
                                <div class="footer-call-wrap">
                                    <div class="info-box">
                                        <div class="info-contnt">
                                            <h4 class="footer-info-title">Visit Us:</h4>
                                            <p class="info-box_text">2 Bruce Berquist Drive, Te Awamutu</p>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-contnt">
                                            <h4 class="footer-info-title">Phone / Fax:</h4>
                                            <p class="info-box_text">
                                                <a href="tel:+6478718575" class="info-box_link">(07) 871-8575</a><br>
                                                <span>Fax: 871 8245</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-contnt">
                                            <h4 class="footer-info-title">Email:</h4>
                                            <p class="info-box_text">
                                                <a href="mailto:gmautospares@xtra.co.nz" class="info-box_link">gmautospares@xtra.co.nz</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="th-social">
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-auto">
                        <div class="widget widget_nav_menu footer-widget">
                            <h3 class="widget_title">Quick Links</h3>
                            <div class="menu-all-pages-container">
                                <ul class="menu">
                                    <li><a href="{{ route('about') }}">About Us</a></li>
                                    <li><a href="{{ route('parts.index') }}">Browse Parts</a></li>
                                    <li><a href="{{ route('vehicles.index') }}">Now Dismantling</a></li>
                                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="row gy-2 align-items-center">
                    <div class="col-md-12">
                        <p class="copyright-text text-center">Copyright <i class="fal fa-copyright"></i> {{ date('Y') }} <a href="{{ route('home') }}">G&M Auto Parts</a>. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll To Top -->
    <div class="scroll-top">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
        </svg>
    </div>

    <!-- Kars Template JS -->
    <script src="/kars/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="/kars/js/swiper-bundle.min.js"></script>
    <script src="/kars/js/bootstrap.min.js"></script>
    <script src="/kars/js/jquery.magnific-popup.min.js"></script>
    <script src="/kars/js/jquery.counterup.min.js"></script>
    <script src="/kars/js/tilt.jquery.min.js"></script>
    <script src="/kars/js/gsap.min.js"></script>
    <script src="/kars/js/imagesloaded.pkgd.min.js"></script>
    <script src="/kars/js/isotope.pkgd.min.js"></script>
    <script src="/kars/js/jquery-ui.min.js"></script>
    <script src="/kars/js/nice-select.min.js"></script>
    <script src="/kars/js/main.js"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
