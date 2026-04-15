@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ? $title . ' - Flux Admin' : 'Flux Admin' }}</title>

    @include('components.partials.theme-boot')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @include('components.partials.theme-api')
    @livewireStyles

    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-white/10 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('flux-admin.dashboard') }}" class="flex items-center gap-2 px-2">
            <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-8 w-auto">
            <span class="text-sm font-semibold text-zinc-900 dark:text-white">Flux Admin</span>
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group heading="Main">
                <flux:navlist.item
                    icon="squares-2x2"
                    href="{{ route('flux-admin.dashboard') }}"
                    :current="request()->routeIs('flux-admin.dashboard')"
                >
                    Dashboard
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Operations">
                <flux:navlist.item
                    icon="truck"
                    href="{{ route('flux-admin.motorbikes.index') }}"
                    :current="request()->routeIs('flux-admin.motorbikes.*')"
                >
                    Motorbikes
                </flux:navlist.item>

                <flux:navlist.item
                    icon="users"
                    href="{{ route('flux-admin.customers.index') }}"
                    :current="request()->routeIs('flux-admin.customers.*')"
                >
                    Customers
                </flux:navlist.item>

                <flux:navlist.item
                    icon="key"
                    href="{{ route('flux-admin.rentals.index') }}"
                    :current="request()->routeIs('flux-admin.rentals.*')"
                >
                    Rentals
                </flux:navlist.item>

                <flux:navlist.item
                    icon="banknotes"
                    href="{{ route('flux-admin.finance.index') }}"
                    :current="request()->routeIs('flux-admin.finance.*')"
                >
                    Finance
                </flux:navlist.item>

                <flux:navlist.item
                    icon="exclamation-triangle"
                    href="{{ route('flux-admin.pcn.index') }}"
                    :current="request()->routeIs('flux-admin.pcn.*')"
                >
                    PCN Cases
                </flux:navlist.item>

                <flux:navlist.item
                    icon="star"
                    href="{{ route('flux-admin.club.index') }}"
                    :current="request()->routeIs('flux-admin.club.*')"
                >
                    Club Members
                </flux:navlist.item>

                <flux:navlist.item
                    icon="building-storefront"
                    href="{{ route('flux-admin.branches.index') }}"
                    :current="request()->routeIs('flux-admin.branches.*')"
                >
                    Branches
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="System">
                <flux:navlist.item
                    icon="arrow-left-start-on-rectangle"
                    href="/ngn-admin/dashboard"
                >
                    Backpack Admin
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="sun" x-on:click="window.ngnSetColourMode && window.ngnSetColourMode(document.documentElement.classList.contains('dark') ? 'light' : 'dark')">
                Toggle Theme
            </flux:navlist.item>
        </flux:navlist>

        @auth
        <div class="flex items-center gap-2 px-3 py-2 border-t border-zinc-200 dark:border-white/10">
            <flux:avatar size="sm" name="{{ auth()->user()->full_name ?? auth()->user()->first_name }}" />
            <div class="min-w-0">
                <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ auth()->user()->full_name ?? auth()->user()->first_name }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
        @endauth
    </flux:sidebar>

    <flux:main class="min-h-screen">
        <flux:header class="border-b border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:breadcrumbs class="hidden sm:flex">
                <flux:breadcrumbs.item href="{{ route('flux-admin.dashboard') }}">Flux Admin</flux:breadcrumbs.item>
                @isset($breadcrumbs)
                    {{ $breadcrumbs }}
                @endisset
            </flux:breadcrumbs>

            <flux:spacer />

            @isset($headerActions)
                {{ $headerActions }}
            @endisset
        </flux:header>

        <div class="flex flex-1 overflow-hidden">
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </div>

            @isset($contextPanel)
                <aside class="hidden xl:block w-80 2xl:w-96 border-l border-zinc-200 dark:border-white/10 overflow-y-auto bg-zinc-50 dark:bg-zinc-900 p-4">
                    {{ $contextPanel }}
                </aside>
            @endisset
        </div>
    </flux:main>

    @fluxScripts
    @livewireScripts
</body>
</html>
