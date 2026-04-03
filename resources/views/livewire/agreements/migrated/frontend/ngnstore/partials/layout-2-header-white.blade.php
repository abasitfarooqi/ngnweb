<link rel="stylesheet" type="text/css" href="/assets/css/layout-2-header-white-forced.css">
<!-- Header -->
<header id="header" class="header clearfix homepagex forced-whited">
    <style>

    </style>
    <!-- Start Top Nav -->
    <nav class="navbar navbar-expand navbar-dark bg-dark d-none d-lg-block forced-whited  pt-1 pb-1"
        style="background-color: #rgb(197, 35, 45);">
        <div class="container container-width-93">
            <div class="w-100 d-flex justify-content-between">
                <div>
                    <i class="fa fa-envelope mx-2" style="color:black;"></i>
                    <a style="color: black;" class="navbar-sm-brand text-dark text-decoration-none"
                        href="mailto:customerservice@neguinhomotors.co.uk" target="_newtab"
                        onmouseover="this.style.color='#f63440'" onMouseOut="this.style.color='#fff'">Customer
                        Service</a>
                    <i class="fa fa-phone mx-2" style="color:black;"></i>
                    <a style="color: black;" class="navbar-sm-brand text-dark text-decoration-none"
                        href="tel:02083141498" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Catford: 0208 314 1498</a>

                    <i class="fa fa-phone mx-2" style="color:black;"></i>
                    <a style="color: black;" class="navbar-sm-brand text-dark text-decoration-none"
                        href="tel:02084129275" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Sutton: 0208 412 9275</a>

                    <i class="fa fa-phone mx-2" style="color:black;"></i>
                    <a style="color: black;" class="navbar-sm-brand text-dark text-decoration-none"
                        href="tel:02034095478" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Tooting: 0203 409 5478</a>
                    <i class="fa fa-bell mx-2" style="color:black;"></i>
                    <i class="fa fa-briefcase mx-2" style="color:white;"></i>
                    <a style="color: white;" class="navbar-sm-brand text-light text-decoration-none"
                        href="{{ url('/career') }}" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Career</a>
                </div>

                <div>
                    {{-- @if (Route::has('login'))

                        @auth

                            <a class="navbar-sm-brand text-dark text-decoration-none" href="{{ url('/dashboard') }}"
                                style="color:black;">Dashboard</a>
                        @else
                            <a class="navbar-sm-brand text-dark text-decoration-none" href="{{ route('login') }}"
                                style="color:black;">Log in </a>/

                            @if (Route::has('register'))
                                <a class="navbar-sm-brand text-dark text-decoration-none" href="{{ route('register') }}"
                                    style="color:black;">Register</a>
                            @endif

                        @endauth

                    @endif --}}
                </div>

            </div>
        </div>
    </nav>
    <!-- Close Top Nav -->

    <div class="container container-width-93 clearfix homepagex forced-whited" id="site-header-inner">
        <div id="logo" class="logo float-left image-responsive col-lg-2 col-md-4 col-sm-4 col-xs-4">
            <a href="/" title="logo" class="logo">
                <img src="{{ url('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN" width="55%"
                    height="24" data-retina="{{ url('img/ngn-motor-logo-fit-optimized.png') }}" data-width="55%"
                    data-height="24">
            </a>
        </div><!-- /.logo -->

        <!-- Added search bar directly into the second column -->
        <div class="header-search-bar d-none d-lg-flex">
            <form role="search" method="get" class="header-search-form" action="{{ route('ngn_search_results') }}">
                <input type="text" value="{{ request('query') }}" name="query" class="header-search-field"
                    placeholder="Search...">
                <button type="submit" class="header-search-submit " title="Search">Search</button>
            </form>
        </div><!-- /.header-search-bar -->

        <div class="mobile-button"><span></span></div>

        <div class="menu-extra">
            <ul>
                <!-- Clickable search icon, visible on small screens -->
                <!-- <li class="box-search d-lg-none">
                    <a class="icon_search header-search-icon" href="#"></a>
                    <form role="search" method="get" class="header-search-form"
                        action="{{ route('ngn_search_results') }}">
                        <input type="text" value="{{ request('query') }}" name="query" class="header-search-field"
                            placeholder="Search...">
                        <button type="submit" class="header-search-submit" title="Search">Search</button>
                    </form>
                </li>
                <li class="box-cart nav-top-cart-wrapper">
                    <a class="icon_cart nav-cart-trigger" href="{{ route('product.cart') }}"><span></span></a>
                </li> -->
            </ul>
        </div><!-- /.menu-extra -->

    </div><!-- /.container-fluid -->
    <div class="clearfix"></div>
    <div class="container container-width-93 p-0">
        <div class="nav-wrap">
            <nav id="mainnav" class="mainnav hlayout-2">
                <ul class="menu left-menu">
                    <li>
                        <a href="{{ route('sale-motorcycles') }}">SALES</a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('motorcycles.new') }}">Motorcycles</a>
                            </li>
                            <li>
                                <a href="{{ route('motorcycles.used') }}">USED MOTORCYCLES</a>
                            </li>
                            <li>
                                <a href="{{ url('/finance') }}">FINANCE</a>
                            </li>
                            <li>
                                <a href="{{ route('road-traffic-accidents') }}">ACCIDENT MANAGEMENT</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('rental-hire') }}">RENTALS</a>
                    </li>
                    <li>
                        <a href="{{ route('services') }}">SERVICES</a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('road-traffic-accidents') }}">ACCIDENT MANAGEMENT</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('shop-motorcycle') }}">SHOP</a>
                        <ul class="submenu">
                            <li>
                                <a href="/helmets">ACCESSORIES</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">PHONE & DEVICE MOUNTS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">COVERS</a>
                                    </li>
                                    <li>
                                        <a href="/category/3">HANDLEBAR ACCESSORIES</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">PHONES</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">CLOCKS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">BAR ENDS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">GRIPS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">HEATED GRIPS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">MIRRORS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">MUFFS</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/26">BATTERY CARE & POWER</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">SCOOT STUFF</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">WORKSHOP</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">HELMET ACCESSORIES</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">LIGHTING</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">PAINT PROTECTION</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">TRAVEL & TRANSPORTATION</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">TYRES & WHEEL CARE</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">EYE WEAR</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/helmets">HELMETS</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">MT HELMETS</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">FULL FACE</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">OPEN FACE</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">VISORS & PINLOCK</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">FLIP FRONT</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">MT ACCESSORIES</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">GOGGLES</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">ADVENTURE</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">SIMPONS</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">FULL FACE</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">FLIP FRONT</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">ACCESSORIES</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">ALPINESTARS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">BOX</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">VISORS & PINLOCK</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">HJC</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">ADVENTURE</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">SMART HJC</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">FLIP FRONT</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">FULL FACE</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">MOTOR X</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">OPEN FACE</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">VISORS & PINLOCK</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">SPARES</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/helmets">SECURITY</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">LEVER LOCKS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">CHAIN LOCKS & CHAINS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">ANCHORS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">CABLE LOCKS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">DISC LOCKS & PADLOCKS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">U LOCKS</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/helmets">LUGGAGE</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">HELMET & BOOT CARRIERS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">PANNIERS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">LUGGAGE ACCESSORIES</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">BACKPACKS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">TAIL PACKS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">TANK BAGS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">WAIST AND LEG BAGS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">TOP BOXES</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">SAT NAV HOLDER</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/helmets">CLOTHING</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">ARMR</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">TEXTILE JACKETS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">LEATHER JACKETS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">ARMR ACCESSORIES</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">DOJO</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">BOOTS</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">ALPINESTARS</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">SUITS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">JACKETS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">TROUSERS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">GLOVES</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">FOOTWEAR</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">MERLIN</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/category/10">JACKETS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">TROUSERS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">GLOVES</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">BOOTS</a>
                                            </li>
                                            <li>
                                                <a href="/category/10">KOMINE</a>
                                                <ul class="submenu">
                                                    <li>
                                                        <a href="/category/10">BOOTS</a>
                                                    </li>
                                                    <li>
                                                        <a href="/category/10">GLOVES</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/10">OVERSUITS</a>
                                    </li>
                                    <li>
                                        <a href="/category/10">PROTECTION</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/helmets">TYRES</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/category/10">ALPINESTARS</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="menu right-menu">
                    <li>
                        <a href="{{ route('about.page') }}">ABOUT</a>
                    </li>
                    <li>
                        <a href="{{ route('contact.me') }}">CONTACT</a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('login') }}">LOGIN</a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}">REGISTER</a>
                            </li>
                        </ul>
                    </li>
                    {{-- <li class="rounded-lg border-gray-500" style="border:0.5px">
                        <a href="/notify-mottax" class="text-gray-500">FREE MOT / TAX ALERT</a>
                    </li> --}}

                    <li class="rounded-lg border-gray-500" style="border:0.5px">
                        <a href="{{ route('ngnclub.subscribe') }}" class="text-gray-500"><i class="fa fa-star"
                                style="color:#eeb407;"></i> JOIN NGN CLUB</a>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</header><!-- /header -->
