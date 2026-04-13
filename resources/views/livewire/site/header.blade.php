<div x-data="{
    mobileOpen: false,
    expandedGroup: null,
    toggleGroup(name) { this.expandedGroup = this.expandedGroup === name ? null : name; },
    closeMobile() { this.mobileOpen = false; this.expandedGroup = null; }
}" @keydown.escape.window="closeMobile()">

{{-- Top info strip --}}
@if($branches && $branches->isNotEmpty())
<div class="bg-gray-900 text-gray-300 text-xs border-b border-gray-800 hidden sm:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 h-9">
            <div class="flex items-center gap-4 overflow-x-auto scrollbar-hide">
                @foreach($branches as $branch)
                    @php $phone = $branch->phone ?? ''; @endphp
                    @if($phone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hover:text-white transition whitespace-nowrap">
                        <span class="font-medium">{{ $branch->name }}:</span> {{ $phone }}
                    </a>
                    @endif
                @endforeach
                <a href="https://wa.me/447951790568?text=Hello%20NGN" target="_blank" rel="noopener" class="hover:text-white transition whitespace-nowrap">WhatsApp</a>
            </div>
            <div class="flex items-center gap-4 flex-shrink-0">
                <span class="whitespace-nowrap">{{ config('site.hours', 'Mon–Sat 9am–6pm') }}</span>
                <a href="/career" class="hover:text-white transition">Careers</a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Main sticky header (below mobile drawer so menu overlay wins) --}}
