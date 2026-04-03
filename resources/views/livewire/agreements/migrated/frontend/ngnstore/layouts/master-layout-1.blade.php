<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Include the head section -->
    @include('livewire.agreements.migrated.frontend.ngnstore.partials.head')
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
            <!-- Include the custom header for ngnstore -->
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.header') --}}
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.white-header') --}}
            @include('livewire.agreements.migrated.frontend.ngnstore.partials.transparent-header')
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.black-header') --}}
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.layout-2-header-black') --}}
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.layout-2-header-black-top-white') --}}
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.layout-2-header-white') --}}
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.layout-3-header-black') --}}
            {{-- @include('livewire.agreements.migrated.frontend.ngnstore.partials.layout-3-header-black-less-width') --}}
        </div>

        <!-- Page Content -->
        @yield('content')
        <!-- End Page Content -->

        <!-- Footer -->
        @include('livewire.agreements.migrated.frontend.ngnstore.partials.footer')
        <!-- End Footer -->

        {{-- <!-- Go Top -->
        <a class="go-top">
            <i class="fa fa-chevron-up"></i>
        </a> --}}
    </div>

    <!-- Javascript -->
    @include('livewire.agreements.migrated.frontend.ngnstore.partials.footer-scripts')


</body>
</html>
