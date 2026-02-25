<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Include the head section -->
    @include('frontend.ngnstore.partials.head')
    <title>@yield('title', 'Default Title')</title>

    <!-- Other meta tags -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<script>
    window.Laravel = {
        sessioncid: "{{ session()->getId() }}",
    };
</script>

<body class="header_sticky header-style-2 has-menu-extra">
    <!-- Preloader -->
    <div id="loading-overlay">
        <div class="loader"></div>
    </div>

    <!-- Boxed -->
    <div class="boxed">
        <div id="site-header-wrap">
            <!-- Include the custom header for ngnstore -->
            {{-- @include('frontend.ngnstore.partials.header') --}}
            @include('frontend.ngnstore.partials.white-header')
            {{-- @include('frontend.ngnstore.partials.transparent-header') --}}
            {{-- @include('frontend.ngnstore.partials.black-header') --}}
            {{-- @include('frontend.ngnstore.partials.layout-2-header-black') --}}
            {{-- @include('frontend.ngnstore.partials.layout-2-header-black-top-white') --}}
            {{-- @include('frontend.ngnstore.partials.layout-2-header-white') --}}
            {{-- @include('frontend.ngnstore.partials.layout-3-header-black') --}}
            {{-- @include('frontend.ngnstore.partials.layout-3-header-black-less-width') --}}
        </div>

        <!-- Page Content -->
        @yield('content')
        <!-- End Page Content -->

        <!-- Footer -->
        @include('frontend.ngnstore.partials.footer')
        <!-- End Footer -->

        {{-- <!-- Go Top -->
        <a class="go-top">
            <i class="fa fa-chevron-up"></i>
        </a> --}}
    </div>

    <!-- Javascript -->
    @include('frontend.ngnstore.partials.footer-scripts')


</body>

</html>
