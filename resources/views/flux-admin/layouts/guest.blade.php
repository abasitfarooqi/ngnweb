<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light">

    <title>{{ $title ?? 'Flux Admin login' }} — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <script>document.documentElement.classList.remove('dark');</script>

    <x-ngn-assets />
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-full bg-gray-100 font-sans text-gray-900 antialiased">
    {{ $slot }}
    @livewireScripts
</body>
</html>
