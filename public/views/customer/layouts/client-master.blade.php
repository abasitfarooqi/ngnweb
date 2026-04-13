<!doctype html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1RE49QH35E"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-1RE49QH35E');
    </script>

    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
    <title>NGN</title>

    <meta name="author" content="Emmanuel Nwokedi">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
    @vite(['resources/css/app.css', 'resources/css/style.css'])
    {{-- all40 --}}
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">

    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}

    <!--[if lt IE 9]>
        <script src="javascript/html5shiv.js"></script>
        <script src="javascript/respond.min.js"></script>
    <![endif]-->
    {{--
    <script src="//widget.simplybook.it/v2/widget/widget.js"></script>
    <script>
        var widget = new SimplybookWidget({
            "widget_type": "button",
            "url": "https:\/\/neguinhomotorslimited.simplybook.it",
            "theme": "simple_beauty_theme",
            "theme_settings": {
                "sb_base_color": "#cd3232",
                "header_color": "#ffffff",
                "timeline_hide_unavailable": "1",
                "hide_past_days": "0",
                "timeline_show_end_time": "0",
                "timeline_modern_display": "as_slots",
                "display_item_mode": "list",
                "body_bg_color": "#ffffff",
                "sb_review_image": "",
                "dark_font_color": "#474747",
                "light_font_color": "#ffffff",
                "sb_company_label_color": "#333333",
                "hide_img_mode": "1",
                "show_sidebar": "1",
                "sb_busy": "#dad2ce",
                "sb_available": "#d3e0f1"
            },
            "timeline": "modern",
            "datepicker": "top_calendar",
            "is_rtl": false,
            "app_config": {
                "clear_session": 0,
                "allow_switch_to_ada": 0,
                "predefined": []
            },
            "button_title": "BOOK SERVICE OR MOT",
            "button_background_color": "#cd3232",
            "button_text_color": "#ffffff",
            "button_position": "right",
            "button_position_offset": "55%"
        });
    </script> --}}
    @vite('resources/js/app.js')
</head>

<header id="header" class="header clearfix">

    <!-- Start Top Nav -->
    <nav class="navbar navbar-expand navbar-dark bg-dark d-none d-lg-block" style="background-color: #101111;">
        <div class="container">
            <div class="w-100 d-flex justify-content-between">
                <div>
                    <i class="fa fa-envelope mx-2" style="color:white;"></i>
                    <a style="color: white;" class="navbar-sm-brand text-light text-decoration-none"
                        href="mailto:customerservice@neguinhomotors.co.uk" target="_newtab"
                        onmouseover="this.style.color='#f63440'" onMouseOut="this.style.color='#fff'">Customer
                        Service</a>
                    <i class="fa fa-phone mx-2" style="color:white;"></i>
                    <a style="color: white;" class="navbar-sm-brand text-light text-decoration-none"
                        href="tel:02083141498" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Catford: 0208 314 1498</a>
                    <i class="fa fa-phone mx-2" style="color:white;"></i>
                    <a style="color: white;" class="navbar-sm-brand text-light text-decoration-none"
                        href="tel:02034095478" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Tooting: 0203 409 5478</a>

                </div>

                @auth
                    <div>
                        <i class="fa fa-user mx-2" style="color:white;"></i>
                        <a style="color: white;" class="text-light" style="padding-right: 5px;" href="/dashboard"
                            onmouseover="this.style.color='#f63440'" onMouseOut="this.style.color='#fff'">Welcome
                            {{ auth()->user()->first_name }}</a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <div>
                            <a style="color: white;" class="text-light" style="padding-right: 5px;"
                                href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                        </div>
                    </form>
                @endauth

                @guest
                    <div>
                        <a style="color: white;" class="text-light" style="padding-right: 5px;" href="{{ route('login') }}"
                            onmouseover="this.style.color='#f63440'" onMouseOut="this.style.color='#fff'">Login </a> /
                        <a style="color: white;" class="text-light" style="padding-right: 5px;"
                            href="{{ route('register') }}" onmouseover="this.style.color='#f63440'"
                            onMouseOut="this.style.color='#fff'">Register</a>
                    </div>
                @endguest
            </div>
        </div>
    </nav>
    <!-- Close Top Nav -->

    <div class="container-fluid container-width-93 clearfix" id="site-header-inner">
        <div id="logo" class="logo float-left image-responsive col-sm-3 col-md-4">
            <a href="/" title="logo" class="logo">
                <img src="{{ url('img/ngn-motor-logo-fit-small.png') }}" alt="Neguinho Motors" width="70%"
                    height="24" data-retina="{{ url('img/ngn-motor-logo-fit-small.png') }}" data-width="70%"
                    data-height="24">
            </a>
        </div><!-- /.logo -->
        <div class="mobile-button"><span></span></div>
        <ul class="menu-extra">
            <!-- <li class="box-search">
                <a class="icon_search header-search-icon" href="#"></a>
                <form role="search" method="get" class="header-search-form" action="#">
                    <input type="text" value="" name="search" class="header-search-field"
                        placeholder="Search...">
                    <button type="submit" class="header-search-submit" title="Search">Search</button>
                </form>
            </li> -->
            <!-- <li class="box-cart nav-top-cart-wrapper">
                <a class="icon_cart nav-cart-trigger " href="/cart"><span> </span></a>
            </li> -->
        </ul>
        <div class="nav-wrap">
            <nav id="mainnav" class="mainnav">
                <ul class="menu">
                    <!-- <li>
                        <a href="/motorcycle-sales">SALES</a>
                        <ul class="submenu">
                            <li>
                                <a href="/new-motorcycles">NEW MOTORCYCLES</a>
                            </li>
                            <li>
                                <a href="/used-motorcycles">USED MOTORCYCLES</a>
                            </li>
                            <li>
                                <a href="/coming-soon">FINANCE</a>
                            </li>
                            <li>
                                <a href="/coming-soon">MOTORCYCLE INSURANCE</a>
                            </li>
                            <li>
                                <a href="/accident-management-services">ACCIDENT MANAGEMENT</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/motorcycle-rentals">RENTALS</a>
                    </li>
                    <li>
                        <a href="/services">SERVICES</a>
                        <ul class="submenu">
                            <li>
                                <a href="/accident-management-services">ACCIDENT MANAGEMENT</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/category/1">SHOP</a>
                        <ul class="submenu">
                            <li>
                                <a href="/category/1">HELMETS</a>
                            </li>
                            <li>
                                <a href="/category/14">HELMET ACCESSORIES</a>
                            </li>
                            <li>
                                <a href="/category/2">ESSENTIALS</a>
                            </li>
                            <li>
                                <a href="/category/11">CLOTHING</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">HEADWEAR</a>
                                    </li>
                                    <li>
                                        <a href="/category/3">HEATED CLOTHING</a>
                                    </li>
                                    <li>
                                        <a href="/category/26">OXFORD CLOTHING</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/category/6">LOCKS</a>
                            </li>
                            <li>
                                <a href="/category/7">PAINT PROTECTION</a>
                            </li>
                            <li>
                                <a href="/category/8">HANDLEBAR ACCESSORIES</a>
                            </li>
                            <li>
                                <a href="/category/9">REFLECTIVES</a>
                            </li>
                            <li>
                                <a href="/category/12">LIGHTING</a>
                            </li>
                            <li>
                                <a href="/category/13">LUGGAGE</a>
                            </li>
                            <li>
                                <a href="/category/15">BATTERY CARE</a>
                            </li>
                            <li>
                                <a href="/category/16">CYCLE ACCESSORIES</a>
                            </li>
                            <li>
                                <a href="/category/25">INTERCOMS</a>
                            </li>
                            <li>
                                <a href="/category/35">MINT</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/about">ABOUT</a>
                    </li>
                    <li>
                        <a href="/contact">CONTACT</a>
                    </li>-->
                </ul>
            </nav><!-- /.mainnav -->
        </div><!-- /.nav-wrap -->
    </div><!-- /.container-fluid -->
