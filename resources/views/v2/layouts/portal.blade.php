<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'My Account – NGN Motors' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950">

<flux:sidebar sticky stashable class="bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800">

    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    {{-- Logo --}}
    <a href="/" class="flex items-center gap-3 px-4 py-4 border-b border-gray-200 dark:border-gray-800">
        <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-8 w-auto">
        <span class="font-semibold text-sm text-gray-600 dark:text-gray-400">My Account</span>
    </a>

    {{-- Account info --}}
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
            {{ auth('customer')->user()->email ?? 'Account' }}
        </p>
    </div>

    <flux:navlist class="flex-1 py-2">

        <flux:navlist.item
            icon="home"
            href="{{ route('account.dashboard') }}"
            :current="request()->routeIs('account.dashboard')"
        >
            Dashboard
        </flux:navlist.item>

        <flux:navlist.item
            icon="user"
            href="{{ route('account.profile') }}"
            :current="request()->routeIs('account.profile')"
        >
            Profile
        </flux:navlist.item>

        <flux:navlist.item
            icon="document-text"
            href="{{ route('account.documents') }}"
            :current="request()->routeIs('account.documents')"
        >
            Documents
        </flux:navlist.item>

        <flux:navlist.group
            icon="calendar"
            heading="Repairs & MOT"
            :expandable="true"
            :expanded="request()->routeIs('account.bookings') || request()->routeIs('account.mot.*') || request()->routeIs('account.repairs.*')"
        >
            <flux:navlist.item href="{{ route('account.bookings') }}" :current="request()->routeIs('account.bookings')">My Bookings</flux:navlist.item>
            <flux:navlist.item href="{{ route('account.mot.book') }}" :current="request()->routeIs('account.mot.book')">Book MOT</flux:navlist.item>
            <flux:navlist.item href="{{ route('account.repairs.request') }}" :current="request()->routeIs('account.repairs.request')">Repair enquiry</flux:navlist.item>
            <flux:navlist.item href="{{ route('account.repairs.appointment') }}" :current="request()->routeIs('account.repairs.appointment')">Repairs appointment</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group
            icon="clock"
            heading="Rentals"
            :expandable="true"
            :expanded="request()->routeIs('account.rentals*')"
        >
            <flux:navlist.item href="{{ route('account.rentals') }}" :current="request()->routeIs('account.rentals') || request()->routeIs('account.rentals.browse')">Browse Rentals</flux:navlist.item>
            <flux:navlist.item href="{{ route('account.rentals.my-rentals') }}" :current="request()->routeIs('account.rentals.my-rentals')">My Rentals</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group
            icon="banknotes"
            heading="Finance"
            :expandable="true"
            :expanded="request()->routeIs('account.finance*')"
        >
            <flux:navlist.item href="{{ route('account.finance') }}" :current="request()->routeIs('account.finance') || request()->routeIs('account.finance.browse')">Browse Finance</flux:navlist.item>
            <flux:navlist.item href="{{ route('account.finance.my-applications') }}" :current="request()->routeIs('account.finance.my-applications')">My Applications</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group
            icon="bolt"
            heading="Recovery"
            :expandable="true"
            :expanded="request()->routeIs('account.recovery*')"
        >
            <flux:navlist.item href="{{ route('account.recovery') }}" :current="request()->routeIs('account.recovery') || request()->routeIs('account.recovery.request')">Request Recovery</flux:navlist.item>
            <flux:navlist.item href="{{ route('account.recovery.my-requests') }}" :current="request()->routeIs('account.recovery.my-requests')">My Requests</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item
            icon="shopping-bag"
            href="{{ route('account.orders') }}"
            :current="request()->routeIs('account.orders')"
        >
            Orders
        </flux:navlist.item>

        <flux:navlist.item
            icon="star"
            href="{{ route('account.club') }}"
            :current="request()->routeIs('account.club')"
        >
            NGN Club
        </flux:navlist.item>

        <flux:navlist.item
            icon="shield-check"
            href="{{ route('account.security') }}"
            :current="request()->routeIs('account.security')"
        >
            Security
        </flux:navlist.item>

    </flux:navlist>

    {{-- Sign out --}}
    <div class="border-t border-gray-200 dark:border-gray-800 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red transition w-full">
                <flux:icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                Sign out
            </button>
        </form>
    </div>

</flux:sidebar>

<flux:main class="flex-1 min-h-screen">

    {{-- Mobile header --}}
    <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <flux:sidebar.toggle icon="bars-2" />
        <a href="/"><img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN" class="h-7 w-auto"></a>
        <a href="/" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red">← Site</a>
    </div>

    <div class="p-6 md:p-8">
        {{ $slot }}
    </div>

</flux:main>

@fluxScripts
@livewireScripts
</body>
</html>
