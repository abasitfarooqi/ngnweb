<meta charset="UTF-8">
<!-- Basic Page Needs -->
<meta charset="utf-8">
<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="author" content="Shariq Ayaz, A Basit Farooqui">
<!-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0"> -->
<title>@yield('title') - {{ config('app.name') }}</title>
@section('meta_keywords')
    <meta name="keywords"
        content="motorcycle rentals, MOT services, motorcycle sales, spare parts, servicing, repair services, motorcycle accessories, London, Catford, Tooting">
@show

@section('meta_description')
    <meta name="description"
        content="At NGN, We're dedicated to providing top-notch motorcycle rentals, MOT services, sales, spare parts, servicing, and repair services in London and situated in Catford, Tooting and Sutton.">
@show
<!-- Favicon and Touch Icons -->
<link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
<!-- Custom CSS for ngnstore -->
{{-- <link href="/assets/css/bootstrap.css" rel="stylesheet"> --}}
<!-- Favicon and Touch Icons -->
<link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">

@if (app()->environment('production'))
    <!-- Microsoft Clarity Tracking Code -->
    <script type="text/javascript">
        (function(c, l, a, r, i, t, y) {
            c[a] = c[a] || function() {
                (c[a].q = c[a].q || []).push(arguments)
            };
            t = l.createElement(r);
            t.async = 1;
            t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0];
            y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "n871dj3sq5");
    </script>
@endif


<!-- Google Tag Manager -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1RE49QH35E"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-1RE49QH35E');
</script>


<!-- Favicon and Touch Icons -->
<link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
<!-- External CSS Files -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Set up AJAX to include the CSRF token in the headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

</script>
<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap5.min.css">

<link rel="stylesheet" type="text/css" href="/assets/css/owl.carousel.min.css">
<link rel="stylesheet" type="text/css" href="/assets/css/owl.theme.default.min.css">

{{-- <link rel="stylesheet" type="text/css" href="/assets/css/templatemo-pod-talk.css"> --}}

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

{{-- <link rel="stylesheet" type="text/css" href="/assets/css/ngnstore.css"> --}}
<link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
@vite(['resources/css/ngnstore.css'])
{{-- @vite(['resources/js/auth-dropdown.js']) --}}
{{-- <link rel="stylesheet" type="text/css" href="/assets/css/white-header-ngn.css"> --}}
{{-- <link rel="stylesheet" type="text/css" href="/assets/css/white-header-hover-white.css"> --}}
{{-- <link rel="stylesheet" type="text/css" href="/assets/css/header-black-forced.css"> --}}
{{-- <link rel="stylesheet" type="text/css" href="/assets/css/black-header-backdrop-effect.css"> --}}


<!-- AOS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />



<link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">


@stack('css')
<!-- Vite - Tailwind and Custom CSS -->
{{-- <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css"> --}}
{{-- @vite(['resources/css/app.css', 'resources/css/style.css']) --}}


@livewireStyles
