@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ? $title . ' — Flux Admin' : 'Flux Admin — ' . config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @include('components.partials.theme-boot')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @include('components.partials.theme-api')
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-sans antialiased">

    <flux:sidebar sticky stashable class="border-r border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        {{-- Brand --}}
        <a href="{{ route('flux-admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2">
            <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-8 w-auto">
            <span class="text-sm font-semibold tracking-tight text-zinc-900 dark:text-white">Flux Admin</span>
        </a>

        <flux:separator />

        <flux:navlist>
            <flux:navlist.group heading="Main">
                <flux:navlist.item
                    href="{{ route('flux-admin.dashboard') }}"
                    icon="home"
                    :current="request()->routeIs('flux-admin.dashboard*')"
                >
                    Dashboard
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- Module nav groups — will be expanded as modules ship --}}
            <flux:navlist.group heading="Fleet" expandable>
                <flux:navlist.item
                    href="{{ route('flux-admin.motorbikes.index') }}"
                    icon="truck"
                    :current="request()->routeIs('flux-admin.motorbikes*')"
                >
                    Motorbikes
                </flux:navlist.item>
                <flux:navlist.item
                    href="{{ route('flux-admin.branches.index') }}"
                    icon="map-pin"
                    :current="request()->routeIs('flux-admin.branches*')"
                >
                    Branches
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Operations" expandable>
                <flux:navlist.item
                    href="{{ route('flux-admin.rentals.index') }}"
                    icon="key"
                    :current="request()->routeIs('flux-admin.rentals.*')"
                >
                    Rentals
                </flux:navlist.item>
                <flux:navlist.item icon="banknotes" href="{{ route('flux-admin.finance.index') }}" :current="request()->routeIs('flux-admin.finance.*')">
                    Finance
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Other" expandable>
                <flux:navlist.item
                    href="{{ route('flux-admin.pcn.index') }}"
                    icon="exclamation-triangle"
                    :current="request()->routeIs('flux-admin.pcn*')"
                >
                    PCN Cases
                </flux:navlist.item>
                <flux:navlist.item
                    href="{{ route('flux-admin.customers.index') }}"
                    icon="users"
                    :current="request()->routeIs('flux-admin.customers.*')"
                >
                    Customers
                </flux:navlist.item>
                <flux:navlist.item
                    href="{{ route('flux-admin.club.index') }}"
                    icon="star"
                    :current="request()->routeIs('flux-admin.club.*')"
                >
                    Club Members
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        {{-- Theme toggle --}}
        <div class="px-3 pb-2">
            <button
                type="button"
                x-data
                @click="window.ngnSetColourMode && window.ngnSetColourMode(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                class="flex items-center gap-2 w-full px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
            >
                <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <span class="dark:hidden">Dark mode</span>
                <span class="hidden dark:inline">Light mode</span>
            </button>
        </div>

        <flux:separator />

        {{-- Profile --}}
        <flux:dropdown position="top" align="start">
            <flux:profile
                :name="auth()->user()->full_name ?? auth()->user()->first_name"
                :avatar="null"
                icon-trailing="chevron-up-down"
            />
            <flux:menu class="min-w-[200px]">
                <flux:menu.item icon="arrow-left" href="/ngn-admin/dashboard">
                    Back to Backpack
                </flux:menu.item>
                <flux:separator />
                <flux:menu.item icon="arrow-right-start-on-rectangle" href="/ngn-admin/logout">
                    Sign out
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    {{-- Mobile header --}}
    <flux:header class="lg:hidden! border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <span class="text-sm font-semibold text-zinc-900 dark:text-white">Flux Admin</span>
        <flux:spacer />
    </flux:header>

    <flux:main class="flex-1">
        {{ $slot }}
    </flux:main>

    <flux:toast />
    @fluxScripts
    @livewireScripts
</body>
</html>
