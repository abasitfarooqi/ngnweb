<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>{{ $title ?? 'NGN Motors – Motorcycle Rentals, MOT, Repairs &amp; Sales in London' }}</title>
    <meta name="description" content="{{ $description ?? 'Motorcycle services in London – Catford, Tooting, Sutton. Rentals, MOT, repairs, used bikes, finance.' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:title"       content="{{ $title ?? 'NGN Motors' }}">
    <meta property="og:description" content="{{ $description ?? 'Motorcycle services in London' }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:type"        content="website">
    @isset($ogImage)
    <meta property="og:image"       content="{{ $ogImage }}">
    @endisset

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @include('components.partials.theme-boot')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @include('components.partials.theme-api')
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>

    @stack('head')
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-white">

    @livewire('site.header')

    @isset($breadcrumbs)
        @if(count($breadcrumbs) > 0)
            <nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700" aria-label="Breadcrumb">
                <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                    <ol class="flex flex-wrap items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="/" class="hover:text-brand-red transition">Home</a></li>
                        @foreach($breadcrumbs as $crumb)
                            <li class="flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                @if(!empty($crumb['url']))
                                    <a href="{{ $crumb['url'] }}" class="hover:text-brand-red transition">{{ $crumb['label'] }}</a>
                                @else
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </nav>
        @endif
    @endisset

    <main id="main-content">
        {{ $slot }}
    </main>

    @livewire('site.footer')

    @stack('schema')
    <flux:toast />
    @fluxScripts
    @livewireScripts
    @stack('scripts')
</body>
</html>
