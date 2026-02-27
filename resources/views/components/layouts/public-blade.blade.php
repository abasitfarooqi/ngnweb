<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NGN Motors - Motorcycle Rentals, MOT, Repairs & Sales in London')</title>
    <meta name="description" content="@yield('description', 'Motorcycle services in London - Catford, Tooting, Sutton. Rentals, MOT, repairs, used bikes, finance.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-900">
    @livewire('site.header')

    <main>
        @yield('content')
    </main>

    @livewire('site.footer')
    <x-ui.quick-book-modal />
    @stack('schema')
    @livewireScripts
</body>
</html>
