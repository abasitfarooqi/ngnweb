<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'My Account – NGN Motors' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @include('components.partials.theme-boot')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @include('components.partials.theme-api')
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">

{{-- Top Nav --}}
<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            {{-- Logo + label --}}
            <div class="flex items-center gap-3">
                <a href="/" class="flex-shrink-0" aria-label="NGN Motors Home">
                    <img class="h-8 w-auto" src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors">
                </a>
                <span class="hidden sm:block text-gray-300 dark:text-gray-600">|</span>
                <span class="hidden sm:block text-sm font-medium text-gray-600 dark:text-gray-400">My Account</span>
            </div>

            {{-- Right: email, dashboard, theme toggle, sign out --}}
            <div class="flex items-center gap-2 sm:gap-4">
                <span class="hidden md:block text-sm text-gray-500 dark:text-gray-400 truncate max-w-[180px]">
                    {{ auth('customer')->user()->email ?? '' }}
                </span>
                <a href="{{ route('account.dashboard') }}" class="hidden sm:block text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red dark:hover:text-brand-red transition">
                    Dashboard
                </a>

                {{-- Dark/Light toggle (Alpine) --}}
                <button type="button"
                    x-data
                    @click="window.ngnSetColourMode && window.ngnSetColourMode(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                    class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    aria-label="Toggle colour mode"
                >
                    {{-- Sun (shown in dark mode) --}}
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{-- Moon (shown in light mode) --}}
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <form method="POST" action="{{ route('customer.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-brand-red hover:text-red-700 font-medium transition">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- Page wrapper: sidebar + main --}}
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

        {{-- Sidebar --}}
        <aside class="w-full lg:w-64 xl:w-72 flex-shrink-0" x-data="{
            openBookings: {{ (request()->routeIs('account.bookings') || request()->routeIs('account.mot.*') || request()->routeIs('account.repairs.*')) ? 'true' : 'false' }},
            openRentals:  {{ request()->routeIs('account.rentals*')  ? 'true' : 'false' }},
            openFinance:  {{ request()->routeIs('account.finance*')  ? 'true' : 'false' }},
            openRecovery: {{ request()->routeIs('account.recovery*') ? 'true' : 'false' }}
        }">
            {{-- User badge --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 mb-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium mb-1">Signed in as</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth('customer')->user()->email ?? 'Account' }}</p>
            </div>

            <nav class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">

                @php
                    $navItem = function(string $route, string $label, string $icon) {
                        $active = request()->routeIs($route);
                        return [
                            'href'   => route($route),
                            'label'  => $label,
                            'icon'   => $icon,
                            'active' => $active,
                        ];
                    };

                    $icon_home      = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>';
                    $icon_user      = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>';
                    $icon_doc       = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>';
                    $icon_calendar  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>';
                    $icon_clock     = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                    $icon_finance   = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>';
                    $icon_bolt      = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>';
                    $icon_bag       = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>';
                    $icon_star      = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>';
                    $icon_lock      = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>';
                    $icon_chevron   = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>';
                @endphp

                @foreach ([
                    ['route'=>'account.dashboard','label'=>'Dashboard','icon'=>$icon_home],
                    ['route'=>'account.profile',  'label'=>'Profile',   'icon'=>$icon_user],
                    ['route'=>'account.documents','label'=>'Documents', 'icon'=>$icon_doc],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="portal-nav-link border-b border-gray-100 dark:border-gray-700 {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $item['icon'] !!}</svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                {{-- Repairs & MOT --}}
                <div class="border-b border-gray-100 dark:border-gray-700">
                    <button type="button" @click="openBookings = !openBookings"
                        class="portal-nav-link w-full justify-between {{ (request()->routeIs('account.bookings')||request()->routeIs('account.mot.*')||request()->routeIs('account.repairs.*')) ? 'active' : '' }}">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_calendar !!}</svg>
                            <span>Repairs &amp; MOT</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform flex-shrink-0" :class="{'rotate-180': openBookings}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_chevron !!}</svg>
                    </button>
                    <div x-show="openBookings" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0" class="border-l-2 border-brand-red ml-4 bg-gray-50 dark:bg-gray-800/50">
                        <a href="{{ route('account.bookings') }}"       class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.bookings') ? 'active' : '' }}">My Bookings</a>
                        <a href="{{ route('account.mot.book') }}"       class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.mot.book') ? 'active' : '' }}">Book MOT</a>
                        <a href="{{ route('account.repairs.request') }}" class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.repairs.request') ? 'active' : '' }}">Repair enquiry</a>
                        <a href="{{ route('account.repairs.appointment') }}" class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.repairs.appointment') ? 'active' : '' }}">Repairs appointment</a>
                    </div>
                </div>

                {{-- Rentals --}}
                <div class="border-b border-gray-100 dark:border-gray-700">
                    <button type="button" @click="openRentals = !openRentals"
                        class="portal-nav-link w-full justify-between {{ request()->routeIs('account.rentals*') ? 'active' : '' }}">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_clock !!}</svg>
                            <span>Rentals</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform flex-shrink-0" :class="{'rotate-180': openRentals}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_chevron !!}</svg>
                    </button>
                    <div x-show="openRentals" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0" class="border-l-2 border-brand-red ml-4 bg-gray-50 dark:bg-gray-800/50">
                        <a href="{{ route('account.rentals') }}"            class="portal-nav-sub-link px-4 py-2 {{ (request()->routeIs('account.rentals') || request()->routeIs('account.rentals.browse')) ? 'active' : '' }}">Rentals Booking</a>
                        <a href="{{ route('account.rentals.my-enquiries') }}" class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.rentals.my-enquiries') ? 'active' : '' }}">Rental Enquiries</a>
                        <a href="{{ route('account.rentals.my-rentals') }}" class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.rentals.my-rentals') ? 'active' : '' }}">My Rentals</a>
                    </div>
                </div>

                {{-- Finance --}}
                <div class="border-b border-gray-100 dark:border-gray-700">
                    <button type="button" @click="openFinance = !openFinance"
                        class="portal-nav-link w-full justify-between {{ request()->routeIs('account.finance*') ? 'active' : '' }}">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_finance !!}</svg>
                            <span>Finance</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform flex-shrink-0" :class="{'rotate-180': openFinance}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_chevron !!}</svg>
                    </button>
                    <div x-show="openFinance" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0" class="border-l-2 border-brand-red ml-4 bg-gray-50 dark:bg-gray-800/50">
                        <a href="{{ route('account.finance') }}"                  class="portal-nav-sub-link px-4 py-2 {{ (request()->routeIs('account.finance') || request()->routeIs('account.finance.browse')) ? 'active' : '' }}">Browse New & Used Bikes</a>
                        <a href="{{ route('account.finance.my-applications') }}"  class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.finance.my-applications') ? 'active' : '' }}">My Finance Applications</a>
                    </div>
                </div>

                {{-- Recovery --}}
                <div class="border-b border-gray-100 dark:border-gray-700">
                    <button type="button" @click="openRecovery = !openRecovery"
                        class="portal-nav-link w-full justify-between {{ request()->routeIs('account.recovery*') ? 'active' : '' }}">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_bolt !!}</svg>
                            <span>Recovery</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform flex-shrink-0" :class="{'rotate-180': openRecovery}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon_chevron !!}</svg>
                    </button>
                    <div x-show="openRecovery" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0" class="border-l-2 border-brand-red ml-4 bg-gray-50 dark:bg-gray-800/50">
                        <a href="{{ route('account.recovery') }}"             class="portal-nav-sub-link px-4 py-2 {{ (request()->routeIs('account.recovery') || request()->routeIs('account.recovery.request')) ? 'active' : '' }}">Request Recovery</a>
                        <a href="{{ route('account.recovery.my-requests') }}" class="portal-nav-sub-link px-4 py-2 {{ request()->routeIs('account.recovery.my-requests') ? 'active' : '' }}">My Requests</a>
                    </div>
                </div>

                @php $icon_map = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>'; @endphp
                @php $icon_cc  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'; @endphp
                @php $icon_repeat = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.64 19A9 9 0 0019 8.36M18.36 5A9 9 0 005 15.64"/>'; @endphp
                @php $icon_chat = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8m-8 4h6m6 5l-3-3H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-1z"/>'; @endphp

                @foreach ([
                    ['route'=>'account.orders',         'label'=>'My Orders',          'icon'=>$icon_bag],
                    ['route'=>'account.enquiries',      'label'=>'My Enquiries',       'icon'=>$icon_chat],
                    ['route'=>'account.support',        'label'=>'Conversations',      'icon'=>$icon_chat],
                    ['route'=>'account.addresses',      'label'=>'Addresses',           'icon'=>$icon_map],
                    ['route'=>'account.payment-methods','label'=>'Payment Methods',     'icon'=>$icon_cc],
                    ['route'=>'account.payments.recurring','label'=>'Recurring Payments','icon'=>$icon_repeat],
                    ['route'=>'account.club',           'label'=>'NGN Club',            'icon'=>$icon_star],
                    ['route'=>'account.security',       'label'=>'Security',            'icon'=>$icon_lock],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="portal-nav-link border-t border-gray-100 dark:border-gray-700 {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $item['icon'] !!}</svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                {{-- Back to site --}}
                <div class="border-t-2 border-gray-200 dark:border-gray-700 mt-1">
                    <a href="/" class="portal-nav-link text-gray-500 dark:text-gray-500">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span class="text-xs">Back to NGN Site</span>
                    </a>
                </div>
            </nav>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 min-w-0">
            {{ $slot }}
        </main>

    </div>
</div>

<flux:toast />
@stack('scripts')
@fluxScripts
@livewireScripts
</body>
</html>
