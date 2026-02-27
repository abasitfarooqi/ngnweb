<nav class="ngn-navbar" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('v2.home') }}" class="flex items-center gap-2 flex-shrink-0">
                <span class="text-orange-400 font-black text-2xl tracking-tight">NGN</span>
                <span class="text-white font-light text-sm tracking-widest uppercase">Motors</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('v2.bikes.sale') }}" class="ngn-navbar-link {{ request()->routeIs('v2.bikes.*') ? 'active' : '' }}">Bikes for Sale</a>
                <a href="{{ route('v2.rentals') }}" class="ngn-navbar-link {{ request()->routeIs('v2.rental*') ? 'active' : '' }}">Rentals</a>
                <a href="{{ route('v2.services') }}" class="ngn-navbar-link {{ request()->routeIs('v2.service*') ? 'active' : '' }}">Servicing</a>
                <a href="{{ route('v2.recovery') }}" class="ngn-navbar-link {{ request()->routeIs('v2.recovery') ? 'active' : '' }}">Recovery</a>
                <a href="{{ route('v2.mot.checker') }}" class="ngn-navbar-link {{ request()->routeIs('v2.mot*') ? 'active' : '' }}">MOT Checker</a>
                <a href="{{ route('v2.about') }}" class="ngn-navbar-link {{ request()->routeIs('v2.about') ? 'active' : '' }}">About</a>
                <a href="{{ route('v2.contact') }}" class="ml-2 btn-ngn text-xs py-2 px-4">Contact Us</a>
            </div>

            {{-- Mobile toggle --}}
            <button class="md:hidden p-2 text-zinc-300 hover:text-white" @click="open = !open" aria-label="Menu">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="md:hidden mobile-nav-overlay" x-show="open" x-transition @click.outside="open = false">
        <div class="flex flex-col gap-2 mt-4">
            <a href="{{ route('v2.home') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">Home</a>
            <a href="{{ route('v2.bikes.sale') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">Bikes for Sale</a>
            <a href="{{ route('v2.rentals') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">Rentals</a>
            <a href="{{ route('v2.rental.qr.booking') }}" @click="open = false" class="text-base text-orange-400 py-2 border-b border-zinc-800">&#9654; QR Rental Booking</a>
            <a href="{{ route('v2.services') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">Servicing</a>
            <a href="{{ route('v2.recovery') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">Recovery</a>
            <a href="{{ route('v2.mot.checker') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">MOT Checker</a>
            <a href="{{ route('v2.about') }}" @click="open = false" class="text-lg font-semibold text-white py-3 border-b border-zinc-800">About</a>
            <a href="{{ route('v2.contact') }}" @click="open = false" class="mt-4 btn-ngn w-full justify-center text-base py-3">Contact Us</a>
        </div>
    </div>
</nav>
