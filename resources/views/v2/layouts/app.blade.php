<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NGN Motors — London Motorbike Specialists')</title>
    <meta name="description" content="@yield('meta_description', 'NGN Motors — London\'s trusted motorbike dealer, rental, servicing and recovery specialists.')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', 'NGN Motors')">
    <meta property="og:description" content="@yield('og_description', 'London\'s motorbike specialists — sales, rental, servicing and recovery.')">
    <meta property="og:image" content="@yield('og_image', asset('assets/images/ngn-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- Flux + Alpine + Tailwind --}}
    @vite(['resources/css/v2.css', 'resources/js/v2.js'])
    @fluxStyles
</head>
<body class="bg-white text-zinc-900 antialiased" x-data>

    <livewire:v2.navbar />

    <main>
        {{ $slot }}
    </main>

    <livewire:v2.footer />

    @fluxScripts
    @livewireScripts
</body>
</html>