</header><!-- /header -->

<body>

    <main class="container">
        @yield('content')
    </main>

    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p class="copyright">Copyright @ 2023 <a href="/">Neguinho Motors Limited - All Rights
                            Reserved</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/tether.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/jquery.easing.js"></script>
    <script src="/assets/js/parallax.js"></script>
    <script src="/assets/js/jquery-waypoints.js"></script>
    <script src="/assets/js/jquery-countTo.js"></script>
    <script src="/assets/js/jquery.countdown.js"></script>
    <script src="/assets/js/jquery.flexslider-min.js"></script>
    <script src="/assets/js/images-loaded.js"></script>
    <script src="/assets/js/jquery.isotope.min.js"></script>
    <script src="/assets/js/magnific.popup.min.js"></script>
    <script src="/assets/js/jquery.hoverdir.js"></script>
    <script src="/assets/js/owl.carousel.min.js"></script>
    <script src="/assets/js/equalize.min.js"></script>
    <script src="/assets/js/gmap3.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIEU6OT3xqCksCetQeNLIPps6-AYrhq-s&region=GB"></script>

    <script src="/assets/js/jquery-ui.js"></script>

    <script src="/assets/js/jquery.cookie.js"></script>
    <script src="/assets/js/main.js"></script>

    <!-- Revolution Slider -->
    <script src="/assets/rev-slider/js/jquery.themepunch.tools.min.js"></script>
    <script src="/assets/rev-slider/js/jquery.themepunch.revolution.min.js"></script>
    <script src="/assets/js/rev-slider.js"></script>

    <!-- Load Extensions only on Local File Systems ! The following part can be removed on Server for On Demand Loading -->
    <script src="assets/rev-slider/js/extensions/revolution.extension.actions.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.carousel.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.kenburn.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.layeranimation.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.migration.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.navigation.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.parallax.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.slideanims.min.js"></script>
    <script src="assets/rev-slider/js/extensions/revolution.extension.video.min.js"></script>

    <!-- <script>
        const x = document.getElementById("amount");
        // let x = 5;
        let y = 3;
        let z = x * y;
        document.getElementById("rentalDeposit").innerHTML = z;
    </script> -->

</body>

</html>
