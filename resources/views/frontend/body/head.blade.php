<head>
    <!-- Clarity Analytics Script -->
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
    @if (app()->environment('production'))
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-1RE49QH35E"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'G-1RE49QH35E');
        </script>
    @endif



    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="author" content="Shariq Ayaz, A Basit Farooqui">

    <!-- Title and Meta Description/Keywords -->
    <title>NGN - Motorcycle Rentals, Repairs, and Motorcycle MOT in London, Catford, and Tooting</title>
    <meta name="description" content="NGN offers motorcycle rentals, MOT services, sales, spare parts, servicing, and repair services in London, Catford, Tooting and Sutton.">
    <meta name="keywords" content="motorcycle rentals, MOT services, motorcycle sales, spare parts, servicing, repair services, motorcycle accessories, London, Catford, Tooting">



    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <!-- Favicon and Touch Icons -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">

    <!-- External CSS Files -->
    {{-- <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css"> --}}
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap5.min.css">

    {{-- <link rel="stylesheet" type="text/css" href="/assets/css/style.css"> --}}
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
    @vite(['resources/css/app.css', 'resources/css/ngnstore.css'])
    {{-- all40 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">

    <!-- Vite - Tailwind and Custom CSS -->

    <!-- Livewire Styles -->
    @livewireStyles

</head>
