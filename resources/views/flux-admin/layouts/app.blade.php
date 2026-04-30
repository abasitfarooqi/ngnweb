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
        {{-- Flux Pro `navlist.group expandable` uses Tailwind v4 `data-open:*` / `group-data-open/*` variants, which Tailwind v3 (this project) does not compile. Flux JS propagates `data-open` onto <ui-disclosure>, the trigger button, and the panel div on toggle (flux.js L7194-7196); hook directly on those so clicks actually open/close. --}}
        [data-flux-navlist-group][data-open] > div.hidden,
        [data-flux-navlist-group] > div[data-open] { display: block; }
        [data-flux-navlist-group][data-open] > button > div > svg.hidden,
        [data-flux-navlist-group] > button[data-open] > div > svg.hidden { display: block; }
        [data-flux-navlist-group][data-open] > button > div > svg.block,
        [data-flux-navlist-group] > button[data-open] > div > svg.block { display: none; }

        {{-- Sidebar density: more comfortable padding and line-height for navlist items (user reported items look too small). --}}
        body.flux-admin-app [data-flux-sidebar] [data-flux-navlist-item],
        body.flux-admin-app [data-flux-sidebar] [data-flux-navlist-group] > button {
            padding-top: .55rem;
            padding-bottom: .55rem;
            font-size: .875rem;
            line-height: 1.25rem;
            letter-spacing: .01em;
        }
        body.flux-admin-app [data-flux-sidebar] [data-flux-navlist-group] > div [data-flux-navlist-item] {
            padding-top: .45rem;
            padding-bottom: .45rem;
        }
        body.flux-admin-app [data-flux-sidebar] {
            min-width: 16rem;
        }
        @media (min-width: 1024px) {
            body.flux-admin-app [data-flux-sidebar] { min-width: 17rem; }
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
            <flux:navlist.item href="{{ route('flux-admin.dashboard') }}" icon="home" :current="request()->routeIs('flux-admin.dashboard*')">Dashboard</flux:navlist.item>

            @can('see-menu-ecommerce')
                <flux:navlist.item href="{{ route('flux-admin.ec-orders.index') }}" icon="shopping-cart" :current="request()->routeIs('flux-admin.ec-orders.*')">Online store</flux:navlist.item>
            @endcan

            @can('see-menu-finance')
                <flux:navlist.group expandable :expanded="false" heading="Finance">
                    <flux:navlist.item href="{{ route('flux-admin.finance.index') }}" :current="request()->routeIs('flux-admin.finance.*')">Create / Edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.contract-access.index') }}" :current="request()->routeIs('flux-admin.contract-access.*')">Contract signature expire</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.application-items.index') }}" :current="request()->routeIs('flux-admin.application-items.*')">Application items</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.contract-extra-items.index') }}" :current="request()->routeIs('flux-admin.contract-extra-items.*')">Contract extras</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.booking-invoices.index') }}" :current="request()->routeIs('flux-admin.booking-invoices.*')">Booking invoices</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-rentals')
                <flux:navlist.group expandable :expanded="false" heading="Rentals">
                    <flux:navlist.item href="{{ route('flux-admin.rental-operations.index') }}" :current="request()->routeIs('flux-admin.rental-operations.*')">Operations hub</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.rentals.index') }}" :current="request()->routeIs('flux-admin.rentals.*')">Rentals list</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.new-booking.index') }}" :current="request()->routeIs('flux-admin.new-booking.*')">New booking</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.bookings-management.index') }}" :current="request()->routeIs('flux-admin.bookings-management.*')">Bookings management</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.inactive-bookings.index') }}" :current="request()->routeIs('flux-admin.inactive-bookings.*')">Inactive bookings</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.all-bookings.index') }}" :current="request()->routeIs('flux-admin.all-bookings.*')">All bookings</flux:navlist.item>
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
                <flux:navlist.group expandable :expanded="false" heading="PCNs">
                    <flux:navlist.item href="{{ route('flux-admin.pcn.index') }}" :current="request()->routeIs('flux-admin.pcn.index') || request()->routeIs('flux-admin.pcn.show')">Add / Edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn-updates.index') }}" :current="request()->routeIs('flux-admin.pcn-updates.*')">PCN updates</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn-tol-requests.index') }}" :current="request()->routeIs('flux-admin.pcn-tol-requests.*')">TOL requests</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.pcn-dashboard.index') }}" :current="request()->routeIs('flux-admin.pcn-dashboard.*')">Overview</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-services-and-repairs-and-report')
                <flux:navlist.group expandable :expanded="false" heading="Book services / repairs / report">
                    <flux:navlist.item href="{{ route('flux-admin.customer-appointments.index') }}" :current="request()->routeIs('flux-admin.customer-appointments.*')">Services / repairs booking</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-repairs.index') }}" :current="request()->routeIs('flux-admin.motorbike-repairs.*')">Repairs report</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-repair-updates.index') }}" :current="request()->routeIs('flux-admin.motorbike-repair-updates.*')">Repair updates</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-mot-bookings')
                <flux:navlist.group expandable :expanded="false" heading="MOT">
                    <flux:navlist.item href="{{ route('flux-admin.mot-bookings.index') }}" :current="request()->routeIs('flux-admin.mot-bookings.*')">Add / Edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.mot-checker.index') }}" :current="request()->routeIs('flux-admin.mot-checker.*')">MOT checker</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.mot-stats.index') }}" :current="request()->routeIs('flux-admin.mot-stats.*')">MOT stats</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-commons')
                <flux:navlist.group expandable :expanded="false" heading="Customers">
                    <flux:navlist.item href="{{ route('flux-admin.customers.index') }}" :current="request()->routeIs('flux-admin.customers.*')">Customer list</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.customer-documents.index') }}" :current="request()->routeIs('flux-admin.customer-documents.*')">Verify documents</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-b2b')
                <flux:navlist.group expandable :expanded="false" heading="B2B">
                    <flux:navlist.item href="{{ route('flux-admin.inventory-partners.index') }}" :current="request()->routeIs('flux-admin.inventory-partners.*')">Partners</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-inventory')
                <flux:navlist.group expandable :expanded="false" heading="Inventory">
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

                <flux:navlist.group expandable :expanded="false" heading="Spare parts">
                    <flux:navlist.item href="{{ route('flux-admin.sp-parts.index') }}" :current="request()->routeIs('flux-admin.sp-parts.*')">Parts</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-makes.index') }}" :current="request()->routeIs('flux-admin.sp-makes.*')">Makes</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-models.index') }}" :current="request()->routeIs('flux-admin.sp-models.*')">Models</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-fitments.index') }}" :current="request()->routeIs('flux-admin.sp-fitments.*')">Fitments</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-assemblies.index') }}" :current="request()->routeIs('flux-admin.sp-assemblies.*')">Assemblies</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-assembly-parts.index') }}" :current="request()->routeIs('flux-admin.sp-assembly-parts.*')">Assembly parts</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.sp-stock-movements.index') }}" :current="request()->routeIs('flux-admin.sp-stock-movements.*')">Stock movements</flux:navlist.item>
                    <flux:navlist.item href="{{ url('/ngn-admin/sp-stock-handler') }}" badge="Legacy">Stock handler</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-vehicles')
                <flux:navlist.group expandable :expanded="false" heading="Vehicles">
                    <flux:navlist.item href="{{ route('flux-admin.motorbikes-dvla.create') }}" :current="request()->routeIs('flux-admin.motorbikes-dvla.*')">DVLA add / edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbikes.index') }}" :current="request()->routeIs('flux-admin.motorbikes*')">Manual add / edit</flux:navlist.item>
                    <flux:navlist.item href="{{ url('/ngn-admin/motorbike-annual-compliance-m') }}" badge="Legacy">MOT / TAX override</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-compliance.index') }}" :current="request()->routeIs('flux-admin.motorbike-compliance.*')">Vehicle database</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-new.index') }}" :current="request()->routeIs('flux-admin.motorbike-new.*')">New arrivals</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.ebikes.index') }}" :current="request()->routeIs('flux-admin.ebikes.*')">E-bike manager</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.delivery-enquiries.index') }}" :current="request()->routeIs('flux-admin.delivery-enquiries.*')">Delivery enquiries</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.vehicle-notifications.index') }}" :current="request()->routeIs('flux-admin.vehicle-notifications.*')">Vehicle notifications</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.recovered-motorbikes.index') }}" :current="request()->routeIs('flux-admin.recovered-motorbikes.*')">Recovered</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable :expanded="false" heading="Website">
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-sales.index') }}" :current="request()->routeIs('flux-admin.motorbike-sales.*')">Vehicle sale add / edit</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-for-sale.index') }}" :current="request()->routeIs('flux-admin.motorbike-for-sale.*')">For sale catalogue</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-claims')
                <flux:navlist.group expandable :expanded="false" heading="Claims">
                    <flux:navlist.item href="{{ route('flux-admin.motorbike-claims.index') }}" :current="request()->routeIs('flux-admin.motorbike-claims.*')">Add / Edit</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.motorbike-cat-b.index') }}" :current="request()->routeIs('flux-admin.motorbike-cat-b.*')">Category B</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.club-member-vehicles.index') }}" :current="request()->routeIs('flux-admin.club-member-vehicles.*')">Club member vehicles details</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.vehicle-history.index') }}" :current="request()->routeIs('flux-admin.vehicle-history.*')">Vehicle history</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.company-vehicles.index') }}" :current="request()->routeIs('flux-admin.company-vehicles.*')">Company vehicles</flux:navlist.item>

                <flux:navlist.group expandable :expanded="false" heading="Purchase">
                    <flux:navlist.item href="{{ route('flux-admin.used-purchases.index') }}" :current="request()->routeIs('flux-admin.used-purchases.*')">Add / Edit</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.item href="{{ route('flux-admin.contact-queries.index') }}" :current="request()->routeIs('flux-admin.contact-queries.*')">Contact queries</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.service-bookings.index') }}" :current="request()->routeIs('flux-admin.service-bookings.*')">Service enquiries</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.support-inbox.index') }}" :current="request()->routeIs('flux-admin.support-inbox.*')">Conversations inbox</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.support-conversations.index') }}" :current="request()->routeIs('flux-admin.support-conversations.*')">Support conversations</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.support-messages.index') }}" :current="request()->routeIs('flux-admin.support-messages.*')">Support messages</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.vehicle-delivery-orders.index') }}" :current="request()->routeIs('flux-admin.vehicle-delivery-orders.*')">Motorbike delivery orders</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.careers.index') }}" :current="request()->routeIs('flux-admin.careers.*')">Careers</flux:navlist.item>

                <flux:navlist.group expandable :expanded="false" heading="Blog management">
                    <flux:navlist.item href="{{ route('flux-admin.blog-posts.index') }}" :current="request()->routeIs('flux-admin.blog-posts.*')">Blog posts</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.blog-categories.index') }}" :current="request()->routeIs('flux-admin.blog-categories.*')">Blog categories</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.blog-tags.index') }}" :current="request()->routeIs('flux-admin.blog-tags.*')">Blog tags</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable :expanded="false" heading="Motorbike preference survey">
                    <flux:navlist.item href="{{ url('/ngn-admin/ngn_survey_campaign/1') }}" badge="Legacy">Survey WhatsApp notifier</flux:navlist.item>
                    <flux:navlist.item href="{{ url('/ngn-admin/survey_responses/1') }}" badge="Legacy">Survey results</flux:navlist.item>
                    <flux:navlist.item href="{{ url('/ngn-admin/survey_index') }}" badge="Legacy">Survey campaign</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @role('Admin')
                <flux:navlist.group expandable :expanded="false" heading="Misc / Experiments">
                    <flux:navlist.group expandable :expanded="false" heading="Club members">
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
                </flux:navlist.group>
            @endrole

            @can('see-menu-security')
                <flux:navlist.group expandable :expanded="false" heading="Security">
                    <flux:navlist.item href="{{ route('flux-admin.ip-restrictions.index') }}" :current="request()->routeIs('flux-admin.ip-restrictions.*')">IP restrictions</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.access-logs.index') }}" :current="request()->routeIs('flux-admin.access-logs.*')">Access logs</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @can('see-menu-permissions')
                <flux:navlist.group expandable :expanded="false" heading="Permissions">
                    <flux:navlist.item href="{{ route('flux-admin.users.index') }}" :current="request()->routeIs('flux-admin.users.*')">Users</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.roles.index') }}" :current="request()->routeIs('flux-admin.roles.*')">Roles</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.permissions.index') }}" :current="request()->routeIs('flux-admin.permissions.*')">Permissions</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            @canany(['see-judopay-home', 'see-judopay'])
                <flux:navlist.group expandable :expanded="false" heading="Judo Pay">
                    <flux:navlist.item href="{{ route('flux-admin.judopay-recurring.index') }}" :current="request()->routeIs('flux-admin.judopay-recurring.*')">Judo Pay</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-mit-dashboard.index') }}" :current="request()->routeIs('flux-admin.judopay-mit-dashboard.*')">MIT dashboard</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-weekly-queue.index') }}" :current="request()->routeIs('flux-admin.judopay-weekly-queue.*')">Weekly schedule</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-subscriptions.index') }}" :current="request()->routeIs('flux-admin.judopay-subscriptions.*')">Subscriptions</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.ngn-mit-queue.index') }}" :current="request()->routeIs('flux-admin.ngn-mit-queue.*')">NGN MIT queue</flux:navlist.item>
                    <flux:navlist.item href="{{ route('flux-admin.judopay-mit-queue.index') }}" :current="request()->routeIs('flux-admin.judopay-mit-queue.*')">Judopay MIT queue</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            <flux:navlist.item href="{{ url('/ngn-admin/ebike_manager') }}" badge="Legacy">Ebike manager</flux:navlist.item>

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
