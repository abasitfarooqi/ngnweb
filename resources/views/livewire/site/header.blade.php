<div>
{{-- Top info strip (desktop) --}}
<div class="hidden lg:block bg-gray-900 text-gray-300 text-xs border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-9">
            <div class="flex items-center gap-6">
                @foreach($branches as $branch)
                    @php $phone = $branch->phone ?? config('site.branches.' . strtolower($branch->name) . '.phone'); @endphp
                    <a href="tel:{{ $phone }}" class="hover:text-white transition">
                        <span class="font-medium">{{ $branch->name }}:</span> {{ $phone }}
                    </a>
                @endforeach
                <a href="https://wa.me/447951790568?text=Hello%20NGN" target="_blank" class="hover:text-white transition">WhatsApp</a>
            </div>
            <div class="flex items-center gap-4">
                <span>{{ config('site.hours', 'Mon–Sat 9am–6pm') }}</span>
                <a href="{{ url('/career') }}" class="hover:text-white transition">Careers</a>
            </div>
        </div>
    </div>
</div>

{{-- Main header --}}
<flux:header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">

    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    {{-- Logo --}}
    <a href="/" class="flex items-center mr-6 lg:mr-10">
        <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-10 w-auto">
    </a>

    {{-- Desktop nav --}}
    <flux:navbar class="hidden lg:flex">

        {{-- SALES dropdown --}}
        <flux:dropdown>
            <flux:navbar.item icon-trailing="chevron-down" class="font-semibold uppercase tracking-wide text-sm">
                Sales
            </flux:navbar.item>
            <flux:navmenu>
                <flux:navmenu.item href="{{ route('motorcycles.new') }}">New Motorcycles</flux:navmenu.item>
                <flux:navmenu.item href="{{ route('motorcycles.used') }}">Used Motorcycles</flux:navmenu.item>
                <flux:navmenu.item href="/finance">Finance</flux:navmenu.item>
                <flux:navmenu.item href="{{ route('road-traffic-accidents') }}">Accident Management</flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>

        <flux:navbar.item
            href="{{ route('rental-hire') }}"
            :current="request()->is('rentals*') || request()->is('rental*')"
            class="font-semibold uppercase tracking-wide text-sm"
        >
            Rentals
        </flux:navbar.item>

        {{-- SERVICES dropdown --}}
        <flux:dropdown>
            <flux:navbar.item icon-trailing="chevron-down" class="font-semibold uppercase tracking-wide text-sm">
                Services
            </flux:navbar.item>
            <flux:navmenu>
                <flux:navmenu.item href="{{ route('services') }}">All Services</flux:navmenu.item>
                <flux:navmenu.item href="/mot">MOT Testing</flux:navmenu.item>
                <flux:navmenu.item href="/repairs">Repairs & Servicing</flux:navmenu.item>
                <flux:navmenu.item href="/recovery">Recovery & Delivery</flux:navmenu.item>
                <flux:navmenu.item href="{{ route('road-traffic-accidents') }}">Accident Management</flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>

        {{-- SHOP dropdown --}}
        <flux:dropdown>
            <flux:navbar.item
                href="{{ route('shop-motorcycle') }}"
                icon-trailing="chevron-down"
                :current="request()->is('shop*')"
                class="font-semibold uppercase tracking-wide text-sm"
            >
                Shop
            </flux:navbar.item>
            <flux:navmenu>
                <flux:navmenu.item href="/shop/accessories">Accessories</flux:navmenu.item>
                <flux:navmenu.item href="/shop/spare-parts">Spare Parts</flux:navmenu.item>
                <flux:navmenu.item href="/shop/gps-tracker">GPS Trackers</flux:navmenu.item>
                <flux:navmenu.item href="/helmets">Helmets</flux:navmenu.item>
                <flux:navmenu.item href="/ebikes">E-Bikes</flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>

        <flux:navbar.item
            href="{{ route('about.page') }}"
            :current="request()->is('about*')"
            class="font-semibold uppercase tracking-wide text-sm"
        >
            About
        </flux:navbar.item>

        <flux:navbar.item
            href="{{ route('contact.me') }}"
            :current="request()->is('contact*')"
            class="font-semibold uppercase tracking-wide text-sm"
        >
            Contact
        </flux:navbar.item>

    </flux:navbar>

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

{{-- Mobile sidebar --}}
<flux:sidebar stashable class="lg:hidden bg-white dark:bg-gray-900">

    <flux:sidebar.toggle icon="x-mark" class="self-end m-3" />

    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-8 w-auto">
    </div>

    {{-- Mobile search --}}
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <form action="{{ route('ngn_search_results') }}" method="GET">
            <flux:input type="search" name="query" placeholder="Search…" />
        </form>
    </div>

    <flux:navlist class="flex-1 py-2">
        <flux:navlist.group heading="Sales" :expandable="true">
            <flux:navlist.item href="{{ route('motorcycles.new') }}">New Motorcycles</flux:navlist.item>
            <flux:navlist.item href="{{ route('motorcycles.used') }}">Used Motorcycles</flux:navlist.item>
            <flux:navlist.item href="/finance">Finance</flux:navlist.item>
            <flux:navlist.item href="{{ route('road-traffic-accidents') }}">Accident Management</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item href="{{ route('rental-hire') }}" :current="request()->is('rental*')">Rentals</flux:navlist.item>

        <flux:navlist.group heading="Services" :expandable="true">
            <flux:navlist.item href="{{ route('services') }}">All Services</flux:navlist.item>
            <flux:navlist.item href="/mot">MOT Testing</flux:navlist.item>
            <flux:navlist.item href="/repairs">Repairs & Servicing</flux:navlist.item>
            <flux:navlist.item href="/recovery">Recovery & Delivery</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group heading="Shop" :expandable="true">
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
