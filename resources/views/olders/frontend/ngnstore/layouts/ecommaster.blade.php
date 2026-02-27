<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Include the head section -->
    <title>@yield('title', 'Default Title')</title>

    <!-- Other meta tags -->
</head>

<body class="header_sticky header-style-2 has-menu-extra">
    <!-- Preloader -->
    <div id="loading-overlay">
        <div class="loader"></div>
    </div>

    <!-- Boxed -->
    <div class="boxed">
        <div id="site-header-wrap">
            
        </div>

        <!-- Page Content -->
        @yield('content')
        <!-- End Page Content -->

        <!-- Footer -->
        
        <!-- End Footer -->

        {{-- <!-- Go Top -->
        <a class="go-top">
            <i class="fa fa-chevron-up"></i>
        </a> --}}
    </div>

    <!-- Javascript -->
    @include('olders.frontend.ngnstore.partials.footer-scripts')


</body>
</html>
