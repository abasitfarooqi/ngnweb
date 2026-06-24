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
    <x-ngn-assets />
    @fluxAppearance
    @include('components.partials.theme-api')
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
    {{-- Flux admin: table row lines, toolbar controls (no rounded corners in toolbars per product preference). --}}
    <style>
        .flux-admin-skip {
            position: fixed;
            left: 1rem;
            top: .75rem;
            z-index: 60;
            transform: translateY(-150%);
            background: rgb(24 24 27);
            color: white;
            padding: .5rem .75rem;
            font-size: .875rem;
            line-height: 1.25rem;
            transition: transform .15s ease;
        }
        .flux-admin-skip:focus { transform: translateY(0); outline: 2px solid rgb(59 130 246); outline-offset: 2px; }
        .flux-admin-content [data-flux-table] tbody tr + tr td { box-shadow: inset 0 1px 0 0 rgb(228 228 231); }
        .dark .flux-admin-content [data-flux-table] tbody tr + tr td { box-shadow: inset 0 1px 0 0 rgb(63 63 70); }
        .flux-admin-toolbar [data-flux-input] input,
        .flux-admin-toolbar [data-flux-input] button,
        .flux-admin-toolbar select[data-flux-control] { border-radius: 0 !important; }
        .flux-admin-toolbar [data-flux-field] { margin-bottom: 0; }
        .flux-admin-table-panel { -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain; }
        .flux-admin-responsive-table {
            min-width: 0;
        }
        body.flux-admin-app [data-flux-table] {
            width: max-content;
            min-width: 100%;
            table-layout: auto;
        }
        body.flux-admin-app [data-flux-table] th,
        body.flux-admin-app [data-flux-table] td {
            white-space: nowrap;
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        body.flux-admin-app [data-flux-table] thead th {
            padding-top: 0.875rem;
            padding-bottom: 0.875rem;
        }
        body.flux-admin-app [data-flux-table] th:first-child,
        body.flux-admin-app [data-flux-table] td:first-child {
            padding-left: 1.25rem;
        }
        body.flux-admin-app [data-flux-table] th:last-child,
        body.flux-admin-app [data-flux-table] td:last-child {
            position: sticky;
            right: 0;
            z-index: 20;
            padding-right: 1.25rem;
            background-color: rgb(255 255 255);
            box-shadow: -12px 0 16px -16px rgb(24 24 27 / .45), -1px 0 0 0 rgb(228 228 231);
        }
        .dark body.flux-admin-app [data-flux-table] th:last-child,
        .dark body.flux-admin-app [data-flux-table] td:last-child {
            background-color: rgb(9 9 11);
            box-shadow: -12px 0 16px -16px rgb(0 0 0 / .65), -1px 0 0 0 rgb(63 63 70);
        }
        body.flux-admin-app [data-flux-table] th:last-child {
            z-index: 25;
        }
        @media (max-width: 1535px) {
            body.flux-admin-app [data-flux-table] > :is(thead, tbody) > tr > :nth-child(n + 11):not(:last-child) {
                display: none;
            }
        }
        @media (max-width: 1279px) {
            body.flux-admin-app [data-flux-table] > :is(thead, tbody) > tr > :nth-child(n + 9):not(:last-child) {
                display: none;
            }
        }
        @media (max-width: 1023px) {
            body.flux-admin-app [data-flux-table] > :is(thead, tbody) > tr > :nth-child(n + 7):not(:last-child) {
                display: none;
            }
        }
        @media (max-width: 767px) {
            body.flux-admin-app [data-flux-table] > :is(thead, tbody) > tr > :nth-child(n + 5):not(:last-child) {
                display: none;
            }
        }
        @media (max-width: 639px) {
            body.flux-admin-app [data-flux-table] > :is(thead, tbody) > tr > :nth-child(n + 3):not(:last-child) {
                display: none;
            }
        }
        .flux-admin-page-title {
            overflow-wrap: anywhere;
            letter-spacing: 0;
        }
        .flux-admin-actions {
            flex-shrink: 0;
        }
        .flux-admin-actions [data-flux-button] {
            min-height: 2rem;
        }
        .flux-admin-content input,
        .flux-admin-content select,
        .flux-admin-content textarea {
            min-width: 0;
        }
        .flux-admin-content [data-flux-button]:focus-visible,
        .flux-admin-content a:focus-visible,
        body.flux-admin-app [data-flux-sidebar] a:focus-visible,
        body.flux-admin-app [data-flux-sidebar] button:focus-visible {
            outline: 2px solid rgb(59 130 246);
            outline-offset: 2px;
        }
        {{-- Sidebar host: belt-and-braces if any global rule or UA style fights Tailwind dark surface. --}}
        html.dark body.flux-admin-app [data-flux-sidebar] {
            background-color: rgb(18 18 20);
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
        {{-- Flux Pro `navlist.group expandable` uses Tailwind v4 `data-open:*` / `group-data-open/*` variants, which Tailwind v3 (this project) does not compile. Flux JS propagates `data-open` onto <ui-disclosure>, the trigger button, and the panel div on toggle (flux.js L7194-7196); hook directly on those so clicks actually open/close. --}}
        [data-flux-navlist-group][data-open] > div.hidden,
        [data-flux-navlist-group] > div[data-open] { display: block; }
        [data-flux-navlist-group][data-open] > button > div > svg.hidden,
        [data-flux-navlist-group] > button[data-open] > div > svg.hidden { display: block; }
        [data-flux-navlist-group][data-open] > button > div > svg.block,
        [data-flux-navlist-group] > button[data-open] > div > svg.block { display: none; }

        {{-- Sidebar redesign: clear left navigation with compact operations shortcuts and sectioned menus. --}}
        body.flux-admin-app [data-flux-sidebar] {
            min-width: 18rem;
            border-right-color: rgb(212 212 216);
            background:
                linear-gradient(180deg, rgb(255 255 255) 0%, rgb(250 250 250) 100%);
            box-shadow: 1px 0 0 rgb(255 255 255 / .7) inset, 16px 0 36px rgb(24 24 27 / .06);
        }
        html.dark body.flux-admin-app [data-flux-sidebar] {
            border-right-color: rgb(39 39 42);
            background:
                linear-gradient(180deg, rgb(24 24 27) 0%, rgb(18 18 20) 100%);
            box-shadow: 1px 0 0 rgb(255 255 255 / .04) inset, 16px 0 36px rgb(0 0 0 / .32);
        }
        @media (min-width: 1024px) {
            body.flux-admin-app [data-flux-sidebar] {
                min-width: 19rem;
                width: 19rem;
            }
        }
        {{-- Ensure sidebar fills viewport height and inner navlist scrolls properly
             on both desktop (sticky) and mobile (stashable overlay). --}}
        body.flux-admin-app [data-flux-sidebar] {
            height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        body.flux-admin-app [data-flux-sidebar] [data-flux-navlist] {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
            scrollbar-width: thin;
            scrollbar-color: rgb(161 161 170) transparent;
        }
        body.flux-admin-app [data-flux-sidebar] [data-flux-navlist]::-webkit-scrollbar { width: .45rem; }
        body.flux-admin-app [data-flux-sidebar] [data-flux-navlist]::-webkit-scrollbar-thumb {
            background: rgb(161 161 170);
            border-radius: 999px;
        }
        .flux-admin-brand {
            margin: .75rem .75rem .5rem;
            border: 1px solid rgb(228 228 231);
            background: rgb(255 255 255);
            padding: .75rem;
            box-shadow: 0 1px 2px rgb(24 24 27 / .05);
        }
        html.dark .flux-admin-brand {
            border-color: rgb(39 39 42);
            background: rgb(9 9 11 / .72);
            box-shadow: 0 1px 2px rgb(0 0 0 / .28);
        }
        .flux-admin-brand-mark {
            display: flex;
            height: 2.5rem;
            width: 2.5rem;
            align-items: center;
            justify-content: center;
            border: 1px solid rgb(228 228 231);
            background: rgb(250 250 250);
        }
        html.dark .flux-admin-brand-mark {
            border-color: rgb(63 63 70);
            background: rgb(24 24 27);
        }
        .flux-admin-quick-grid {
            margin: 0 .75rem .75rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        .flux-admin-quick-link {
            display: flex;
            min-height: 3rem;
            align-items: center;
            gap: .55rem;
            border: 1px solid rgb(228 228 231);
            background: rgb(255 255 255);
            padding: .55rem .65rem;
            color: rgb(63 63 70);
            font-size: .78rem;
            font-weight: 650;
            line-height: 1.1rem;
            transition: border-color .15s ease, background-color .15s ease, color .15s ease, transform .15s ease;
        }
        .flux-admin-quick-link:hover {
            transform: translateY(-1px);
            border-color: rgb(16 185 129);
            background: rgb(240 253 250);
            color: rgb(20 83 45);
        }
        html.dark .flux-admin-quick-link {
            border-color: rgb(39 39 42);
            background: rgb(24 24 27);
            color: rgb(212 212 216);
        }
        html.dark .flux-admin-quick-link:hover {
            border-color: rgb(20 184 166);
            background: rgb(19 78 74 / .34);
            color: rgb(240 253 250);
        }
        .flux-admin-quick-icon {
            display: inline-flex;
            height: 1.65rem;
            width: 1.65rem;
            shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid rgb(228 228 231);
            background: rgb(250 250 250);
            color: rgb(9 9 11);
        }
        html.dark .flux-admin-quick-icon {
            border-color: rgb(63 63 70);
            background: rgb(9 9 11);
            color: rgb(244 244 245);
        }
        .flux-admin-menu {
            padding: .35rem .65rem .75rem;
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-item],
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button {
            min-height: 2.25rem;
            border-radius: .5rem;
            padding: .52rem .7rem;
            color: rgb(82 82 91);
            font-size: .875rem;
            font-weight: 560;
            line-height: 1.2rem;
            letter-spacing: 0;
            transition: background-color .15s ease, color .15s ease, box-shadow .15s ease;
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-item],
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button {
            color: rgb(212 212 216);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-item]:hover,
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button:hover {
            background: rgb(244 244 245);
            color: rgb(24 24 27);
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-item]:hover,
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button:hover {
            background: rgb(39 39 42);
            color: rgb(255 255 255);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-item][data-current],
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-item][aria-current="page"] {
            background: rgb(20 184 166);
            color: white;
            box-shadow: 0 8px 18px rgb(20 184 166 / .22);
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-item][data-current],
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-item][aria-current="page"] {
            background: rgb(20 184 166);
            color: rgb(6 78 59);
            box-shadow: 0 8px 18px rgb(20 184 166 / .18);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] {
            margin: .35rem 0;
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button {
            border: 1px solid transparent;
            color: rgb(39 39 42);
            font-weight: 700;
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button {
            color: rgb(244 244 245);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group][data-open] > button,
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button[data-open] {
            border-color: rgb(228 228 231);
            background: rgb(255 255 255);
            box-shadow: 0 1px 2px rgb(24 24 27 / .04);
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group][data-open] > button,
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button[data-open] {
            border-color: rgb(63 63 70);
            background: rgb(24 24 27);
            box-shadow: 0 1px 2px rgb(0 0 0 / .22);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > div {
            margin: .25rem 0 .4rem .45rem;
            border-left: 1px solid rgb(228 228 231);
            padding-left: .45rem;
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > div {
            border-left-color: rgb(63 63 70);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > div [data-flux-navlist-item] {
            min-height: 2rem;
            padding-top: .42rem;
            padding-bottom: .42rem;
            font-size: .835rem;
            font-weight: 520;
        }
        .flux-admin-sidebar-footer {
            margin: .5rem .75rem .75rem;
            border: 1px solid rgb(228 228 231);
            background: rgb(255 255 255);
            padding: .5rem;
        }
        html.dark .flux-admin-sidebar-footer {
            border-color: rgb(39 39 42);
            background: rgb(9 9 11 / .7);
        }
        @media (max-width: 767px) {
            .flux-admin-content {
                padding-left: .75rem !important;
                padding-right: .75rem !important;
            }
            .flux-admin-page-title {
                font-size: 1.375rem;
                line-height: 1.75rem;
            }
            .flux-admin-table-panel [data-flux-table] {
                font-size: .8125rem;
            }
            body.flux-admin-app [data-flux-table] th,
            body.flux-admin-app [data-flux-table] td {
                padding: 0.625rem 0.75rem;
            }
            body.flux-admin-app [data-flux-table] th:first-child,
            body.flux-admin-app [data-flux-table] td:first-child {
                padding-left: 1rem;
            }
            body.flux-admin-app [data-flux-table] th:last-child,
            body.flux-admin-app [data-flux-table] td:last-child {
                padding-right: 1rem;
            }
            .flux-admin-toolbar {
                position: sticky;
                top: 0;
                z-index: 5;
            }
        }
    </style>
</head>
<body class="flux-admin-app min-h-dvh bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans antialiased lg:flex lg:min-h-screen lg:flex-row">
    <a href="#flux-admin-main" class="flux-admin-skip">Skip to content</a>

    {{-- Sidebar: same dark surface as main canvas (no half-light / half-dark split). --}}
    <flux:sidebar sticky stashable class="flux-admin-sidebar z-20 border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950 lg:z-auto lg:min-h-screen lg:shrink-0">
        <button
            type="button"
            class="lg:hidden flex h-10 w-10 shrink-0 items-center justify-center text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
            aria-label="Close menu"
            onclick="document.dispatchEvent(new CustomEvent('flux-sidebar-toggle',{bubbles:true}))"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>

        {{-- Brand --}}
        <a href="{{ route('flux-admin.dashboard') }}" class="flux-admin-brand flex items-center gap-3">
            <span class="flux-admin-brand-mark">
                <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-7 w-auto">
            </span>
            <span class="min-w-0">
                <span class="block truncate text-sm font-bold text-zinc-950 dark:text-white">NGN Flux</span>
                <span class="block truncate text-xs font-medium text-zinc-500 dark:text-zinc-400">Operations admin</span>
            </span>
        </a>

        <div class="flux-admin-quick-grid">
            <a href="{{ route('flux-admin.new-booking.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="calendar-days" class="h-4 w-4" /></span>
                <span>Booking</span>
            </a>
            <a href="{{ route('flux-admin.mot-bookings.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="clipboard-document-check" class="h-4 w-4" /></span>
                <span>MOT</span>
            </a>
            <a href="{{ route('flux-admin.service-bookings.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="wrench-screwdriver" class="h-4 w-4" /></span>
                <span>Services</span>
            </a>
            <a href="{{ route('flux-admin.support-inbox.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="inbox" class="h-4 w-4" /></span>
                <span>Inbox</span>
            </a>
        </div>

        <flux:separator />

        <flux:navlist class="flux-admin-menu min-h-0  overflow-y-auto">
            <flux:navlist.item href="{{ route('flux-admin.dashboard') }}" icon="home" :current="request()->routeIs('flux-admin.dashboard*')">Dashboard</flux:navlist.item>
            <flux:navlist.item href="{{ route('flux-admin.search') }}" icon="magnifying-glass" :current="request()->routeIs('flux-admin.search')">Global search</flux:navlist.item>

            @can('see-menu-ecommerce')
                <flux:navlist.item href="{{ route('flux-admin.ec-orders.index') }}" icon="shopping-cart" :current="request()->routeIs('flux-admin.ec-orders.*')">Online store</flux:navlist.item>
            @endcan

            @can('see-menu-finance')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.finance.*','flux-admin.contract-access.*','flux-admin.application-items.*','flux-admin.contract-extra-items.*','flux-admin.modules.show')" heading="Finance">
                    <flux:navlist.item href="{{ route('flux-admin.modules.show', 'finance') }}" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'finance'">Module home</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.finance.index') }}" :current="request()->routeIs('flux-admin.finance.*')">Create / Edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.contract-access.index') }}" :current="request()->routeIs('flux-admin.contract-access.*')">Contract signature expire</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.application-items.index') }}" :current="request()->routeIs('flux-admin.application-items.*')">Application items</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.contract-extra-items.index') }}" :current="request()->routeIs('flux-admin.contract-extra-items.*')">Contract extras</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-rentals')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.rentals.*','flux-admin.rental-*','flux-admin.new-booking.*','flux-admin.bookings-management.*','flux-admin.inactive-bookings.*','flux-admin.all-bookings.*','flux-admin.booking-invoices.*','flux-admin.booking-invoice-dates.*','flux-admin.change-start-date.*','flux-admin.renting-pricing.*','flux-admin.upload-document-links.*','flux-admin.agreement-access.*','flux-admin.active-rentals.*','flux-admin.rental-due-payments.*','flux-admin.service-videos.*','flux-admin.adjust-weekday.*','flux-admin.active-bookings-summary.*','flux-admin.modules.show')" heading="Rentals">
                    <flux:navlist.item href="{{ route('flux-admin.modules.show', 'rentals') }}" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'rentals'">Module home</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.rental-operations.index') }}" :current="request()->routeIs('flux-admin.rental-operations.*')">Operations hub</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.rentals.index') }}" :current="request()->routeIs('flux-admin.rentals.*')">Rentals list</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.new-booking.index') }}" :current="request()->routeIs('flux-admin.new-booking.*')">New booking</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.bookings-management.index') }}" :current="request()->routeIs('flux-admin.bookings-management.*')">Bookings management</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.inactive-bookings.index') }}" :current="request()->routeIs('flux-admin.inactive-bookings.*')">Inactive bookings</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.all-bookings.index') }}" :current="request()->routeIs('flux-admin.all-bookings.*')">All bookings</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.booking-invoices.index') }}" :current="request()->routeIs('flux-admin.booking-invoices.*')">Booking invoices</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.booking-invoice-dates.index') }}" :current="request()->routeIs('flux-admin.booking-invoice-dates.*')">Booking invoice dates</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.change-start-date.index') }}" :current="request()->routeIs('flux-admin.change-start-date.*')">Change booking start date</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.renting-pricing.index') }}" :current="request()->routeIs('flux-admin.renting-pricing.*')">Add new vehicle (pricing)</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.upload-document-links.index') }}" :current="request()->routeIs('flux-admin.upload-document-links.*')">Document expire date</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.agreement-access.index') }}" :current="request()->routeIs('flux-admin.agreement-access.*')">Signature expire date</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.rental-terminate-links.index') }}" :current="request()->routeIs('flux-admin.rental-terminate-links.*')">Terminate / generate link</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.active-rentals.index') }}" :current="request()->routeIs('flux-admin.active-rentals.*')">Overview</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.rental-due-payments.index') }}" :current="request()->routeIs('flux-admin.rental-due-payments.*')">Due payments</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.service-videos.index') }}" :current="request()->routeIs('flux-admin.service-videos.*')">Renting service videos</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.adjust-weekday.index') }}" :current="request()->routeIs('flux-admin.adjust-weekday.*')">Adjust booking weekday</flux:navlist.item>
                    @role('Admin')
                    <flux:navlist.item href="{{ route('flux-admin.active-bookings-summary.index') }}" :current="request()->routeIs('flux-admin.active-bookings-summary.*')">Active bookings summary</flux:navlist.item>
                    @endrole
                </flux:navlist.group>
            @endcan

            @can('see-menu-pcns')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.pcn.*','flux-admin.pcn-*','flux-admin.modules.show')" heading="PCNs">
                    <flux:navlist.item href="{{ route('flux-admin.modules.show', 'pcn') }}" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'pcn'">Module home</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn.index') }}" :current="request()->routeIs('flux-admin.pcn.index') || request()->routeIs('flux-admin.pcn.show')">Add / Edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn-updates.index') }}" :current="request()->routeIs('flux-admin.pcn-updates.*')">PCN updates</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn-tol-requests.index') }}" :current="request()->routeIs('flux-admin.pcn-tol-requests.*')">TOL requests</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn-dashboard.index') }}" :current="request()->routeIs('flux-admin.pcn-dashboard.*')">Overview</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-services-and-repairs-and-report')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.customer-appointments.*','flux-admin.motorbike-repairs.*','flux-admin.motorbike-repair-updates.*')" heading="Book services / repairs / report">
                    <flux:navlist.item href="{{ route('flux-admin.customer-appointments.index') }}" :current="request()->routeIs('flux-admin.customer-appointments.*')">Services / repairs booking</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-repairs.index') }}" :current="request()->routeIs('flux-admin.motorbike-repairs.*')">Repairs report</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-repair-updates.index') }}" :current="request()->routeIs('flux-admin.motorbike-repair-updates.*')">Repair updates</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-mot-bookings')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.mot-*')" heading="MOT">
                    <flux:navlist.item href="{{ route('flux-admin.mot-bookings.index') }}" :current="request()->routeIs('flux-admin.mot-bookings.*')">Add / Edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.mot-checker.index') }}" :current="request()->routeIs('flux-admin.mot-checker.*')">MOT checker</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.mot-stats.index') }}" :current="request()->routeIs('flux-admin.mot-stats.*')">MOT stats</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-commons')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.customers.*','flux-admin.customer-documents.*','flux-admin.modules.show')" heading="Customers">
                    <flux:navlist.item href="{{ route('flux-admin.modules.show', 'customers') }}" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'customers'">Module home</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.customers.index') }}" :current="request()->routeIs('flux-admin.customers.*')">Customer list</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.customer-documents.index') }}" :current="request()->routeIs('flux-admin.customer-documents.*')">Verify documents</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-b2b')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.inventory-partners.*')" heading="B2B">
                    <flux:navlist.item href="{{ route('flux-admin.inventory-partners.index') }}" :current="request()->routeIs('flux-admin.inventory-partners.*')">Partners</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-inventory')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.inventory-*','flux-admin.oxford-products.*','flux-admin.purchase-request*','flux-admin.store-front.*')" heading="Inventory">
                    <flux:navlist.item href="{{ route('flux-admin.inventory-products.index') }}" :current="request()->routeIs('flux-admin.inventory-products.*')">Products (add / edit)</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.inventory-stock-movements.index') }}" :current="request()->routeIs('flux-admin.inventory-stock-movements.*')">Stock management</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.inventory-brands.index') }}" :current="request()->routeIs('flux-admin.inventory-brands.*')">Brands</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.inventory-categories.index') }}" :current="request()->routeIs('flux-admin.inventory-categories.*')">Categories</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.inventory-models.index') }}" :current="request()->routeIs('flux-admin.inventory-models.*')">Product models</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.oxford-products.index') }}" :current="request()->routeIs('flux-admin.oxford-products.*')">Oxford products</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.purchase-requests.index') }}" :current="request()->routeIs('flux-admin.purchase-requests.*')">Purchase requests</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.purchase-request-items.index') }}" :current="request()->routeIs('flux-admin.purchase-request-items.*')">Purchase request items</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.store-front.index') }}" :current="request()->routeIs('flux-admin.store-front.*')">Store front</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.sp-*')" heading="Spare parts">
                    <flux:navlist.item href="{{ route('flux-admin.sp-parts.index') }}" :current="request()->routeIs('flux-admin.sp-parts.*')">Parts</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-makes.index') }}" :current="request()->routeIs('flux-admin.sp-makes.*')">Makes</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-models.index') }}" :current="request()->routeIs('flux-admin.sp-models.*')">Models</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-fitments.index') }}" :current="request()->routeIs('flux-admin.sp-fitments.*')">Fitments</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-assemblies.index') }}" :current="request()->routeIs('flux-admin.sp-assemblies.*')">Assemblies</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-assembly-parts.index') }}" :current="request()->routeIs('flux-admin.sp-assembly-parts.*')">Assembly parts</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-stock-movements.index') }}" :current="request()->routeIs('flux-admin.sp-stock-movements.*')">Stock movements</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-parts.index') }}" :current="request()->routeIs('flux-admin.sp-parts.*')">Stock handler</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-vehicles')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.motorbikes*','flux-admin.motorbike-compliance.*','flux-admin.motorbike-new.*','flux-admin.ebikes.*','flux-admin.delivery-enquiries.*','flux-admin.vehicle-notifications.*','flux-admin.recovered-motorbikes.*')" heading="Vehicles">
                    <flux:navlist.item href="{{ route('flux-admin.motorbikes-dvla.create') }}" :current="request()->routeIs('flux-admin.motorbikes-dvla.*')">DVLA add / edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbikes.index') }}" :current="request()->routeIs('flux-admin.motorbikes*')">Manual add / edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-compliance.index') }}" :current="request()->routeIs('flux-admin.motorbike-compliance.*')">MOT / TAX compliance</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-compliance.index') }}" :current="request()->routeIs('flux-admin.motorbike-compliance.*')">Vehicle database</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-new.index') }}" :current="request()->routeIs('flux-admin.motorbike-new.*')">New arrivals</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.ebikes.index') }}" :current="request()->routeIs('flux-admin.ebikes.*')">E-bike manager</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.delivery-enquiries.index') }}" :current="request()->routeIs('flux-admin.delivery-enquiries.*')">Delivery enquiries</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.vehicle-notifications.index') }}" :current="request()->routeIs('flux-admin.vehicle-notifications.*')">Vehicle notifications</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.recovered-motorbikes.index') }}" :current="request()->routeIs('flux-admin.recovered-motorbikes.*')">Recovered</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.motorbike-sales.*','flux-admin.motorbike-for-sale.*')" heading="Website">
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-sales.index') }}" :current="request()->routeIs('flux-admin.motorbike-sales.*')">Vehicle sale add / edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-for-sale.index') }}" :current="request()->routeIs('flux-admin.motorbike-for-sale.*')">For sale catalogue</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-claims')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.motorbike-claims.*')" heading="Claims">
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-claims.index') }}" :current="request()->routeIs('flux-admin.motorbike-claims.*')">Add / Edit</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.motorbike-cat-b.index') }}" :current="request()->routeIs('flux-admin.motorbike-cat-b.*')">Category B</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.club-member-vehicles.index') }}" :current="request()->routeIs('flux-admin.club-member-vehicles.*')">Club member vehicles details</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.vehicle-history.index') }}" :current="request()->routeIs('flux-admin.vehicle-history.*')">Vehicle history</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.company-vehicles.index') }}" :current="request()->routeIs('flux-admin.company-vehicles.*')">Company vehicles</flux:navlist.item>

                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.used-purchases.*')" heading="Purchase">
                    <flux:navlist.item href="{{ route('flux-admin.used-purchases.index') }}" :current="request()->routeIs('flux-admin.used-purchases.*')">Add / Edit</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.item href="{{ route('flux-admin.contact-queries.index') }}" :current="request()->routeIs('flux-admin.contact-queries.*')">Contact queries</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.service-bookings.index') }}" :current="request()->routeIs('flux-admin.service-bookings.*')">Service enquiries</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.support-inbox.index') }}" :current="request()->routeIs('flux-admin.support-inbox.*')">Conversations inbox</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.support-conversations.index') }}" :current="request()->routeIs('flux-admin.support-conversations.*')">Support conversations</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.support-messages.index') }}" :current="request()->routeIs('flux-admin.support-messages.*')">Support messages</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.vehicle-delivery-orders.index') }}" :current="request()->routeIs('flux-admin.vehicle-delivery-orders.*')">Motorbike delivery orders</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.careers.index') }}" :current="request()->routeIs('flux-admin.careers.*')">Careers</flux:navlist.item>

                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.blog-*')" heading="Blog management">
                    <flux:navlist.item href="{{ route('flux-admin.blog-posts.index') }}" :current="request()->routeIs('flux-admin.blog-posts.*')">Blog posts</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.blog-categories.index') }}" :current="request()->routeIs('flux-admin.blog-categories.*')">Blog categories</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.blog-tags.index') }}" :current="request()->routeIs('flux-admin.blog-tags.*')">Blog tags</flux:navlist.item>
                </flux:navlist.group>

            @endcan

            @can('see-menu-surveys')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.survey*')" heading="Surveys">
                    <flux:navlist.item href="{{ route('flux-admin.surveys.index') }}" :current="request()->routeIs('flux-admin.surveys.*')">Surveys</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.survey-questions.index') }}" :current="request()->routeIs('flux-admin.survey-questions.*')">Questions</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.survey-options.index') }}" :current="request()->routeIs('flux-admin.survey-options.*')">Options</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.survey-responses.index') }}" :current="request()->routeIs('flux-admin.survey-responses.*')">Responses</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.survey-answers.index') }}" :current="request()->routeIs('flux-admin.survey-answers.*')">Answers</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @role('Admin')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.club.*','flux-admin.club-*','flux-admin.calendar.*','flux-admin.employee-schedules.*','flux-admin.ds-orders.*','flux-admin.ds-order-items.*','flux-admin.digital-invoices.*','flux-admin.digital-invoice-items.*','flux-admin.agent-settings.*','flux-admin.branches.*','flux-admin.vehicle-issuances.*','flux-admin.dev-club-otp.*','flux-admin.queue-monitor.*')" heading="Misc / Experiments">
                    <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.club.*','flux-admin.club-*')" heading="Club members">
                        <flux:navlist.item href="{{ route('flux-admin.club.index') }}" :current="request()->routeIs('flux-admin.club.index') || request()->routeIs('flux-admin.club.show')">Club members</flux:navlist.item>
                        <flux:navlist.item href="{{ route('flux-admin.club-purchases.index') }}" :current="request()->routeIs('flux-admin.club-purchases.*')">Club member purchases</flux:navlist.item>
                        <flux:navlist.item href="{{ route('flux-admin.club-redemptions.index') }}" :current="request()->routeIs('flux-admin.club-redemptions.*')">Club member redeems</flux:navlist.item>
                        <flux:navlist.item href="{{ route('flux-admin.club-spending.index') }}" :current="request()->routeIs('flux-admin.club-spending.*')">0% spendings</flux:navlist.item>
                        <flux:navlist.item href="{{ route('flux-admin.club-spending-payments.index') }}" :current="request()->routeIs('flux-admin.club-spending-payments.*')">Spending payments</flux:navlist.item>
                    </flux:navlist.group>
                    <flux:navlist.item href="{{ route('flux-admin.calendar.index') }}" :current="request()->routeIs('flux-admin.calendar.*')">Calendar</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.employee-schedules.index') }}" :current="request()->routeIs('flux-admin.employee-schedules.*')">Staff schedules</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.ds-orders.index') }}" :current="request()->routeIs('flux-admin.ds-orders.*')">DS orders</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.ds-order-items.index') }}" :current="request()->routeIs('flux-admin.ds-order-items.*')">DS order legs</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.digital-invoices.index') }}" :current="request()->routeIs('flux-admin.digital-invoices.*')">Digital invoices</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.digital-invoice-items.index') }}" :current="request()->routeIs('flux-admin.digital-invoice-items.*')">Invoice items</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.agent-settings.index') }}" :current="request()->routeIs('flux-admin.agent-settings.*')">AI agent settings</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.branches.index') }}" :current="request()->routeIs('flux-admin.branches*')">Branches</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.vehicle-issuances.index') }}" :current="request()->routeIs('flux-admin.vehicle-issuances.*')">Vehicle issuances</flux:navlist.item>
                    <flux:navlist.item href="{{ url('/admin') }}" badge="Old">Old admin panel</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.dev-club-otp.index') }}" :current="request()->routeIs('flux-admin.dev-club-otp.*')">Club OTP viewer</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.queue-monitor.index') }}" :current="request()->routeIs('flux-admin.queue-monitor.*')">Queue monitor</flux:navlist.item>
                </flux:navlist.group>
            @endrole

            @can('see-menu-security')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.ip-restrictions.*','flux-admin.access-logs.*')" heading="Security">
                    <flux:navlist.item href="{{ route('flux-admin.ip-restrictions.index') }}" :current="request()->routeIs('flux-admin.ip-restrictions.*')">IP restrictions</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.access-logs.index') }}" :current="request()->routeIs('flux-admin.access-logs.*')">Access logs</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-permissions')
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.users.*','flux-admin.roles.*','flux-admin.permissions.*')" heading="Permissions">
                    <flux:navlist.item href="{{ route('flux-admin.users.index') }}" :current="request()->routeIs('flux-admin.users.*')">Users</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.roles.index') }}" :current="request()->routeIs('flux-admin.roles.*')">Roles</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.permissions.index') }}" :current="request()->routeIs('flux-admin.permissions.*')">Permissions</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @canany(['see-judopay-home', 'see-judopay'])
                <flux:navlist.group expandable :expanded="request()->routeIs('flux-admin.judopay-*','flux-admin.ngn-mit-queue.*')" heading="Judo Pay">
                    <flux:navlist.item href="{{ route('flux-admin.judopay-recurring.index') }}" :current="request()->routeIs('flux-admin.judopay-recurring.*')">Judo Pay</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-mit-dashboard.index') }}" :current="request()->routeIs('flux-admin.judopay-mit-dashboard.*')">MIT dashboard</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-weekly-queue.index') }}" :current="request()->routeIs('flux-admin.judopay-weekly-queue.*')">Weekly schedule</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-subscriptions.index') }}" :current="request()->routeIs('flux-admin.judopay-subscriptions.*')">Subscriptions</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.ngn-mit-queue.index') }}" :current="request()->routeIs('flux-admin.ngn-mit-queue.*')">NGN MIT queue</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-mit-queue.index') }}" :current="request()->routeIs('flux-admin.judopay-mit-queue.*')">Judopay MIT queue</flux:navlist.item>
                </flux:navlist.group>
            @endcan


        </flux:navlist>

        <flux:spacer />

        {{-- Theme toggle --}}
        <div class="flux-admin-sidebar-footer">
            <button
                type="button"
                x-data
                @click="window.ngnSetColourMode && window.ngnSetColourMode(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                class="flex w-full items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
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
        <flux:header class="hidden shrink-0 items-center border-b border-zinc-200 bg-white px-6 py-3 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 lg:flex">
            <div class="min-w-0">
                <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-500">NGN Motors</div>
                <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $title ?? 'Flux Admin' }}</div>
            </div>
            <flux:spacer />
            <form action="{{ route('flux-admin.search') }}" method="get" class="hidden min-w-0 max-w-md flex-1 lg:flex">
                <div class="w-full">
                    <flux:input name="q" value="{{ request('q') }}" icon="magnifying-glass" placeholder="Search all records…" variant="outline" size="sm" />
                </div>
            </form>
            <flux:spacer />
            <div class="flex items-center gap-2">
                <a href="{{ route('flux-admin.search') }}" class="lg:hidden">
                    <flux:button size="sm" variant="ghost" icon="magnifying-glass" class="!rounded-none" title="Global search">Search</flux:button>
                </a>
                <a href="{{ route('flux-admin.dashboard') }}">
                    <flux:button size="sm" variant="ghost" icon="home" class="!rounded-none">Dashboard</flux:button>
                </a>
                <a href="/ngn-admin/dashboard">
                    <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Backpack</flux:button>
                </a>
            </div>
        </flux:header>

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

        <flux:main id="flux-admin-main" class="min-h-0 min-w-0 flex-1 overflow-y-auto bg-zinc-100 !p-0 dark:bg-zinc-950 dark:text-zinc-100">
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
