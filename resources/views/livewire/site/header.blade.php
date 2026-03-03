<div>
{{-- Top info strip --}}
@if($branches && $branches->isNotEmpty())
<div class="bg-gray-950 text-gray-300 text-xs border-b border-gray-800 hidden sm:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-9">
            <div class="flex items-center gap-5 overflow-x-auto scrollbar-none">
                @foreach($branches as $branch)
                    @php $phone = $branch->phone ?? config('site.branches.' . strtolower($branch->name ?? '') . '.phone', ''); @endphp
                    @if($phone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="whitespace-nowrap hover:text-white transition">
                        <span class="font-medium text-gray-200">{{ $branch->name }}:</span> {{ $phone }}
                    </a>
                    @endif
                @endforeach
                <a href="https://wa.me/{{ config('site.whatsapp', '447951790568') }}?text=Hello%20NGN" target="_blank" rel="noopener" class="whitespace-nowrap text-green-400 hover:text-green-300 transition">
                    WhatsApp Us
                </a>
            </div>
            <div class="flex items-center gap-5 flex-shrink-0 text-gray-400">
                <span>{{ config('site.hours', 'Mon–Sat 9am–6pm') }}</span>
                <a href="/career" class="hover:text-white transition">Careers</a>
                <a href="/faqs" class="hover:text-white transition">FAQs</a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Main header --}}
<flux:header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm sticky top-0 z-50 h-14 lg:h-16">

    {{-- Hamburger: opens sidebar on mobile/tablet --}}
    <flux:sidebar.toggle class="lg:hidden -ml-1 mr-1 p-2 text-gray-700 dark:text-gray-300" icon="bars-3" inset="left" aria-label="Open menu" />

    {{-- Logo --}}
    <a href="/" class="flex items-center mr-4 lg:mr-6 flex-shrink-0" aria-label="NGN Motors – Home">
        <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-8 sm:h-9 lg:h-10 w-auto">
    </a>

    {{-- Desktop nav (lg+) --}}
    <nav class="hidden lg:flex items-center gap-0 flex-1" aria-label="Main navigation">

        {{-- Sales --}}
        <flux:dropdown>
            <flux:button variant="ghost" icon:trailing="chevron-down" size="sm"
                class="font-semibold uppercase text-xs tracking-widest text-gray-700 dark:text-gray-200 hover:text-brand-red dark:hover:text-brand-red px-3">
                Sales
            </flux:button>
            <flux:menu class="min-w-52">
                <flux:menu.item href="{{ route('motorcycles.new') }}" icon="sparkles">New Motorcycles</flux:menu.item>
                <flux:menu.item href="{{ route('motorcycles.used') }}" icon="archive-box">Used Motorcycles</flux:menu.item>
                <flux:menu.item href="/bikes" icon="tag">All Bikes</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item href="/finance" icon="banknotes">Finance</flux:menu.item>
                <flux:menu.item href="/accident-management" icon="shield-exclamation">Accident Management</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        {{-- Rentals --}}
        <a href="{{ route('rental-hire') }}"
           class="px-3 py-2 text-xs font-semibold uppercase tracking-widest transition {{ request()->is('rental*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-200 hover:text-brand-red dark:hover:text-brand-red' }}">
            Rentals
        </a>

        {{-- Services --}}
        <flux:dropdown>
            <flux:button variant="ghost" icon:trailing="chevron-down" size="sm"
                class="font-semibold uppercase text-xs tracking-widest text-gray-700 dark:text-gray-200 hover:text-brand-red dark:hover:text-brand-red px-3">
                Services
            </flux:button>
            <flux:menu class="min-w-56">
                <flux:menu.item href="{{ route('services') }}" icon="squares-2x2">All Services</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item href="/mot" icon="check-badge">MOT Testing</flux:menu.item>
                <flux:menu.item href="/repairs" icon="wrench-screwdriver">Repairs & Servicing</flux:menu.item>
                <flux:menu.item href="/recovery" icon="bolt">Recovery & Delivery</flux:menu.item>
                <flux:menu.item href="/accident-management" icon="shield-exclamation">Accident Management</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        {{-- Shop --}}
        <flux:dropdown>
            <flux:button variant="ghost" icon:trailing="chevron-down" size="sm"
                class="font-semibold uppercase text-xs tracking-widest text-gray-700 dark:text-gray-200 hover:text-brand-red dark:hover:text-brand-red px-3">
                Shop
            </flux:button>
            <flux:menu class="min-w-48">
                <flux:menu.item href="{{ route('shop-motorcycle') }}" icon="shopping-bag">All Shop</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item href="/shop/accessories">Accessories</flux:menu.item>
                <flux:menu.item href="/shop/spare-parts">Spare Parts</flux:menu.item>
                <flux:menu.item href="/shop/gps-tracker">GPS Trackers</flux:menu.item>
                <flux:menu.item href="/ebikes">E-Bikes</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <a href="/locations"
           class="px-3 py-2 text-xs font-semibold uppercase tracking-widest transition {{ request()->is('location*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-200 hover:text-brand-red' }}">
            Locations
        </a>

        <a href="{{ route('about.page') }}"
           class="px-3 py-2 text-xs font-semibold uppercase tracking-widest transition {{ request()->is('about*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-200 hover:text-brand-red' }}">
            About
        </a>

        <a href="{{ route('contact.me') }}"
           class="px-3 py-2 text-xs font-semibold uppercase tracking-widest transition {{ request()->is('contact*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-200 hover:text-brand-red' }}">
            Contact
        </a>

    </nav>

    <flux:spacer />

    {{-- Desktop: Search (Flux Pro dropdown popover) --}}
    <flux:dropdown align="end" class="hidden lg:flex items-center">
        <flux:button icon="magnifying-glass" variant="ghost" size="sm" aria-label="Search" class="text-gray-600 dark:text-gray-400 hover:text-brand-red" />
        <div class="w-72 p-3 bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700">
            <form action="{{ route('ngn_search_results') }}" method="GET">
                <flux:input type="search" name="query" placeholder="Search motorcycles, parts…" autofocus />
            </form>
        </div>
    </flux:dropdown>

    {{-- Desktop: dark mode toggle --}}
    <flux:button
        icon="moon"
        variant="ghost"
        size="sm"
        x-data
        @click="
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('ngn-theme', html.classList.contains('dark') ? 'dark' : 'light');
        "
        class="hidden lg:flex text-gray-500 dark:text-gray-400 hover:text-brand-red ml-1"
        aria-label="Toggle dark mode"
    />

    {{-- Desktop: Auth --}}
    <div class="hidden lg:flex items-center gap-2 ml-2">
        @auth('customer')
            <flux:button href="{{ route('account.dashboard') }}" variant="ghost" size="sm" icon="user"
                class="text-gray-700 dark:text-gray-200">
                Account
            </flux:button>
        @else
            <flux:button href="{{ route('login') }}" variant="ghost" size="sm"
                class="text-gray-700 dark:text-gray-200">
                Login
            </flux:button>
            <flux:button href="{{ route('register') }}" size="sm"
                class="bg-brand-red text-white hover:bg-red-700 border-0 font-semibold">
                Register
            </flux:button>
        @endauth
    </div>

    {{-- Desktop: NGN Club badge --}}
    <a href="{{ route('ngnclub.subscribe') }}"
       class="hidden lg:flex items-center gap-1 ml-3 px-3 py-1.5 bg-amber-400 text-gray-900 text-xs font-bold uppercase tracking-wide hover:bg-amber-300 transition flex-shrink-0">
        ★ Club
    </a>

