<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'NGN Motors – Motorcycle Rentals, MOT, Repairs & Sales in London' }}</title>
    <meta name="description" content="{{ $description ?? 'Motorcycle services in London – Catford, Tooting, Sutton. Rentals, MOT, repairs, used bikes, finance.' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <meta property="og:title" content="{{ $title ?? 'NGN Motors – Motorcycle Rentals, MOT, Repairs & Sales in London' }}">
    <meta property="og:description" content="{{ $description ?? 'Motorcycle services in London – Catford, Tooting, Sutton.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">

    @livewire('site.header')

    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        <nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-2">
            <div class="max-w-7xl mx-auto">
                <flux:breadcrumbs>
                    @foreach($breadcrumbs as $crumb)
                        @if(isset($crumb['url']))
                            <flux:breadcrumbs.item href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</flux:breadcrumbs.item>
                        @else
                            <flux:breadcrumbs.item>{{ $crumb['label'] }}</flux:breadcrumbs.item>
                        @endif
                    @endforeach
                </flux:breadcrumbs>
            </div>
        </nav>
    @endif

    <main>
        {{ $slot }}
    </main>

    @livewire('site.footer')

    {{-- Quick Book Modal --}}
    <flux:modal name="quick-book" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Quick Booking</flux:heading>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Call us or fill in our contact form and we'll get back to you within the hour.
            </p>
            <div class="space-y-3">
                <flux:button href="tel:02083141498" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                    Call Catford: 0208 314 1498
                </flux:button>
                <flux:button href="tel:02034095478" variant="outline" class="w-full">
                    Call Tooting: 0203 409 5478
                </flux:button>
                <flux:button href="tel:02084129275" variant="outline" class="w-full">
                    Call Sutton: 0208 412 9275
                </flux:button>
                <flux:button href="/contact" variant="ghost" class="w-full">Send a Message →</flux:button>
            </div>
        </div>
    </flux:modal>

    @stack('schema')
    @fluxScripts
    @livewireScripts
</body>
</html>
