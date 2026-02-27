<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-UK" lang="en-UK"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"><!--<![endif]-->

<head>
@include('olders.frontend.ngnstore.partials.head')
</head>
<!-- /#site-header-wrap -->

<body class="header_sticky header-style-2 has-menu-extra">

    <!-- Preloader -->
    <div id="loading-overlay">
        <div class="loader"></div>
    </div>

    <!-- Boxed -->
    <div class="boxed">


        <!-- End /#site-header-wrap -->

        <!-- Page Content -->

        @yield('content')

        <!-- End Page Content -->

        <!-- Newsletter -->



        <!-- End Newsletter -->




    </div>
    <!-- Javascript -->
    @include('olders.frontend.body.footer-scripts')

</body>

</html>