<header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-14 sm:h-16 gap-3">

            {{-- Hamburger (mobile/tablet) --}}
            <button type="button"
                @click="mobileOpen = !mobileOpen"
                class="lg:hidden p-2 -m-2 text-gray-600 dark:text-gray-400 hover:text-brand-red transition"
                :aria-expanded="mobileOpen.toString()"
                aria-label="Toggle navigation">
                <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            {{-- Logo --}}
            <a href="/" class="flex-shrink-0">
                <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-9 sm:h-10 w-auto">
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-0.5 ml-6 flex-1" x-data="{ open: null }" @mouseleave="open = null">

                {{-- Sales: label goes to sales hub; chevron toggles submenu (hover still opens via parent) --}}
                <div class="relative" @mouseenter="open = 'sales'" @mouseleave="open = null">
                    <div class="flex items-stretch">
                        <a href="{{ route('sale-motorcycles') }}"
                           class="flex items-center px-3 py-2 text-sm font-semibold uppercase tracking-wide transition {{ request()->routeIs('sale-motorcycles', 'used-motorcycles.page', 'site.finance') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }}">
                            Sales
                        </a>
                        <button type="button"
                                class="flex items-center px-1.5 py-2 text-gray-500 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700 hover:text-brand-red transition"
                                aria-label="Open sales submenu"
                                @click.prevent="open = open === 'sales' ? null : 'sales'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <div x-show="open === 'sales'" x-transition.opacity x-cloak
                         class="absolute left-0 top-full z-50 w-52 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                        <a href="{{ route('motorcycles.new') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">New Motorcycles</a>
                        <a href="{{ route('motorcycles.used') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Used Motorcycles</a>
                        <a href="{{ route('site.finance') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Finance</a>
                        <a href="{{ route('accident-management') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Accident Management</a>
                    </div>
                </div>

                <a href="{{ route('rental-hire') }}" class="px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('rental*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">Rentals</a>

                {{-- Services: label goes to all-services hub --}}
                <div class="relative" @mouseenter="open = 'services'" @mouseleave="open = null">
                    <div class="flex items-stretch">
                        <a href="{{ route('all-services') }}"
                           class="flex items-center px-3 py-2 text-sm font-semibold uppercase tracking-wide transition {{ request()->routeIs('all-services', 'site.mot', 'site.repairs', 'site.repairs.basic', 'site.repairs.full', 'site.repairs.repair-services', 'site.repairs.comparison', 'motorcycle.delivery', 'site.recovery', 'accident-management') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }}">
                            Services
                        </a>
                        <button type="button"
                                class="flex items-center px-1.5 py-2 text-gray-500 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700 hover:text-brand-red transition"
                                aria-label="Open services submenu"
                                @click.prevent="open = open === 'services' ? null : 'services'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <div x-show="open === 'services'" x-transition.opacity x-cloak
                         class="absolute left-0 top-full z-50 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                        <a href="{{ route('all-services') }}" class="block px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">All services</a>
                        <a href="{{ route('site.mot') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">MOT Testing</a>
                        <a href="{{ route('site.repairs') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Repairs &amp; Servicing</a>
                        <a href="{{ route('motorcycle.delivery') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Recovery &amp; Delivery</a>
                        <a href="{{ route('accident-management') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Accident Management</a>
                    </div>
                </div>

                {{-- Shop: label goes to shop home --}}
                <div class="relative" @mouseenter="open = 'shop'" @mouseleave="open = null">
                    <div class="flex items-stretch">
                        <a href="{{ route('shop.home') }}"
                           class="flex items-center px-3 py-2 text-sm font-semibold uppercase tracking-wide transition {{ request()->is('shop*', 'accessories', 'ebikes', 'gps-tracker', 'helmets') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }}">
                            Shop
                        </a>
                        <button type="button"
                                class="flex items-center px-1.5 py-2 text-gray-500 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700 hover:text-brand-red transition"
                                aria-label="Open shop submenu"
                                @click.prevent="open = open === 'shop' ? null : 'shop'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <div x-show="open === 'shop'" x-transition.opacity x-cloak
                         class="absolute left-0 top-full z-50 w-52 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                        <a href="{{ route('shop.home') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">All Shop</a>
                        <a href="{{ route('shop.accessories') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Accessories</a>
                        <a href="{{ route('shop.gps-tracker') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">GPS Trackers</a>
                        <a href="{{ route('shop.helmets') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">Helmets</a>
                        <a href="{{ route('site.ebikes') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-brand-red">E-Bikes</a>
                    </div>
                </div>

                <a href="{{ route('spareparts.index') }}" class="px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('spareparts*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">Spareparts</a>

                <a href="/about" class="px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('about*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">About</a>
                <a href="/contact" class="px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('contact*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">Contact</a>

            </nav>

            <div class="flex-1 lg:flex-none"></div>

            {{-- Desktop right: search, auth, basket, club --}}
            <div class="hidden lg:flex items-center gap-2">
                <x-theme-toggle-icon />
                {{-- Search --}}
                <div x-data="{ searchOpen: false }" class="relative">
                    <button @click="searchOpen = !searchOpen" class="p-2 text-gray-500 hover:text-brand-red transition" aria-label="Search">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <div x-show="searchOpen" @click.outside="searchOpen = false" x-transition.opacity x-cloak
                         class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700 p-3 z-50">
                        <form action="{{ route('ngn_search_results') }}" method="GET">
                            <input type="search" name="query" placeholder="Search motorcycles, parts…"
                                   autofocus
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-800 dark:text-white">
                        </form>
                    </div>
                </div>

                {{-- Basket --}}
                <a href="{{ route('shop.basket') }}" class="p-2 text-gray-500 hover:text-brand-red transition relative" aria-label="Basket">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[1rem] h-4 px-1 inline-flex items-center justify-center bg-brand-red text-white text-[10px] leading-none">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth('customer')
                    <a href="{{ route('account.dashboard') }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-brand-red transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        My Account
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-brand-red transition">Login</a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 text-sm font-semibold bg-brand-red text-white hover:bg-red-700 transition">Register</a>
                @endauth

                <a href="{{ route('ngnclub.subscribe') }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-amber-400 hover:bg-amber-300 text-gray-900 text-xs font-bold uppercase tracking-wide transition">
                    ★ Club
                </a>
            </div>

            {{-- Mobile right: theme, basket, account --}}
            <div class="flex items-center gap-1 lg:hidden">
                <x-theme-toggle-icon />
                <a href="{{ route('shop.basket') }}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-brand-red transition relative" aria-label="Basket">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[1rem] h-4 px-1 inline-flex items-center justify-center bg-brand-red text-white text-[10px] leading-none">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ auth('customer')->check() ? route('account.dashboard') : route('login') }}"
                   class="p-2 text-gray-600 dark:text-gray-400 hover:text-brand-red transition" aria-label="Account">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
            </div>

        </div>
    </div>
</header>

{{-- Mobile menu drawer --}}
<div x-show="mobileOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak
     class="lg:hidden fixed inset-0 z-[60] flex"
     style="display:none;">

    {{-- Overlay --}}
    <div @click="closeMobile()" class="absolute inset-0 z-[60] bg-black/50"></div>

    {{-- Drawer panel --}}
    <div x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="relative z-[70] w-80 max-w-[90vw] bg-white dark:bg-gray-900 h-full max-h-full flex flex-col shadow-2xl overflow-hidden">

        {{-- Drawer header --}}
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
            <a href="/" @click="closeMobile()">
                <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-8 w-auto">
            </a>
            <button @click="closeMobile()" class="p-2 -m-2 text-gray-500 hover:text-brand-red transition" aria-label="Close menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Scrollable nav --}}
        <div class="flex-1 overflow-y-auto overscroll-contain">

            {{-- Search --}}
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <form action="{{ route('ngn_search_results') }}" method="GET" @submit="closeMobile()">
                    <input type="search" name="query" placeholder="Search…"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-brand-red focus:border-brand-red">
                </form>
            </div>

            {{-- Nav groups --}}
            <nav class="py-2">

                {{-- Sales: tap label for hub, chevron for submenu --}}
                <div class="flex items-stretch border-b border-gray-100 dark:border-gray-800">
                    <a href="{{ route('sale-motorcycles') }}" @click="closeMobile()"
                       class="flex-1 flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">
                        Sales
                    </a>
                    <button type="button" @click="toggleGroup('sales')"
                            class="flex items-center px-4 py-3 text-gray-500 dark:text-gray-400 border-l border-gray-100 dark:border-gray-800 hover:text-brand-red transition"
                            aria-label="Expand sales submenu">
                        <svg :class="expandedGroup === 'sales' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
                <div x-show="expandedGroup === 'sales'" x-transition class="bg-gray-50 dark:bg-gray-800">
                    <a href="{{ route('motorcycles.new') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">New Motorcycles</a>
                    <a href="{{ route('motorcycles.used') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Used Motorcycles</a>
                    <a href="{{ route('site.finance') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Finance</a>
                    <a href="{{ route('accident-management') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Accident Management</a>
                </div>

                <a href="{{ route('rental-hire') }}" @click="closeMobile()"
                   class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">
                    Rentals
                </a>

                {{-- Services --}}
                <div class="flex items-stretch border-b border-gray-100 dark:border-gray-800">
                    <a href="{{ route('all-services') }}" @click="closeMobile()"
                       class="flex-1 flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">
                        Services
                    </a>
                    <button type="button" @click="toggleGroup('services')"
                            class="flex items-center px-4 py-3 text-gray-500 dark:text-gray-400 border-l border-gray-100 dark:border-gray-800 hover:text-brand-red transition"
                            aria-label="Expand services submenu">
                        <svg :class="expandedGroup === 'services' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
                <div x-show="expandedGroup === 'services'" x-transition class="bg-gray-50 dark:bg-gray-800">
                    <a href="{{ route('all-services') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red">All services</a>
                    <a href="{{ route('site.mot') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">MOT Testing</a>
                    <a href="{{ route('site.repairs') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Repairs &amp; Servicing</a>
                    <a href="{{ route('motorcycle.delivery') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Recovery &amp; Delivery</a>
                    <a href="{{ route('accident-management') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Accident Management</a>
                </div>

                {{-- Shop --}}
                <div class="flex items-stretch border-b border-gray-100 dark:border-gray-800">
                    <a href="{{ route('shop.home') }}" @click="closeMobile()"
                       class="flex-1 flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">
                        Shop
                    </a>
                    <button type="button" @click="toggleGroup('shop')"
                            class="flex items-center px-4 py-3 text-gray-500 dark:text-gray-400 border-l border-gray-100 dark:border-gray-800 hover:text-brand-red transition"
                            aria-label="Expand shop submenu">
                        <svg :class="expandedGroup === 'shop' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
                <div x-show="expandedGroup === 'shop'" x-transition class="bg-gray-50 dark:bg-gray-800">
                    <a href="{{ route('shop.home') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">All Shop</a>
                    <a href="{{ route('shop.accessories') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Accessories</a>
                    <a href="{{ route('shop.gps-tracker') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">GPS Trackers</a>
                    <a href="{{ route('shop.helmets') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">Helmets</a>
                    <a href="{{ route('site.ebikes') }}" @click="closeMobile()" class="block px-6 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red">E-Bikes</a>
                </div>

                <a href="{{ route('spareparts.index') }}" @click="closeMobile()" class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">
                    Spareparts
                </a>

                <a href="/about" @click="closeMobile()" class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">About</a>
                <a href="/contact" @click="closeMobile()" class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">Contact</a>
                <a href="/locations" @click="closeMobile()" class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red transition">Locations</a>

                <div class="border-t border-gray-100 dark:border-gray-800 mt-2 pt-2">
                    @auth('customer')
                        <a href="{{ route('account.dashboard') }}" @click="closeMobile()"
                           class="flex items-center gap-2 px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Account
                        </a>
                    @else
                        <a href="{{ route('login') }}" @click="closeMobile()" class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white hover:text-brand-red">Login</a>
                        <a href="{{ route('register') }}" @click="closeMobile()" class="flex items-center px-4 py-3 text-sm font-bold uppercase tracking-wide text-brand-red">Register</a>
                    @endauth
                </div>
            </nav>
        </div>

        {{-- Club CTA at bottom of drawer --}}
        <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-800">
            <a href="{{ route('ngnclub.subscribe') }}" @click="closeMobile()"
               class="flex items-center justify-center gap-2 w-full py-3 bg-amber-400 hover:bg-amber-300 text-gray-900 text-sm font-bold uppercase tracking-wide transition">
                ★ Join NGN Club for Free
            </a>
            {{-- Phones --}}
            @if($branches && $branches->isNotEmpty())
                <div class="mt-3 space-y-1">
                    @foreach($branches as $branch)
                        @php $phone = $branch->phone ?? ''; @endphp
                        @if($phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                               class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 hover:text-brand-red">
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span><strong>{{ $branch->name }}:</strong> {{ $phone }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Mobile sticky bottom action bar --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 grid grid-cols-4 safe-area-inset-bottom">
    <button type="button"
            x-data
            @click="$flux.modal('quick-book').show()"
            class="flex flex-col items-center py-3 text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span class="text-[10px] font-medium">Book</span>
    </button>
    <a href="tel:02083141498" class="flex flex-col items-center py-3 text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        <span class="text-[10px] font-medium">Call</span>
    </a>
    <a href="https://wa.me/447951790568?text=Hello%20NGN" target="_blank" class="flex flex-col items-center py-3 text-gray-600 dark:text-gray-400 hover:text-green-600 transition">
        <svg class="h-5 w-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="text-[10px] font-medium">WhatsApp</span>
    </a>
    <a href="{{ auth('customer')->check() ? route('account.dashboard') : route('login') }}"
       class="flex flex-col items-center py-3 text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span class="text-[10px] font-medium">Account</span>
    </a>
</div>

{{-- Spacer for mobile bottom bar --}}
<div class="lg:hidden h-14"></div>

</div>