</flux:header>

{{-- ================================================================ --}}
{{-- Mobile sidebar: full-height, scrollable, Flux Pro stashable      --}}
{{-- ================================================================ --}}
<flux:sidebar stashable sticky
    class="lg:hidden bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 shadow-2xl flex flex-col w-72 max-w-[85vw]"
    style="max-height: 100dvh; max-height: 100vh;">

    {{-- Sidebar header --}}
    <div class="flex items-center justify-between shrink-0 px-4 h-14 border-b border-gray-100 dark:border-gray-800">
        <a href="/" class="flex items-center">
            <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-8 w-auto">
        </a>
        <flux:sidebar.toggle icon="x-mark" class="p-2 -mr-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white" aria-label="Close menu" />
    </div>

    {{-- Mobile search --}}
    <div class="shrink-0 px-4 py-3 border-b border-gray-100 dark:border-gray-800">
        <form action="{{ route('ngn_search_results') }}" method="GET">
            <flux:input type="search" name="query" placeholder="Search motorcycles, parts…" size="sm" />
        </form>
    </div>

    {{-- Scrollable nav --}}
    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain pb-2">
        <flux:navlist class="py-2 px-2">

            <flux:navlist.group heading="Sales" :expandable="true" :expanded="request()->is('bikes*','finance*','motorcycles*')">
                <flux:navlist.item href="{{ route('motorcycles.new') }}" :current="request()->is('motorcycles/new*')">New Motorcycles</flux:navlist.item>
                <flux:navlist.item href="{{ route('motorcycles.used') }}" :current="request()->is('motorcycles/used*')">Used Motorcycles</flux:navlist.item>
                <flux:navlist.item href="/bikes" :current="request()->is('bikes*')">All Bikes</flux:navlist.item>
                <flux:navlist.item href="/finance" :current="request()->is('finance*')">Finance</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.item href="{{ route('rental-hire') }}" :current="request()->is('rental*')">
                Rentals
            </flux:navlist.item>

            <flux:navlist.group heading="Services" :expandable="true" :expanded="request()->is('mot*','repair*','recovery*','accident*','services*')">
                <flux:navlist.item href="{{ route('services') }}" :current="request()->is('services')">All Services</flux:navlist.item>
                <flux:navlist.item href="/mot" :current="request()->is('mot*')">MOT Testing</flux:navlist.item>
                <flux:navlist.item href="/repairs" :current="request()->is('repair*')">Repairs & Servicing</flux:navlist.item>
                <flux:navlist.item href="/recovery" :current="request()->is('recovery*')">Recovery & Delivery</flux:navlist.item>
                <flux:navlist.item href="/accident-management" :current="request()->is('accident-management*')">Accident Management</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Shop" :expandable="true" :expanded="request()->is('shop*','ebike*')">
                <flux:navlist.item href="{{ route('shop-motorcycle') }}" :current="request()->is('shop')">All Shop</flux:navlist.item>
                <flux:navlist.item href="/shop/accessories" :current="request()->is('shop/accessories*')">Accessories</flux:navlist.item>
                <flux:navlist.item href="/shop/spare-parts" :current="request()->is('shop/spare-parts*')">Spare Parts</flux:navlist.item>
                <flux:navlist.item href="/shop/gps-tracker" :current="request()->is('shop/gps-tracker*')">GPS Trackers</flux:navlist.item>
                <flux:navlist.item href="/ebikes" :current="request()->is('ebike*')">E-Bikes</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.item href="/locations" :current="request()->is('location*')">Locations</flux:navlist.item>
            <flux:navlist.item href="{{ route('about.page') }}" :current="request()->is('about*')">About</flux:navlist.item>
            <flux:navlist.item href="{{ route('contact.me') }}" :current="request()->is('contact*')">Contact</flux:navlist.item>
            <flux:navlist.item href="/faqs" :current="request()->is('faqs*')">FAQs</flux:navlist.item>

            <flux:navlist.separator />

            @auth('customer')
                <flux:navlist.item icon="user" href="{{ route('account.dashboard') }}" :current="request()->is('portal*')">
                    My Account
                </flux:navlist.item>
            @else
                <flux:navlist.item href="{{ route('login') }}" :current="request()->is('login')">Login</flux:navlist.item>
                <flux:navlist.item href="{{ route('register') }}" :current="request()->is('register')">Register</flux:navlist.item>
            @endauth

            <flux:navlist.separator />

            <flux:navlist.item href="{{ route('ngnclub.subscribe') }}" class="text-amber-600 font-bold">
                ★ Join NGN Club
            </flux:navlist.item>

        </flux:navlist>
    </div>

    {{-- Branch phones pinned at bottom --}}
    @if($branches && $branches->isNotEmpty())
    <div class="shrink-0 border-t border-gray-100 dark:border-gray-800 px-4 py-3 space-y-2 bg-gray-50 dark:bg-gray-950">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Call Us</p>
        @foreach($branches as $branch)
            @php $phone = $branch->phone ?? config('site.branches.' . strtolower($branch->name) . '.phone'); @endphp
            @if($phone)
            <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
               class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-red transition">
                <svg class="h-4 w-4 flex-shrink-0 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>{{ $branch->name }}: <strong>{{ $phone }}</strong></span>
            </a>
            @endif
        @endforeach
    </div>
    @endif

