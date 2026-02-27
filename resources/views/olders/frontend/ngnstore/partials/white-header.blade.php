<link rel="stylesheet" type="text/css" href="/assets/css/white-header-ngn.css">
<link rel="stylesheet" type="text/css" href="/assets/css/header-white-forced.css">
<style>

</style>
<!-- Header -->
<header id="header" class="header clearfix homepagex ">

    <!-- Start Top Nav -->
    <nav class="navbar navbar-expand navbar-dark bg-dark d-none d-lg-block "
        style="background-color: #rgb(197, 35, 45);">
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
                        href="tel:02084129275" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Sutton: 0208 412 9275</a>
                    <i class="fa fa-phone mx-2" style="color:white;"></i>
                    <a style="color: white;" class="navbar-sm-brand text-light text-decoration-none"
                        href="tel:02034095478" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Tooting: 0203 409 5478</a>

                    <i class="fa fa-briefcase mx-2" style="color:white;"></i>
                    <a style="color: white;" class="navbar-sm-brand text-light text-decoration-none"
                        href="{{ url('/career') }}" onmouseover="this.style.color='#f63440'"
                        onMouseOut="this.style.color='#fff'">Career</a>

                </div>

                <div>
             

                    <!-- resources/views/frontend/ngnstore/partials/white-header.blade.php -->
                    <div class="navbar-auth d-flex ">
                        @auth('customer')
                            <span class="welcome-message active-white me-2">Welcome,
                                {{ Auth::guard('customer')->user()->customer->first_name }}
                                {{ Auth::guard('customer')->user()->customer->last_name }}</span>

                            <div class="dropdown me-2">
                                <button class="btn-sm btn-light dropdown-toggle"
                                    style="padding: 0px 5px !important;margin-top: -4px;background: white;" type="button" id="accountDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    My Account
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                                    <li><a class="dropdown-item" href="{{ url('accountinformation/') }}">Account
                                            Information</a></li>
                                    <li><a class="dropdown-item" href="{{ url('accountinformation/profile') }}">Profile</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ url('accountinformation/orders') }}">Orders</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ url('accountinformation/addresses') }}">Addresses</a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ url('accountinformation/payment-methods') }}">Payment Methods</a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ url('accountinformation/change-password') }}">Change Password</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href=""
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fa fa-sign-out mx-1"></i> Logout
                                        </a></li>
                                </ul>
                            </div>
                            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a class="navbar-sm-brand text-light text-decoration-none me-2"
                                href="{{ route('customer.login') }}">
                                <i class="fa fa-sign-in mx-1"></i> Login
                            </a>
                            <span class="active-color"> |</span>
                            <a class="navbar-sm-brand text-light text-decoration-none me-2"
                                href="{{ route('customer.register') }}">
                                <i class="fa fa-user-plus mx-1"></i> Register
                            </a>
                        @endauth
                    </div>




                    <!-- <a class="navbar-sm-brand text-light text-decoration-none font-two" href="/accountinformation/login">
                        <i class="fa fa-sign-in mx-1"></i> LOGIN
                    </a>
                    |
                    <a class="navbar-sm-brand text-light text-decoration-none font-two ml-2" href="/accountinformation/register">
                        <i class="fa fa-user-plus mx-1"></i> REGISTER
                    </a> -->

                    <!-- Right Side: AuthDropdown Component -->
                    <!-- Integrate AuthDropdown Vue component -->
                    <!-- Mounts the AuthDropdown Vue component -->


                </div>

            </div>
        </div>
    </nav>
    <!-- Close Top Nav -->

    <div class="container clearfix homepagex forced-whited" id="site-header-inner">
        <div id="logo" class="logo float-left image-responsive col-lg-2  col-md-3 col-sm-4 col-xs-4">

       
            <a href="/" title="logo" class="logo">
                <img src="{{ url('img/ngn-motor-logo-fit-small.png') }}" alt="NGN" width="55%" height="24"
                    data-retina="{{ url('img/ngn-motor-logo-fit-small.png') }}" data-width="55%" data-height="24">
            </a>
        </div><!-- /.logo -->
        <div class="mobile-button"><span></span></div>


        <ul class="menu-extra">
            <li class="box-search" style="display:none !important;">
                <a class="icon_search header-search-icon" href="#"></a>
                <form role="search" method="get" class="header-search-form" action="{{ route('ngn_search_results') }}">
                    <input type="text" value="{{ request('query') }}" name="query" class="header-search-field"
                        placeholder="Search...">
                    <button type="submit" class="header-search-submit" title="Search">Search</button>
                </form>
            </li>
            <li class="navbar-auth d-flex d-none">
                @auth('customer')
                    <div class="">
                        <button class="" style="padding: 0px 5px !important; margin-top: -4px;background: transparent;" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user mx-1"></i> 
                        </button>
                        <ul class="dropdown-menu dropdown-myaccount" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item dropdown-myaccount-item" href="{{ url('accountinformation/') }}"><i class="fa fa-info-circle mx-1 icons-width"></i> Account Information</a></li>
                            <li><a class="dropdown-item dropdown-myaccount-item" href="{{ url('accountinformation/profile') }}"><i class="fa fa-user mx-1 icons-width"></i> Profile</a></li>
                            <li><a class="dropdown-item dropdown-myaccount-item" href="{{ url('accountinformation/orders') }}"><i class="fa fa-list mx-1 icons-width"></i> Orders</a></li>
                            <li><a class="dropdown-item dropdown-myaccount-item" href="{{ url('accountinformation/addresses') }}"><i class="fa fa-home mx-1 icons-width"></i> Addresses</a></li>
                            <li><a class="dropdown-item dropdown-myaccount-item" href="{{ url('accountinformation/payment-methods') }}"><i class="fa fa-credit-card mx-1 icons-width"></i> Payment Methods</a></li>
                            <li><a class="dropdown-item dropdown-myaccount-item" href="{{ url('accountinformation/change-password') }}"><i class="fa fa-lock mx-1 icons-width"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item dropdown-myaccount-item" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out mx-1 icons-width"></i> Logout
                            </a></li>
                        </ul>
                    </div>
                    <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a class="navbar-sm-brand text-dark text-decoration-none me-2" href="{{ route('customer.login') }}">
                        <i class="fa fa-user mx-1"></i>
                    </a>
                @endauth
            </li>
            
            <li>
            
                <a href="{{ route('product.cart') }}" class="effect-on-btn btn-shape ngn-bg view-cart-btn-x"> <i class="fa fa-shopping-bag mx-1"></i> View
                    Cart</a>
            </li>
            <!-- <li class="box-cart nav-top-cart-wrapper dropdown" style="list-style: none;">
                <a class="icon_cart nav-cart-trigger dropdown-toggle" href="#" id="cartDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false" class="cart-dropdown-toggle">
                    <span></span>
                    <span id="cart-count" class="cart-count">Cart: 0 items</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="cartDropdown" id="cart-dropdown-menu"
                    class="cart-dropdown-menu">
                    <div id="cart-items-container" class="cart-items-container">
                         Cart items will be dynamically inserted here
                    </div>
                    <li>
                       
                            <a href="{{ route('product.cart') }}" class="effect-on-btn btn-shape ngn-bg view-cart-btn-x">View Cart</a>
                       
                    </li>
                </ul>
            </li> -->
        </ul>
        <div class="nav-wrap">
            <nav id="mainnav" class="mainnav">
                <ul class="menu">
                    <li>
                        <a href="{{ route('motorcycle.delivery') }}">MOTORCYCLE RECOVERY</a>
                    </li>
                    <li> 
                        <!-- <img src="{{ url('assets/images/services/book-mot.png') }}" alt="mot booking" class="" style="width: 30px"> -->
                        <a href="{{ route('services') }}#mot-booking-section">MOT</a>
                    </li>
                    <li>
                        <a href="{{ route('ebike.landing') }}">E-BIKES</a>
                    </li>
                    <li>
                        <a href="{{ route('sale-motorcycles') }}">SALES</a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('motorcycles.new') }}">MOTORCYCLES FOR SALE</a>
                            </li>
                            <li>
                                <a href="{{ route('motorcycles.used') }}">USED MOTORCYCLES</a>
                            </li>
                           <li>
                                <a href="{{ url('/finance') }}">FINANCE</a>
                            </li>
                            
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('rental-hire') }}">RENTALS</a>
                    </li>
                    <li>
                        <a href="{{ route('all-services') }}">SERVICES</a>
                    </li>
                    <!-- <li>
                        <a href="{{ route('services') }}">SERVICES</a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('road-traffic-accidents') }}">ACCIDENT MANAGEMENT</a>
                            </li>
                            <li>
                                <a href="{{ url('/finance') }}">FINANCE</a>
                            </li>
                            <li>
                                <a href="{{ route('repairs.comparison') }}">MOTORBIKE SERVICES</a>
                                <ul class="submenu">
                                    
                                    <li>
                                        <a href="{{ route('repairs.comparison') }}">Service Comparison</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('repairs.basic') }}">Basic Service</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('repairs.major') }}">Full Service</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('repairs.repair') }}">MOTORBIKE REPAIRS</a>
                            </li>
                        </ul>
                    </li> -->
                    <li>
                        <a href="{{ route('shop-motorcycle') }}">SHOP</a>
                        <ul class="submenu">
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=accessories">ACCESSORIES</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=phone-&-device-mounts">PHONE
                                            & DEVICE MOUNTS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=covers">COVERS</a>
                                    </li>
                                    <li>
                                        <a href="/category/3">HANDLEBAR ACCESSORIES</a>
                                        <ul class="submenu">
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=phones">PHONES</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=clocks">CLOCKS</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=bar-ends">BAR
                                                    ENDS</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=grips">GRIPS</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=heated-grips">HEATED
                                                    GRIPS</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=mirrors">MIRRORS</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=muffs">MUFFS</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/category/26">BATTERY CARE & POWER</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=scoot-stuff">SCOOT
                                            STUFF</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=workshop">WORKSHOP</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=helmet-accessories">HELMET
                                            ACCESSORIES</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=lighting">LIGHTING</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=paint-protection">PAINT
                                            PROTECTION</a>
                                    </li>
                                    <li>
                                        <a
                                            href="/shop?page=1&per_page=12&sort=newest&categories=travel-&-transportation">TRAVEL
                                            & TRANSPORTATION</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=tyres-&-wheel-care">TYRES
                                            & WHEEL CARE</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=eye-wear">EYE WEAR</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=helmets">HELMETS</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=mt-helmets">MT
                                            HELMETS</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=full-face">FULL
                                                    FACE</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=open-face">OPEN
                                                    FACE</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=visors-&-pinlock">VISORS
                                                    & PINLOCK</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=flip-front">FLIP
                                                    FRONT</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=mt-accessories">MT
                                                    ACCESSORIES</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=goggles">GOGGLES</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=adventure">ADVENTURE</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=simpons">SIMPONS</a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=full-face">FULL
                                                    FACE</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=flip-front">FLIP
                                                    FRONT</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=accessories">ACCESSORIES</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a
                                            href="/shop?page=1&per_page=12&sort=newest&categories=alpinestars">ALPINESTARS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=box">BOX</a>
                                        <ul class="submenu">
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=visors-&-pinlock">VISORS
                                                    & PINLOCK</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=hjc">HJC</a>
                                        <ul class="submenu">
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=adventure">ADVENTURE</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=smart-hjc">SMART
                                                    HJC</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=flip-front">FLIP
                                                    FRONT</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=full-face">FULL
                                                    FACE</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=motor-x">MOTOR
                                                    X</a>
                                            </li>
                                            <li>
                                                <a href="/shop?page=1&per_page=12&sort=newest&categories=open-face">OPEN
                                                    FACE</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=visors-&-pinlock">VISORS
                                                    & PINLOCK</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/shop?page=1&per_page=12&sort=newest&categories=spares">SPARES</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=security">SECURITY</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=lever-locks">LEVER
                                            LOCKS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=chain-locks-&-chains">CHAIN
                                            LOCKS & CHAINS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=anchors">ANCHORS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=cable-locks">CABLE
                                            LOCKS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=disc-locks-&-padlocks">DISC
                                            LOCKS & PADLOCKS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=u-locks">U LOCKS</a>
                                    </li>
                                </ul>
                            </li>
                    </li>
                    <li>
                        <a href="/shop?page=1&per_page=12&sort=newest&categories=luggage">LUGGAGE</a>
                        <ul class="submenu">
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=helmet-&-boot-carriers">HELMET
                                    & BOOT CARRIERS</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=panniers">PANNIERS</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=luggage-accessories">LUGGAGE
                                    ACCESSORIES</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=backpacks">BACKPACKS</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=tail-packs">TAIL PACKS</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=tank-bags">TANK BAGS</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=waist-and-leg-bags">WAIST AND
                                    LEG BAGS</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=top-boxes">TOP BOXES</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=sat-nav-holder">SAT NAV
                                    HOLDER</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/shop?page=1&per_page=12&sort=newest&categories=clothing">CLOTHING</a>
                        <ul class="submenu">
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=armr">ARMR</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=textile-jackets">TEXTILE
                                            JACKETS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=leather-jackets">LEATHER
                                            JACKETS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=armr-accessories">ARMR
                                            ACCESSORIES</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=dojo">DOJO</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=boots">BOOTS</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=alpinestars">ALPINESTARS</a>
                                <ul class="submenu">
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=boots">BOOTS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=casual">CASUAL</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=gloves">GLOVES</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=leather-jackets">LEATHER
                                            JACKETS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=leather-suits">LEATHER
                                            SUITS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=protectors-&-sliders">PROTECTORS
                                            & SLIDERS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=rain-wear">RAIN
                                            WEAR</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=textile-jackets">TEXTILE
                                            JACKETS</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=trousers">TROUSERS</a>
                                    </li>
                                    <li>
                                        <a
                                            href="/shop?page=1&per_page=12&sort=newest&categories=reinforced-jeans-&-hoodies">REINFORCED
                                            JEANS & HOODIES</a>
                                    </li>
                                    <li>
                                        <a href="/shop?page=1&per_page=12&sort=newest&categories=accessories-&-layers">ACCESSORIES
                                            & LAYERS</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=oxfords">OXFORDS</a>
                                <ul class="submenu">
                                    <li><a href="#">CASUAL CLOTHING</a></li>
                                    <li><a href="#">PROTECTIVE CASUAL WEAR</a></li>
                                    <li><a href="#">BRACES</a></li>
                                    <li><a href="#">GLOVES</a></li>
                                    <li><a href="#">HEAD & NECKWEAR</a></li>
                                    <li><a href="#">HEATED</a></li>
                                    <li><a href="#">RAIN WEAR</a></li>
                                    <li><a href="#">JACKETS</a></li>
                                    <li><a href="#">KNEE SLIDERS</a></li>
                                    <li><a href="#">LAYERS</a></li>
                                    <li><a href="#">LEATHER</a></li>
                                    <li><a href="#">RAIN WEAR</a></li>
                                    <li><a href="#">REFLECTIVES</a></li>
                                    <li><a href="#">PROTECTIVE DENIM</a></li>
                                    <li><a href="#">SOCKS</a></li>
                                    <li><a href="#">TROUSERS</a></li>
                                    <li><a href="#">BOOTS</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=spartan">SPARTAN</a>
                            </li>
                            <li>
                                <a href="/shop?page=1&per_page=12&sort=newest&categories=bull-it">BULL-IT</a>
                                <ul class="submenu">
                                    <li><a href="#">MEN</a>
                                        <ul class="submenu">
                                            <li><a href="#">AA 2019</a></li>
                                            <li><a href="#">SR6 6 SECOND 2017</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#">WOMEN</a>
                                        <ul class="submenu">
                                            <li><a href="#">AA 2019</a></li>
                                            <li><a href="#">SR6 2017 6 SECOND</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#">PROTECTORS</a></li>
                                    <li><a href="#">CASUAL</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/shop?page=1&per_page=12&sort=newest&categories=tracker">TRACKER</a>
                    </li>
                </ul>
                </li>
                <!-- changes -->
                <!-- <li>
                    <a href="{{ route('motorcycle.delivery') }}">Motorcycle Delivery</a>
                </li> -->
                <!-- <li>
                    <a href="{{ route('repairs.index') }}">Motorcycle Services</a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('repairs.basic') }}">Basic Service</a>
                        </li>
                        <li>
                            <a href="{{ route('repairs.major') }}">Full Service</a>
                        </li>
                    </ul>
                </li> -->
                
                <li>
                    <a href="{{ route('contact.me') }}">CONTACT</a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('about.page') }}">ABOUT</a>
                        </li>
                        <li class="mt-2 d-lg-none d-md-none d-sm-block d-xs-block">
                            <a href="{{ route('careers.index') }}">CAREERS</a>
                        </li>
                    </ul>
                </li>
                @if (env('ECOMMERCE_DROPDOWN_MENU', false))
                    <li class="" style="border:0.5px;">
                        <!-- Dropdown Trigger -->

                        <a style="padding:0 !important;margin:0 !important;" href="/shop">E-COMMERCE
                            <!-- Dropdown Icon -->

                        </a>


                        <!-- Dropdown Menu -->
                        <ul class="submenu">
                            <!-- Shop Pages Section -->
                            <li style="padding:0 !important;margin:0 !important;">
                                <span class="block px-4 py-2 text-xs text-gray-500 uppercase"
                                    style="padding:0 !important;margin:0 !important;">Shop Pages</span>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('/shop/product-page/1') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Product Page</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;" href="{{ url('/shop/basket') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Basket Page</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;" href="{{ url('/shop/details') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Details Page</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;" href="{{ url('/shop/payment') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Page</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;" href="{{ url('/shop/categories') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Categories</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;" href="{{ url('/shop/brands') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Brands</a>
                            </li>

                            <!-- Divider -->
                            <li style="padding:0 !important;margin:0 !important;">
                                <div class="border-t border-gray-200 my-2"></div>
                            </li>

                            <!-- Legals Pages Section -->
                            <li style="padding:0 !important;margin:0 !important;">
                                <span class="block px-4 py-2 text-xs text-gray-500 uppercase">Legals</span>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/return-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Return Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/refund-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Refund Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/warranty-claim-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Warranty Claim
                                    Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/damaged-goods-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Damaged Goods
                                    Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/privacy-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Privacy Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/cookie-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cookie Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/shipping-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Shipping Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/cancellation-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cancellation
                                    Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/terms-conditions') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Terms &
                                    Conditions</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/payment-policy') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Policy</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/user-agreement') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">User Agreement</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/accessibility-statement') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Accessibility
                                    Statement</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/data-protection-agreement') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Data Protection
                                    Agreement</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/click-collect') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Click & Collect</a>
                            </li>
                            <li style="padding:0 !important;margin:0 !important;">
                                <a style="padding:0 !important;margin:0 !important;"
                                    href="{{ url('legals/frequently-asked-questions') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Frequently Asked
                                    Questions</a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- <li class="rounded-lg border-gray-500" style="border:0.5px">
                    <a href="/notify-mottax" class="text-gray-500">FREE MOT / TAX ALERT</a>
                </li> --}}
                <!-- new changes -->
                <!-- <li class="rounded-lg border-gray-500" style="border:0.5px">
                    <a href="{{ route('ngnclub.subscribe') }}" class="text-gray-500"><i class="fa fa-star"
                            style="color:#eeb407;"></i> JOIN NGN CLUB</a>
                </li> -->
                <li class="rounded-lg border-gray-500" style="border:0.5px">
                    <a href="{{ route('ngnpartner.subscribe') }}" class="text-gray-500" style="">
                        Partner
                    </a>
                </li>
                <!-- File: resources/views/frontend/ngnstore/partials/white-header.blade.php -->



                {{-- <li>
                    <a href="{{ url('/finance') }}"><img src="{{ url('img/financebtn.png') }}" style="width:140px;"
                            class="finance-btn"></a>
                </li> --}}

                {{-- </ul> --}}
            </nav><!-- /.mainnav -->

        </div><!-- /.nav-wrap -->
    </div><!-- /.container-fluid -->
</header><!-- /header -->
<script>
    function updateCartDropdown() {
        const cartItemsContainer = document.getElementById('cart-items-container');

        // Check if cartItemsContainer exists before trying to set innerHTML
        if (!cartItemsContainer) {
            console.warn('Cart items container not found, skipping cart update.');
            return;
        }

        const cart = JSON.parse(sessionStorage.getItem('cart')) || [];
        cartItemsContainer.innerHTML = ''; // Clear previous items

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<li class="dropdown-item text-center">Your cart is empty.</li>';
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = 'Cart: 0 items';
            }
            return;
        }

        let totalItems = 0;

        cart.forEach(item => {
            const product = item.product;
            totalItems += item.quantity;

            const cartItem = document.createElement('li');
            cartItem.className = 'dropdown-item';
            cartItem.innerHTML = `
                <div class="ngn-cart-item">
                <p class="ngn-cart-item-sku">SKU: ${product.sku}</p>
                    <p class="ngn-cart-item-variation">Variation: ${item.variation || 'N/A'}</p>
                    <p class="ngn-cart-item-price">£${(item.price || 0).toFixed(2)} x ${item.quantity}</p>    
                    <div class="clearfix"></div>
                    <h3 class="ngn-cart-item-title"><a href="/shop/product/${product.name.toLowerCase().replace(/ /g, '-')}" class="text-decoration-none text-dark">${product.name}</a></h3>
                </div>
            `;
            cartItemsContainer.appendChild(cartItem);
        });

        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = `Cart: ${totalItems} items`;
        }
    }

    updateCartDropdown();

    setInterval(updateCartDropdown, 5000);
</script>
<script src="/assets/js/make-sticky.js"></script>