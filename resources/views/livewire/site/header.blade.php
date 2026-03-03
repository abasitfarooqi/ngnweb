<div>
{{-- Top info strip: Catford, Sutton, Tooting, WhatsApp (all breakpoints) --}}
@if($branches && $branches->isNotEmpty())
<div class="bg-gray-900 text-gray-300 text-xs border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 py-2 lg:py-0 lg:h-9 lg:flex-nowrap">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                @foreach($branches as $branch)
                    @php $phone = $branch->phone ?? config('site.branches.' . strtolower($branch->name ?? '') . '.phone', ''); @endphp
                    @if($phone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hover:text-white transition">
                        <span class="font-medium">{{ $branch->name }}:</span> {{ $phone }}
                    </a>
                    @endif
                @endforeach
                <a href="https://wa.me/{{ config('site.whatsapp', '447951790568') }}?text=Hello%20NGN" target="_blank" rel="noopener" class="hover:text-white transition">WhatsApp</a>
            </div>
            <div class="flex items-center gap-4">
                <span>{{ config('site.hours', 'Mon–Sat 9am–6pm') }}</span>
                <a href="{{ url('/career') }}" class="hover:text-white transition">Careers</a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Main header --}}
<flux:header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">

    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    {{-- Logo --}}
    <a href="/" class="flex items-center mr-6 lg:mr-10">
        <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-10 w-auto">
    </a>

    {{-- Desktop nav: Alpine dropdowns so only one open at a time, close on mouse leave --}}
    <nav class="hidden lg:flex items-center gap-1" x-data="{ open: null }" @mouseleave="open = null">
        {{-- SALES --}}
        <div class="relative" @mouseenter="open = 'sales'">
            <button type="button" class="flux-navbar-item flex items-center gap-1 px-3 py-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 hover:text-brand-red dark:hover:text-brand-red">
                Sales <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open === 'sales'" x-transition x-cloak class="absolute left-0 top-full z-50 min-w-[200px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                <a href="{{ route('motorcycles.new') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">New Motorcycles</a>
                <a href="{{ route('motorcycles.used') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Used Motorcycles</a>
                <a href="/finance" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Finance</a>
                <a href="{{ route('road-traffic-accidents') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Accident Management</a>
            </div>
        </div>

        <a href="{{ route('rental-hire') }}" class="flux-navbar-item px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('rentals*') || request()->is('rental*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }}">Rentals</a>

        {{-- SERVICES --}}
        <div class="relative" @mouseenter="open = 'services'">
            <button type="button" class="flux-navbar-item flex items-center gap-1 px-3 py-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 hover:text-brand-red dark:hover:text-brand-red">
                Services <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open === 'services'" x-transition x-cloak class="absolute left-0 top-full z-50 min-w-[200px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                <a href="{{ route('services') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All Services</a>
                <a href="/mot" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">MOT Testing</a>
                <a href="/repairs" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Repairs &amp; Servicing</a>
                <a href="/recovery" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Recovery &amp; Delivery</a>
                <a href="{{ route('road-traffic-accidents') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Accident Management</a>
            </div>
        </div>

        {{-- SHOP --}}
        <div class="relative" @mouseenter="open = 'shop'">
            <button type="button" class="flux-navbar-item flex items-center gap-1 px-3 py-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 hover:text-brand-red dark:hover:text-brand-red">
                Shop <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open === 'shop'" x-transition x-cloak class="absolute left-0 top-full z-50 min-w-[200px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                <a href="{{ route('shop-motorcycle') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All Shop</a>
                <a href="/shop/accessories" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Accessories</a>
                <a href="/shop/spare-parts" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Spare Parts</a>
                <a href="/shop/gps-tracker" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">GPS Trackers</a>
                <a href="/helmets" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Helmets</a>
                <a href="/ebikes" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">E-Bikes</a>
            </div>
        </div>

        <a href="{{ route('about.page') }}" class="flux-navbar-item px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('about*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }}">About</a>
        <a href="{{ route('contact.me') }}" class="flux-navbar-item px-3 py-2 text-sm font-semibold uppercase tracking-wide {{ request()->is('contact*') ? 'text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }}">Contact</a>
    </nav>

    <flux:spacer />

    {{-- Search bar (desktop) --}}
    <div class="hidden lg:flex items-center mr-4" x-data="{ open: false }">
        <button @click="open = !open" class="text-gray-500 hover:text-brand-red transition p-2" aria-label="Search">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
        <div x-show="open" x-transition @click.outside="open = false" class="absolute top-full right-0 mt-1 w-80 bg-white dark:bg-gray-900 shadow-lg border border-gray-200 dark:border-gray-700 p-3">
            <form action="{{ route('ngn_search_results') }}" method="GET">
                <flux:input type="search" name="query" placeholder="Search motorcycles, parts…" autofocus />
            </form>
        </div>
    </div>

    {{-- Auth links --}}
    <div class="hidden lg:flex items-center gap-3">
        @auth('customer')
            <flux:button href="{{ route('account.dashboard') }}" variant="ghost" size="sm" icon="user">
                My Account
            </flux:button>
        @else
            <flux:button href="{{ route('login') }}" variant="ghost" size="sm">Login</flux:button>
            <flux:button href="{{ route('register') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">
                Register
            </flux:button>
        @endauth
    </div>

    {{-- NGN Club badge --}}
    <a href="{{ route('ngnclub.subscribe') }}" class="hidden lg:flex items-center gap-1 ml-2 px-3 py-1.5 bg-amber-400 text-gray-900 text-xs font-bold uppercase tracking-wide hover:bg-amber-300 transition">
        ★ Join Club
    </a>

</flux:header>

{{-- Mobile sidebar: one logo only (main header has logo); close button closes menu --}}
<flux:sidebar stashable class="lg:hidden bg-white dark:bg-gray-900 h-full max-h-screen flex flex-col" style="max-height: 100dvh;">

    <div class="flex items-center justify-end shrink-0 px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <span class="mr-auto text-sm font-semibold text-gray-700 dark:text-gray-300">Menu</span>
        <flux:sidebar.toggle icon="x-mark" class="p-2 -m-2" aria-label="Close menu" />
    </div>

    {{-- Mobile search --}}
    <div class="shrink-0 px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <form action="{{ route('ngn_search_results') }}" method="GET">
            <flux:input type="search" name="query" placeholder="Search…" />
        </form>
    </div>

    {{-- Scrollable nav: overflow-y-auto so long menus scroll --}}
    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain py-2">
    <flux:navlist class="py-2">
        <flux:navlist.group heading="Sales" :expandable="true" :expanded="false">
            <flux:navlist.item href="{{ route('motorcycles.new') }}">New Motorcycles</flux:navlist.item>
            <flux:navlist.item href="{{ route('motorcycles.used') }}">Used Motorcycles</flux:navlist.item>
            <flux:navlist.item href="/finance">Finance</flux:navlist.item>
            <flux:navlist.item href="{{ route('road-traffic-accidents') }}">Accident Management</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item href="{{ route('rental-hire') }}" :current="request()->is('rental*')">Rentals</flux:navlist.item>

        <flux:navlist.group heading="Services" :expandable="true" :expanded="false">
            <flux:navlist.item href="{{ route('services') }}">All Services</flux:navlist.item>
            <flux:navlist.item href="/mot">MOT Testing</flux:navlist.item>
            <flux:navlist.item href="/repairs">Repairs & Servicing</flux:navlist.item>
            <flux:navlist.item href="/recovery">Recovery & Delivery</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group heading="Shop" :expandable="true" :expanded="false">
            <flux:navlist.item href="{{ route('shop-motorcycle') }}">All Shop</flux:navlist.item>
            <flux:navlist.item href="/shop/accessories">Accessories</flux:navlist.item>
            <flux:navlist.item href="/shop/spare-parts">Spare Parts</flux:navlist.item>
            <flux:navlist.item href="/ebikes">E-Bikes</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item href="{{ route('about.page') }}">About</flux:navlist.item>
        <flux:navlist.item href="{{ route('contact.me') }}">Contact</flux:navlist.item>
        <flux:navlist.item href="/locations">Locations</flux:navlist.item>

        @auth('customer')
            <flux:navlist.item icon="user" href="{{ route('account.dashboard') }}">My Account</flux:navlist.item>
        @else
            <flux:navlist.item href="{{ route('login') }}">Login</flux:navlist.item>
            <flux:navlist.item href="{{ route('register') }}">Register</flux:navlist.item>
        @endauth

        <flux:navlist.item href="{{ route('ngnclub.subscribe') }}" class="text-amber-600 font-semibold">★ Join NGN Club</flux:navlist.item>
    </flux:navlist>
    </div>

    {{-- Mobile branch phones --}}
    <div class="border-t border-gray-200 dark:border-gray-800 p-4 space-y-2">
        @foreach($branches as $branch)
            @php $phone = $branch->phone ?? config('site.branches.' . strtolower($branch->name) . '.phone'); @endphp
            <a href="tel:{{ $phone }}" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ $branch->name }}: {{ $phone }}
            </a>
        @endforeach
    </div>

</flux:sidebar>

{{-- Mobile sticky bottom action bar --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 grid grid-cols-4">
    <button
        type="button"
        x-data
        @click="$flux.modal('quick-book').show()"
        class="flex flex-col items-center py-3 text-gray-700 dark:text-gray-300 hover:text-brand-red transition"
    >
        <svg class="h-5 w-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="text-xs">Book</span>
    </button>

    <a href="tel:02083141498" class="flex flex-col items-center py-3 text-gray-700 dark:text-gray-300 hover:text-brand-red transition">
        <svg class="h-5 w-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
        <span class="text-xs">Call</span>
    </a>

    <a href="https://wa.me/447951790568?text=Hello%20NGN" target="_blank" class="flex flex-col items-center py-3 text-gray-700 dark:text-gray-300 hover:text-green-600 transition">
        <svg class="h-5 w-5 mb-1" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <span class="text-xs">WhatsApp</span>
    </a>

    <a href="{{ auth('customer')->check() ? route('account.dashboard') : route('login') }}" class="flex flex-col items-center py-3 text-gray-700 dark:text-gray-300 hover:text-brand-red transition">
        <svg class="h-5 w-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span class="text-xs">Account</span>
    </a>
</div>

{{-- Spacer to prevent content being hidden behind mobile bottom bar --}}
<div class="lg:hidden h-14"></div>
</div>