</flux:sidebar>

{{-- ================================================================ --}}
{{-- Mobile sticky bottom action bar (Book / Call / WhatsApp / Account) --}}
{{-- ================================================================ --}}
<div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-[0_-4px_16px_rgba(0,0,0,0.10)] grid grid-cols-4"
     style="padding-bottom: env(safe-area-inset-bottom, 0px);">

    {{-- Book --}}
    <button type="button" x-data @click="$flux.modal('quick-book').show()"
        class="flex flex-col items-center py-3 gap-0.5 text-gray-600 dark:text-gray-300 hover:text-brand-red active:bg-gray-50 dark:active:bg-gray-800 transition">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="text-[10px] font-semibold">Book</span>
    </button>

    {{-- Call --}}
    <a href="tel:02083141498"
       class="flex flex-col items-center py-3 gap-0.5 text-gray-600 dark:text-gray-300 hover:text-brand-red active:bg-gray-50 dark:active:bg-gray-800 transition">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
        <span class="text-[10px] font-semibold">Call</span>
    </a>

    {{-- WhatsApp --}}
    <a href="https://wa.me/447951790568?text=Hello%20NGN" target="_blank" rel="noopener"
       class="flex flex-col items-center py-3 gap-0.5 text-gray-600 dark:text-gray-300 hover:text-green-600 active:bg-gray-50 dark:active:bg-gray-800 transition">
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <span class="text-[10px] font-semibold">WhatsApp</span>
    </a>

    {{-- Account / Login --}}
    <a href="{{ auth('customer')->check() ? route('account.dashboard') : route('login') }}"
       class="flex flex-col items-center py-3 gap-0.5 text-gray-600 dark:text-gray-300 hover:text-brand-red active:bg-gray-50 dark:active:bg-gray-800 transition">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span class="text-[10px] font-semibold">{{ auth('customer')->check() ? 'Account' : 'Login' }}</span>
    </a>

</div>

{{-- Spacer so page content clears the mobile bottom bar --}}
<div class="lg:hidden h-16" style="height: calc(4rem + env(safe-area-inset-bottom, 0px));"></div>

</div>
