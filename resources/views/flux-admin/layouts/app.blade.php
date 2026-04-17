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
    {{-- Flux admin: table row lines, toolbar controls (no rounded corners in toolbars per product preference). --}}
    <style>
        .flux-admin-content [data-flux-table] tbody tr + tr td { box-shadow: inset 0 1px 0 0 rgb(228 228 231); }
        .dark .flux-admin-content [data-flux-table] tbody tr + tr td { box-shadow: inset 0 1px 0 0 rgb(63 63 70); }
        .flux-admin-toolbar [data-flux-input] input,
        .flux-admin-toolbar [data-flux-input] button,
        .flux-admin-toolbar select[data-flux-control] { border-radius: 0 !important; }
        .flux-admin-toolbar [data-flux-field] { margin-bottom: 0; }
        .flux-admin-table-panel { -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain; }
        {{-- Sidebar host: belt-and-braces if any global rule or UA style fights Tailwind dark surface. --}}
        html.dark body.flux-admin-app [data-flux-sidebar] {
            background-color: rgb(9 9 11);
            color: rgb(244 244 245);
        }
        {{-- style.css reset sets div{background:transparent} unlayered, which beats @layer utilities on [data-flux-main]. Paint the right column here (unlayered) so dark mode matches the shell. --}}
        body.flux-admin-app .flux-admin-main-column {
            min-height: 100dvh;
            background-color: rgb(244 244 245);
            color: rgb(24 24 27);
        }
        html.dark body.flux-admin-app .flux-admin-main-column {
            background-color: rgb(9 9 11);
            color: rgb(244 244 245);
        }
    </style>
</head>
<body class="flux-admin-app min-h-dvh bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans antialiased lg:flex lg:min-h-screen lg:flex-row">

    {{-- Sidebar: same dark surface as main canvas (no half-light / half-dark split). --}}
    <flux:sidebar sticky stashable class="z-20 border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950 lg:z-auto lg:min-h-screen lg:shrink-0">
        <button
            type="button"
            class="lg:hidden flex h-10 w-10 shrink-0 items-center justify-center text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
            aria-label="Close menu"
            onclick="document.dispatchEvent(new CustomEvent('flux-sidebar-toggle',{bubbles:true}))"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>

        {{-- Brand --}}
        <a href="{{ route('flux-admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2">
            <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-8 w-auto">
            <span class="text-sm font-semibold tracking-tight text-zinc-900 dark:text-white">Flux Admin</span>
        </a>

        <flux:separator />

        <flux:navlist class="min-h-0 flex-1 overflow-y-auto py-1">
            <flux:navlist.group heading="Main">
                <flux:navlist.item
                    href="{{ route('flux-admin.dashboard') }}"
                    icon="home"
                    :current="request()->routeIs('flux-admin.dashboard*')"
                >
                    Dashboard
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- Fleet / Operations / Other: no expandable — child links must always be visible (expand-only groups hide hrefs until toggled). --}}
            <flux:navlist.group heading="Fleet">
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

            <flux:navlist.group heading="Operations">
                <flux:navlist.item
                    href="{{ route('flux-admin.rentals.index') }}"
                    icon="key"
                    :current="request()->routeIs('flux-admin.rentals.*')"
                >
                    Rentals
                </flux:navlist.item>
                <flux:navlist.item
                    href="{{ route('flux-admin.finance.index') }}"
                    icon="banknotes"
                    :current="request()->routeIs('flux-admin.finance.*')"
                >
                    Finance
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Other">
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

    {{-- Main column: min-w-0 stops wide tables from growing under the sidebar; overflow-y keeps scroll in this pane. --}}
    <div class="flux-admin-main-column flex min-h-dvh w-full min-w-0 flex-1 flex-col lg:min-h-screen">
        <flux:header class="flex shrink-0 items-center border-b border-zinc-200 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 lg:hidden">
            <button
                type="button"
                class="flex h-10 w-10 shrink-0 items-center justify-center text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                aria-label="Open menu"
                onclick="document.dispatchEvent(new CustomEvent('flux-sidebar-toggle',{bubbles:true}))"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5"/></svg>
            </button>
            <flux:spacer />
            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Menu</span>
            <flux:spacer />
        </flux:header>

        <flux:main class="min-h-0 min-w-0 flex-1 overflow-y-auto bg-zinc-100 !p-0 dark:bg-zinc-950 dark:text-zinc-100">
            <div class="flux-admin-content mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                {{ $slot }}
            </div>
        </flux:main>
    </div>

    <flux:toast />
    @livewireScripts
    @fluxScripts
</body>
</html>
