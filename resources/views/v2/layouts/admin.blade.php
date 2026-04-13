<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin – NGN Motors' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950">

<flux:sidebar sticky stashable class="bg-gray-900 dark:bg-gray-950 border-r border-gray-800">

    <flux:sidebar.toggle class="lg:hidden text-white" icon="x-mark" />

    <a href="/admin" class="flex items-center gap-3 px-4 py-4 border-b border-gray-800">
        <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN" class="h-7 w-auto brightness-0 invert">
        <span class="text-sm font-semibold text-white">NGN Admin</span>
    </a>

    <div class="px-4 py-3 border-b border-gray-800">
        <p class="text-xs text-gray-500">Signed in as</p>
        <p class="text-sm font-medium text-gray-200 truncate">{{ Auth::user()->name ?? Auth::user()->email ?? 'Admin' }}</p>
    </div>

    <flux:navlist class="flex-1 py-2 [&_.flux-navlist-item]:text-gray-300 [&_.flux-navlist-item:hover]:text-white [&_.flux-navlist-item:hover]:bg-gray-800">

        <flux:navlist.item icon="home" href="/admin" :current="request()->is('admin')">Dashboard</flux:navlist.item>

        <flux:navlist.group icon="clock" heading="Rentals" :expandable="true" :expanded="request()->is('admin/renting*')">
            <flux:navlist.item href="/admin/renting" :current="request()->is('admin/renting')">Active Rentals</flux:navlist.item>
            <flux:navlist.item href="/admin/renting/bookings" :current="request()->is('admin/renting/bookings')">All Bookings</flux:navlist.item>
            <flux:navlist.item href="/admin/renting/inactive-bookings">Inactive Bookings</flux:navlist.item>
            <flux:navlist.item href="/admin/renting/booking-new">New Booking</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group icon="wrench-screwdriver" heading="Motorcycles" :expandable="true" :expanded="request()->is('admin/motorbikes*')">
            <flux:navlist.item href="/admin/motorbikes" :current="request()->is('admin/motorbikes')">All Bikes</flux:navlist.item>
            <flux:navlist.item href="/admin/motorbikes/create">Add New Bike</flux:navlist.item>
            <flux:navlist.item href="/admin/motorbikes/used-for-sale">For Sale (Used)</flux:navlist.item>
            <flux:navlist.item href="/admin/motorbikes/pricing">Pricing</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item icon="users" href="/admin/customers" :current="request()->is('admin/customers')">Customers</flux:navlist.item>

        <flux:navlist.group icon="banknotes" heading="Finance" :expandable="true" :expanded="request()->is('admin/finance*')">
            <flux:navlist.item href="/admin/finance/dashboard">Finance Dashboard</flux:navlist.item>
            <flux:navlist.item href="/admin/finance/application-new">New Application</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item icon="credit-card" href="/admin/judopay-mit-dashboard" :current="request()->is('admin/judopay*')">Payments</flux:navlist.item>

        <flux:navlist.group icon="puzzle-piece" heading="Other" :expandable="true">
            <flux:navlist.item href="/admin/spareparts">Spare Parts</flux:navlist.item>
            <flux:navlist.item href="/admin/ngn_club">NGN Club</flux:navlist.item>
            <flux:navlist.item href="/admin/survey_index">Surveys</flux:navlist.item>
            <flux:navlist.item href="/admin/rotas-view">Rotas</flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item icon="users" href="/admin/users" :current="request()->is('admin/users')">Users</flux:navlist.item>

    </flux:navlist>

    <div class="border-t border-gray-800 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-sm text-gray-500 hover:text-white transition">
                <flux:icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                Sign out
            </button>
        </form>
    </div>

</flux:sidebar>

<flux:main class="flex-1 min-h-screen">

    <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-gray-900 border-b border-gray-800">
        <flux:sidebar.toggle icon="bars-2" class="text-white" />
        <span class="text-white font-semibold text-sm">NGN Admin</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-gray-400 hover:text-white text-xs">Sign out</button>
        </form>
    </div>

    @if(isset($header))
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
            {{ $header }}
        </div>
    @endif

    <div class="p-6 md:p-8">
        {{ $slot }}
    </div>

</flux:main>

@fluxScripts
@livewireScripts
@stack('scripts')
</body>
</html>
