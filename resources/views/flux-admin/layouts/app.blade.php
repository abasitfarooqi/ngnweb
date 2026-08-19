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
        .flux-admin-table-readable [data-flux-table] {
            width: 100%;
            min-width: 100%;
            table-layout: fixed;
        }
        .flux-admin-table-readable [data-flux-table] th,
        .flux-admin-table-readable [data-flux-table] td {
            white-space: normal;
            overflow-wrap: anywhere;
            vertical-align: top;
        }
        .flux-admin-table-readable [data-flux-table] th:last-child,
        .flux-admin-table-readable [data-flux-table] td:last-child {
            position: static;
            right: auto;
            box-shadow: none;
            background-color: inherit;
        }
        .dark .flux-admin-table-readable [data-flux-table] th:last-child,
        .dark .flux-admin-table-readable [data-flux-table] td:last-child {
            box-shadow: none;
            background-color: inherit;
        }
        .flux-admin-table-readable [data-flux-table] > :is(thead, tbody) > tr > * {
            display: table-cell !important;
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
        {{-- Unlayered paint so form choice cards survive global div{background:transparent} in light/dark. --}}
        body.flux-admin-app .flux-admin-choice {
            background-color: rgb(255 255 255);
            color: rgb(39 39 42);
        }
        body.flux-admin-app .flux-admin-choice-active {
            background-color: rgb(239 246 255);
            color: rgb(39 39 42);
        }
        html.dark body.flux-admin-app .flux-admin-choice {
            background-color: rgb(24 24 27);
            color: rgb(244 244 245);
        }
        html.dark body.flux-admin-app .flux-admin-choice-active {
            background-color: rgb(23 37 84);
            color: rgb(239 246 255);
        }
        body.flux-admin-app .flux-admin-panel {
            background-color: rgb(255 255 255);
        }
        html.dark body.flux-admin-app .flux-admin-panel {
            background-color: rgb(24 24 27);
        }
        body.flux-admin-app .flux-admin-autocomplete {
            position: relative;
            z-index: 40;
            overflow: visible;
        }
        body.flux-admin-app .flux-admin-autocomplete-open {
            z-index: 10040;
        }
        {{-- Absolute fallback under the field. Popover top-layer + fixed coords are applied in JS. --}}
        body.flux-admin-app .flux-admin-autocomplete-menu {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 2px);
            z-index: 10050;
            max-height: 16rem;
            overflow-x: hidden;
            overflow-y: auto;
            border: 1px solid rgb(228 228 231);
            background-color: rgb(255 255 255);
            color: rgb(24 24 27);
            box-shadow: 0 12px 28px rgb(24 24 27 / 0.22);
            padding: 0.25rem 0;
            margin: 0;
            box-sizing: border-box;
        }
        {{-- UA popover uses inset:0 + margin:auto (misplaces to centre/side). Override hard. --}}
        body.flux-admin-app .flux-admin-autocomplete-menu:popover-open,
        body.flux-admin-app .flux-admin-autocomplete-menu[popover]:popover-open {
            position: fixed !important;
            inset: auto !important;
            margin: 0 !important;
            right: auto !important;
            bottom: auto !important;
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 10070 !important;
        }
        html.dark body.flux-admin-app .flux-admin-autocomplete-menu {
            border-color: rgb(63 63 70);
            background-color: rgb(24 24 27);
            color: rgb(244 244 245);
            box-shadow: 0 12px 28px rgb(0 0 0 / 0.5);
        }
        body.flux-admin-app .flux-admin-autocomplete-menu li {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0.55rem 0.75rem;
            line-height: 1.35;
            font-size: 0.875rem;
            cursor: pointer;
            white-space: normal;
            overflow: visible;
        }
        body.flux-admin-app .flux-admin-autocomplete-menu li:hover,
        body.flux-admin-app .flux-admin-autocomplete-menu li:focus {
            background-color: rgb(244 244 245);
        }
        html.dark body.flux-admin-app .flux-admin-autocomplete-menu li:hover,
        html.dark body.flux-admin-app .flux-admin-autocomplete-menu li:focus {
            background-color: rgb(39 39 42);
        }
        {{-- Let suggestion menus escape grids / field wrappers instead of being clipped. --}}
        body.flux-admin-app .flux-admin-form-grid,
        body.flux-admin-app .flux-admin-field,
        body.flux-admin-app .flux-admin-field-control,
        body.flux-admin-app .flux-admin-panel,
        body.flux-admin-app .flux-admin-content,
        body.flux-admin-app form.space-y-5,
        body.flux-admin-app form.space-y-6,
        body.flux-admin-app form > div.border,
        body.flux-admin-app form .grid {
            overflow: visible;
        }
        body.flux-admin-app select.comms-grant-user-list {
            height: 7.25rem;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            overscroll-behavior: contain;
        }
        body.flux-admin-app .flux-admin-autocomplete [data-flux],
        body.flux-admin-app .flux-admin-autocomplete [data-flux-control],
        body.flux-admin-app .flux-admin-autocomplete > * {
            overflow: visible !important;
        }
        body.flux-admin-app .flux-admin-flux-editor,
        body.flux-admin-app .flux-admin-flux-editor ui-editor,
        body.flux-admin-app .flux-admin-flux-editor ui-toolbar,
        body.flux-admin-app .flux-admin-flux-editor [data-slot=content] {
            border-radius: 0 !important;
        }
        body.flux-admin-app .flux-admin-flux-editor ui-editor-content {
            min-height: 8rem;
        }
        {{-- Flux UI select / listbox / menu popovers — top of stacking within the document top layer. --}}
        body.flux-admin-app [data-flux-select-options],
        body.flux-admin-app [data-flux-options],
        body.flux-admin-app [data-flux-autocomplete-items],
        body.flux-admin-app [data-flux-menu],
        body.flux-admin-app [data-flux-navmenu],
        body.flux-admin-app ui-options,
        body.flux-admin-app [popover],
        body.flux-admin-app :popover-open {
            z-index: 10060 !important;
        }
        {{-- Flux Pro `navlist.group expandable` uses Tailwind v4 `data-open:*` / `group-data-open/*` variants, which Tailwind v3 (this project) does not compile. Flux JS propagates `data-open` onto <ui-disclosure>, the trigger button, and the panel div on toggle (flux.js L7194-7196); hook directly on those so clicks actually open/close. --}}
        [data-flux-navlist-group][data-open] > div.hidden,
        [data-flux-navlist-group] > div[data-open] { display: block; }
        [data-flux-navlist-group][data-open] > button > div > svg.hidden,
        [data-flux-navlist-group] > button[data-open] > div > svg.hidden { display: block; }
        [data-flux-navlist-group][data-open] > button > div > svg.block,
        [data-flux-navlist-group] > button[data-open] > div > svg.block { display: none; }

        {{-- Flux tabs — global site button styles + Tailwind v4 data-selected:* variants break tab affordance. --}}
        body.flux-admin-app [data-flux-tabs] {
            border-bottom: 1px solid rgb(228 228 231);
        }
        html.dark body.flux-admin-app [data-flux-tabs] {
            border-bottom-color: rgb(63 63 70);
        }
        body.flux-admin-app [data-flux-tab] {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: -1px;
            padding: 0 0.5rem !important;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            border-radius: 0 !important;
            background-color: transparent !important;
            color: rgb(161 161 170) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            line-height: 2.5rem !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            cursor: pointer;
            box-shadow: none !important;
        }
        body.flux-admin-app [data-flux-tab]:hover {
            color: rgb(39 39 42) !important;
            background-color: transparent !important;
        }
        html.dark body.flux-admin-app [data-flux-tab] {
            color: rgb(161 161 170) !important;
        }
        html.dark body.flux-admin-app [data-flux-tab]:hover {
            color: rgb(255 255 255) !important;
        }
        body.flux-admin-app [data-flux-tab][data-selected] {
            color: rgb(24 24 27) !important;
            border-bottom-color: rgb(24 24 27) !important;
            font-weight: 600 !important;
            background-color: transparent !important;
        }
        html.dark body.flux-admin-app [data-flux-tab][data-selected] {
            color: rgb(255 255 255) !important;
            border-bottom-color: rgb(255 255 255) !important;
        }
        body.flux-admin-app [data-flux-tabs].inline-flex [data-flux-tab] {
            margin-bottom: 0;
            border-bottom: none !important;
            border-radius: 0.375rem !important;
            padding: 0 1rem !important;
            line-height: 2rem !important;
        }
        body.flux-admin-app [data-flux-tabs].inline-flex [data-flux-tab][data-selected] {
            background-color: rgb(24 24 27) !important;
            color: rgb(255 255 255) !important;
        }
        html.dark body.flux-admin-app [data-flux-tabs].inline-flex [data-flux-tab][data-selected] {
            background-color: rgb(255 255 255) !important;
            color: rgb(24 24 27) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button {
            border: 1px solid rgb(228 228 231) !important;
            border-radius: 0 !important;
            background-color: rgb(255 255 255) !important;
            color: rgb(63 63 70) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            line-height: 1.25rem !important;
            padding: 0.375rem 0.75rem !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            box-shadow: none !important;
            cursor: pointer;
        }
        html.dark body.flux-admin-app .flux-admin-segment-tabs button {
            border-color: rgb(63 63 70) !important;
            background-color: rgb(24 24 27) !important;
            color: rgb(212 212 216) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button:hover {
            background-color: rgb(250 250 250) !important;
        }
        html.dark body.flux-admin-app .flux-admin-segment-tabs button:hover {
            background-color: rgb(39 39 42) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-active="true"] {
            color: rgb(255 255 255) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-segment="all"][data-active="true"] {
            background-color: rgb(24 24 27) !important;
            border-color: rgb(24 24 27) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-segment="rental"][data-active="true"] {
            background-color: rgb(4 120 87) !important;
            border-color: rgb(4 120 87) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-segment="finance_new"][data-active="true"] {
            background-color: rgb(29 78 216) !important;
            border-color: rgb(29 78 216) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-segment="finance_used"][data-active="true"] {
            background-color: rgb(109 40 217) !important;
            border-color: rgb(109 40 217) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-segment="company"][data-active="true"] {
            background-color: rgb(180 83 9) !important;
            border-color: rgb(180 83 9) !important;
        }
        body.flux-admin-app .flux-admin-segment-tabs button[data-segment="for_sale"][data-active="true"] {
            background-color: rgb(3 105 161) !important;
            border-color: rgb(3 105 161) !important;
        }
        body.flux-admin-app .club-members-tab-body > div > div.border {
            border-width: 0 !important;
            background: transparent !important;
        }

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
            body.flux-admin-app ui-sidebar[data-flux-sidebar],
            body.flux-admin-app [data-flux-sidebar] {
                min-width: 19rem;
                width: 19rem;
                max-width: 19rem;
                transform: none !important;
                visibility: visible !important;
                pointer-events: auto !important;
                position: sticky !important;
            }
        }
        @media (max-width: 1023px) {
            body.flux-admin-app ui-sidebar[data-flux-sidebar][data-flux-sidebar-on-mobile],
            body.flux-admin-app [data-flux-sidebar][data-flux-sidebar-on-mobile] {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                min-width: 0 !important;
                width: 100vw !important;
                max-width: 100vw !important;
                height: 100dvh !important;
                max-height: 100dvh !important;
                z-index: 50 !important;
                transition: transform 0.25s ease, visibility 0.25s ease;
            }
            body.flux-admin-app ui-sidebar[data-flux-sidebar][data-flux-sidebar-on-mobile][data-flux-sidebar-collapsed-mobile],
            body.flux-admin-app [data-flux-sidebar][data-flux-sidebar-on-mobile][data-flux-sidebar-collapsed-mobile] {
                transform: translateX(-100%) !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
            html[dir="rtl"] body.flux-admin-app ui-sidebar[data-flux-sidebar][data-flux-sidebar-on-mobile][data-flux-sidebar-collapsed-mobile],
            html[dir="rtl"] body.flux-admin-app [data-flux-sidebar][data-flux-sidebar-on-mobile][data-flux-sidebar-collapsed-mobile] {
                transform: translateX(100%) !important;
            }
            body.flux-admin-app ui-sidebar[data-flux-sidebar][data-flux-sidebar-on-mobile]:not([data-flux-sidebar-collapsed-mobile]),
            body.flux-admin-app [data-flux-sidebar][data-flux-sidebar-on-mobile]:not([data-flux-sidebar-collapsed-mobile]) {
                transform: translateX(0) !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }
            body.flux-admin-app [data-flux-sidebar-backdrop] {
                display: none !important;
                position: fixed;
                inset: 0;
                z-index: 40;
                background: rgb(0 0 0 / 0.35);
            }
            body.flux-admin-app [data-flux-sidebar-backdrop][data-flux-sidebar-on-mobile]:not([data-flux-sidebar-collapsed-mobile]) {
                display: block !important;
            }
            body.flux-admin-app .flux-admin-main-column {
                width: 100%;
                min-width: 0;
                flex: 1 1 100%;
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
        body.flux-admin-app .flux-admin-sidebar .flux-admin-nav-mode {
            flex: 1 1 0% !important;
            min-height: 0 !important;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
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
        @media (max-width: 1023px) {
            body.flux-admin-app ui-sidebar[data-flux-sidebar] {
                gap: 0.5rem !important;
                padding: 0.5rem !important;
            }
            body.flux-admin-app .flux-admin-sidebar-top {
                display: flex;
                align-items: center;
                gap: 0.35rem;
                margin: 0 0 0.2rem;
            }
            body.flux-admin-app .flux-admin-sidebar-top .flux-admin-brand {
                margin: 0;
                flex: 1;
                min-width: 0;
                border: 0;
                box-shadow: none;
                padding: 0;
                background: transparent;
            }
            body.flux-admin-app .flux-admin-brand-mark {
                height: 1.375rem;
            }
            body.flux-admin-app .flux-admin-brand-mark img {
                height: 1.375rem;
            }
            body.flux-admin-app .flux-admin-quick-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                margin: 0 0.35rem 0.25rem;
                gap: 0.2rem;
            }
            body.flux-admin-app .flux-admin-quick-link {
                min-height: 1.85rem;
                justify-content: center;
                padding: 0.25rem;
                gap: 0;
            }
            body.flux-admin-app .flux-admin-quick-label {
                display: none;
            }
            body.flux-admin-app .flux-admin-quick-icon {
                height: auto;
                width: auto;
                border: 0;
                background: transparent;
                padding: 0;
            }
        }
        .flux-admin-top-header {
            gap: 0.5rem;
        }
        .flux-admin-header-search-form {
            min-width: 0;
        }
        @media (max-width: 1023px) {
            .flux-admin-header-search-row {
                flex: 1 1 auto;
                min-width: 0;
                width: 100%;
            }
            .flux-admin-header-search-form {
                flex: 1 1 auto;
                min-width: 0;
                width: 100%;
            }
            .flux-admin-header-search-field {
                flex: 1 1 0%;
                min-width: 0;
                width: 100%;
            }
            .flux-admin-header-search-field [data-flux-input],
            .flux-admin-header-search-field ui-input {
                width: 100%;
            }
            .flux-admin-header-search-field input {
                width: 100%;
                min-width: 0;
            }
        }
        .flux-admin-brand {
            margin: .5rem .65rem .35rem;
            border: 1px solid rgb(228 228 231);
            background: rgb(255 255 255);
            padding: .45rem .55rem;
            box-shadow: 0 1px 2px rgb(24 24 27 / .05);
        }
        html.dark .flux-admin-brand {
            border-color: rgb(39 39 42);
            background: rgb(9 9 11 / .72);
            box-shadow: 0 1px 2px rgb(0 0 0 / .28);
        }
        .flux-admin-brand-mark {
            display: flex;
            height: 2.25rem;
            width: auto;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 0;
            padding: 0;
        }
        html.dark .flux-admin-brand-mark {
            border: 0;
            background: transparent;
        }
        .flux-admin-quick-grid {
            margin: 0 .65rem .5rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .35rem;
        }
        .flux-admin-quick-link {
            display: flex;
            min-height: 2.35rem;
            align-items: center;
            gap: .45rem;
            border: 1px solid rgb(228 228 231);
            background: rgb(255 255 255);
            padding: .4rem .55rem;
            color: rgb(63 63 70);
            font-size: .75rem;
            font-weight: 650;
            line-height: 1rem;
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
        .flux-admin-unread-badge {
            display: inline-flex;
            min-width: 1.15rem;
            height: 1.15rem;
            align-items: center;
            justify-content: center;
            padding: 0 0.28rem;
            margin-left: 0.4rem;
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            border-radius: 0;
        }
        .flux-admin-unread-badge.hidden,
        .flux-admin-unread-badge[hidden] {
            display: none !important;
        }
        .flux-admin-quick-icon {
            position: relative;
        }
        .flux-admin-quick-icon .flux-admin-unread-badge {
            position: absolute;
            top: -0.35rem;
            right: -0.35rem;
            margin-left: 0;
        }
        .flux-admin-header-unread {
            position: relative;
            display: inline-flex;
        }
        .flux-admin-header-unread .flux-admin-unread-badge {
            position: absolute;
            top: -0.35rem;
            right: -0.35rem;
            margin-left: 0;
            z-index: 1;
        }
        .flux-admin-menu {
            padding: .1rem .35rem .25rem;
        }
        body.flux-admin-app #flux-admin-grouped-navlist {
            padding-left: 0;
            padding-right: 0;
        }
        body.flux-admin-app #flux-admin-grouped-navlist [data-flux-navlist-group] > div {
            margin-left: 0;
            padding-left: 0;
            border-left: 0;
        }
        body.flux-admin-app #flux-admin-grouped-navlist [data-flux-navlist-group] > button {
            padding-left: 0 !important;
        }
        body.flux-admin-app .flux-admin-sidebar ui-switch {
            position: relative;
            display: inline-flex;
            height: 1.5rem;
            width: 2.75rem;
            flex: 0 0 auto;
            cursor: pointer;
            align-items: center;
            border-radius: 999px;
            background: rgb(212 212 216);
            transition: background-color .15s ease, box-shadow .15s ease;
        }
        body.flux-admin-app .flux-admin-sidebar ui-switch > * {
            display: none !important;
        }
        body.flux-admin-app .flux-admin-sidebar ui-switch::after {
            content: "";
            position: absolute;
            left: .1875rem;
            height: 1.125rem;
            width: 1.125rem;
            border-radius: 999px;
            background: white;
            box-shadow: 0 1px 3px rgb(0 0 0 / .22);
            transition: transform .15s ease;
        }
        body.flux-admin-app .flux-admin-sidebar ui-switch[data-checked] {
            background: rgb(20 184 166);
            box-shadow: 0 0 0 2px rgb(20 184 166 / .16);
        }
        body.flux-admin-app .flux-admin-sidebar ui-switch[data-checked]::after {
            transform: translateX(1.25rem);
        }
        html.dark body.flux-admin-app .flux-admin-sidebar ui-switch {
            background: rgb(63 63 70);
        }
        html.dark body.flux-admin-app .flux-admin-sidebar ui-switch[data-checked] {
            background: rgb(20 184 166);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-item],
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button {
            min-height: 1.7rem;
            border-radius: 0;
            padding: .22rem .45rem;
            color: rgb(82 82 91);
            font-size: .8125rem;
            font-weight: 560;
            line-height: 1.1rem;
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
            margin: 0;
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
            box-shadow: none;
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group][data-open] > button,
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > button[data-open] {
            border-color: rgb(63 63 70);
            background: rgb(24 24 27);
            box-shadow: none;
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > div {
            margin: 0 0 .1rem .25rem;
            border-left: 1px solid rgb(228 228 231);
            padding-left: .25rem;
        }
        html.dark body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > div {
            border-left-color: rgb(63 63 70);
        }
        body.flux-admin-app .flux-admin-menu [data-flux-navlist-group] > div [data-flux-navlist-item] {
            min-height: 1.65rem;
            padding-top: .28rem;
            padding-bottom: .28rem;
            font-size: .78rem;
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

        /* Flux modals: centre in the viewport across the admin panel. Flyouts keep edge positioning. */
        body.flux-admin-app [data-flux-modal] > dialog:not([data-flux-flyout]),
        body.flux-admin-app ui-modal > dialog:not([data-flux-flyout]) {
            position: fixed;
            inset: 0;
            width: fit-content;
            height: fit-content;
            max-width: min(calc(100vw - 2rem), 42rem);
            max-height: calc(100dvh - 2rem);
            margin: auto;
            overflow-y: auto;
            border: 0;
        }

        body.flux-admin-app [data-flux-modal] > dialog:not([data-flux-flyout])::backdrop,
        body.flux-admin-app ui-modal > dialog:not([data-flux-flyout])::backdrop {
            background-color: rgb(0 0 0 / 0.45);
        }

        body.flux-admin-app [data-flux-modal] > dialog:not([open]):not([data-flux-flyout]),
        body.flux-admin-app ui-modal > dialog:not([open]):not([data-flux-flyout]) {
            display: none;
        }
    </style>
</head>
<body class="flux-admin-app min-h-dvh bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans antialiased lg:flex lg:min-h-screen lg:flex-row" data-staff-communications="1" data-staff-unread-url="{{ route('flux-admin.unread-badges') }}">
    @php
        $staffUnread = \App\Support\FluxAdminUnreadBadges::counts();
        $commsOnlyStaff = \App\Support\FluxAdminAccess::isCommunicationsOnlyStaff();
    @endphp
    <a href="#flux-admin-main" class="flux-admin-skip">Skip to content</a>

    {{-- Sidebar: same dark surface as main canvas (no half-light / half-dark split). --}}
    <flux:sidebar sticky stashable x-data="fluxAdminNavigationPreference()" class="flux-admin-sidebar flex flex-col z-20 border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950 lg:z-auto lg:min-h-screen lg:shrink-0">
        <div class="flux-admin-sidebar-top lg:contents">
            <flux:sidebar.toggle class="shrink-0 lg:hidden" icon="x-mark" aria-label="Close menu" />

            {{-- Brand --}}
            <a href="{{ route('flux-admin.dashboard') }}" class="flux-admin-brand flex items-center" aria-label="NGN Motors admin">
                <span class="flux-admin-brand-mark">
                    <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-8 w-auto">
                </span>
            </a>
        </div>

        @if(! $commsOnlyStaff)
        <div class="flux-admin-quick-grid">
            @canany(['see-menu-vehicles', 'see-menu-commons'])
            <a href="{{ route('flux-admin.delivery-enquiries.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="truck" class="h-4 w-4" /></span>
                <span class="flux-admin-quick-label">Delivery enquiries</span>
            </a>
            @endcanany
            @can('see-menu-mot-bookings')
            <a href="{{ route('flux-admin.mot-bookings.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="clipboard-document-check" class="h-4 w-4" /></span>
                <span class="flux-admin-quick-label">MOT</span>
            </a>
            @endcan
            @can('see-menu-commons')
            <a href="{{ route('flux-admin.service-bookings.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon"><flux:icon name="wrench-screwdriver" class="h-4 w-4" /></span>
                <span class="flux-admin-quick-label">Services</span>
            </a>
            <a href="{{ route('flux-admin.support-inbox.index') }}" class="flux-admin-quick-link">
                <span class="flux-admin-quick-icon">
                    <flux:icon name="inbox" class="h-4 w-4" />
                    <span class="js-staff-inbox-unread flux-admin-unread-badge{{ ($staffUnread['inbox'] ?? 0) > 0 ? '' : ' hidden' }}" data-count="{{ (int) ($staffUnread['inbox'] ?? 0) }}">{{ ($staffUnread['inbox'] ?? 0) > 99 ? '99+' : (int) ($staffUnread['inbox'] ?? 0) }}</span>
                </span>
                <span class="flux-admin-quick-label">Inbox</span>
            </a>
            @endcan
        </div>
        @endif

        <flux:separator />

        <div x-show="navMode === 'home'" x-cloak class="flux-admin-nav-mode flex min-h-0 flex-1 flex-col overflow-y-auto">
        @if($commsOnlyStaff)
        <flux:navlist id="flux-admin-navlist-comms" class="flux-admin-menu min-h-0 overflow-y-auto" wire:navigate:scroll>
                <flux:navlist.item href="{{ route('flux-admin.communications.sent.index') }}" icon="bell" :current="request()->routeIs('flux-admin.communications.sent.*')">Notifications <span class="js-staff-notifications-unread flux-admin-unread-badge{{ ($staffUnread['notifications'] ?? 0) > 0 ? '' : ' hidden' }}" data-count="{{ (int) ($staffUnread['notifications'] ?? 0) }}">{{ ($staffUnread['notifications'] ?? 0) > 99 ? '99+' : (int) ($staffUnread['notifications'] ?? 0) }}</span></flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.communications.index') }}" icon="bell-alert" :current="request()->routeIs('flux-admin.communications.index') || request()->routeIs('flux-admin.communications.show') || request()->routeIs('flux-admin.communications.email-preview')">Communications</flux:navlist.item>
        </flux:navlist>
        @else
        <flux:navlist id="flux-admin-navlist" class="flux-admin-menu min-h-0 overflow-y-auto" wire:navigate:scroll>
            <flux:navlist.item href="{{ route('flux-admin.dashboard') }}" icon="home" :current="request()->routeIs('flux-admin.dashboard*')">Dashboard</flux:navlist.item>
         
            {{-- 1. Payment Plan --}}
            @can('see-menu-finance')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'finance') }}" icon="banknotes" :current="(request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'finance') || request()->routeIs('flux-admin.finance.*','flux-admin.contract-access.*')">Payment Plan</flux:navlist.item>
            @endcan

            {{-- 2. Rentals --}}
            @can('see-menu-rentals')
                <flux:navlist.item href="{{ route('flux-admin.rental-operations.index') }}" icon="key" :current="request()->routeIs('flux-admin.rental-operations.*','flux-admin.rentals.*','flux-admin.rental-*','flux-admin.new-booking.*','flux-admin.bookings-management.*','flux-admin.inactive-bookings.*','flux-admin.ended-with-pendings.*','flux-admin.motorbike-pricing.*','flux-admin.all-bookings.*','flux-admin.renting-pricing.*','flux-admin.upload-document-links.*','flux-admin.agreement-access.*','flux-admin.active-rentals.*','flux-admin.rental-due-payments.*','flux-admin.service-videos.*','flux-admin.ebikes.*') || (request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'rentals')">Rentals</flux:navlist.item>
            @endcan

            {{-- 3. PCNs --}}
            @can('see-menu-pcns')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'pcn') }}" icon="shield-exclamation" :current="(request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'pcn') || request()->routeIs('flux-admin.pcn.*','flux-admin.pcn-*','flux-admin.pcn-dashboard.*')">PCN cases</flux:navlist.item>
            @endcan

            {{-- 4. Vehicles (+ vehicle commons items that belong here) --}}
            @can('see-menu-vehicles')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'vehicles') }}" icon="truck" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'vehicles' || request()->routeIs('flux-admin.motorbikes*','flux-admin.motorbike-compliance.*','flux-admin.motorbike-new.*','flux-admin.ebikes.*','flux-admin.vehicle-notifications.*','flux-admin.recovered-motorbikes.*','flux-admin.backpack.motorbike-available.*')">Vehicles</flux:navlist.item>
            @endcan
            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'vehicle-records') }}" icon="clipboard-document-list" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'vehicle-records' || request()->routeIs('flux-admin.motorbike-cat-b.*','flux-admin.vehicle-history.*','flux-admin.company-vehicles.*','flux-admin.club-member-vehicles.*')">Vehicle records</flux:navlist.item>
            @endcan

            {{-- 5. Customers (was Chat position) --}}
            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'customers') }}" icon="users" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'customers' || request()->routeIs('flux-admin.customers.*','flux-admin.customer-documents.*')">Customers</flux:navlist.item>
            @endcan

            {{-- 6. Service enquiries + services bookings --}}
            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.service-bookings.index') }}" icon="inbox" :current="request()->routeIs('flux-admin.service-bookings.*')">Service enquiries</flux:navlist.item>
            @endcan
            @can('see-menu-services-and-repairs-and-report')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'services') }}" icon="wrench-screwdriver" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'services' || request()->routeIs('flux-admin.customer-appointments.*','flux-admin.motorbike-repairs.*','flux-admin.motorbike-repair-updates.*')">Services and repairs</flux:navlist.item>
            @endcan
            {{-- 7. Club: Super Admin hub, or Club Member Access staff lookup --}}
            @if(\App\Support\FluxAdminAccess::isSuperAdmin())
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'club') }}" icon="users" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'club' || request()->routeIs('flux-admin.club.*','flux-admin.club-members.*','flux-admin.club-purchases.*','flux-admin.club-redemptions.*','flux-admin.club-spending.*','flux-admin.club-spending-payments.*','flux-admin.dev-club-otp.*')">Club</flux:navlist.item>
            @elseif(\App\Support\ClubMemberStaffAccess::canAccessPortal())
                <flux:navlist.item href="{{ route('flux-admin.club-members.index') }}" icon="users" :current="request()->routeIs('flux-admin.club-members.*') || (request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'club')">Club</flux:navlist.item>
            @endif

            {{-- 8. Delivery order --}}
            @canany(['see-menu-vehicles', 'see-menu-commons'])
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'delivery') }}" icon="truck" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'delivery' || request()->routeIs('flux-admin.delivery-enquiries.*','flux-admin.vehicle-delivery-orders.*')">Delivery</flux:navlist.item>
            @endcanany

            {{-- 9. Sale (used / brand new) --}}
            @can('see-menu-vehicles')
                <flux:navlist.item href="{{ route('flux-admin.sale.index') }}" icon="tag" :current="request()->routeIs('flux-admin.sale.*','flux-admin.motorbike-sales.*','flux-admin.motorbike-for-sale.*')">Sale</flux:navlist.item>
            @endcan

            {{-- 10. Purchase --}}
            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.used-purchases.index') }}" icon="shopping-cart" :current="request()->routeIs('flux-admin.used-purchases.*')">Purchase</flux:navlist.item>
            @endcan

            {{-- 11. MOT --}}
            @can('see-menu-mot-bookings')
                <flux:navlist.item href="{{ route('flux-admin.mot.index') }}" icon="clipboard-document-check" :current="request()->routeIs('flux-admin.mot.*','flux-admin.mot-bookings.*','flux-admin.backpack.mot-booking.*','flux-admin.mot-checker.*','flux-admin.mot-stats.*')">MOT</flux:navlist.item>
            @endcan

            {{-- 12. Claims --}}
            @can('see-menu-claims')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'claims') }}" icon="exclamation-triangle" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'claims' || request()->routeIs('flux-admin.motorbike-claims.*')">Claims</flux:navlist.item>
            @endcan

            {{-- 13. Ecommerce --}}
            @can('see-menu-ecommerce')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'ecommerce') }}" icon="shopping-bag" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'ecommerce' || request()->routeIs('flux-admin.ec-orders.*','flux-admin.shop-orders.*','flux-admin.spare-part-orders.*','flux-admin.store-front.*')">Ecommerce</flux:navlist.item>
            @endcan

            {{-- 14. Spare parts (+ inventory products under inventory permission) --}}
            @can('see-menu-inventory')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'spare-parts') }}" icon="wrench-screwdriver" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'spare-parts' || request()->routeIs('flux-admin.sp-*')">Spare parts</flux:navlist.item>

                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'inventory') }}" icon="cube" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'inventory' || request()->routeIs('flux-admin.inventory-*','flux-admin.oxford-products.*','flux-admin.purchase-request*','flux-admin.store-front.*')">Inventory</flux:navlist.item>
            @endcan

            {{-- 15. Orders --}}
            @role('Admin')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'orders') }}" icon="truck" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'orders' || request()->routeIs('flux-admin.ds-orders.*','flux-admin.ds-order-items.*','flux-admin.digital-invoices.*','flux-admin.digital-invoice-items.*')">Orders</flux:navlist.item>
            @endrole

            {{-- 16. Blog --}}
            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'blog') }}" icon="document-text" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'blog' || request()->routeIs('flux-admin.blog-*')">Blog management</flux:navlist.item>
            @endcan

            {{-- Chat after Customers, before Careers --}}
            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'chat') }}" icon="chat-bubble-left-right" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'chat' || request()->routeIs('flux-admin.support-*','flux-admin.contact-queries.*')">Chat</flux:navlist.item>
                <flux:navlist.item href="{{ route('flux-admin.careers.index') }}" icon="briefcase" :current="request()->routeIs('flux-admin.careers.*')">Careers</flux:navlist.item>
            @endcan

            @can('see-menu-b2b')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'b2b') }}" icon="building-office" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'b2b' || request()->routeIs('flux-admin.inventory-partners.*')">B2B</flux:navlist.item>
            @endcan

            @can('see-menu-commons')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'surveys') }}" icon="clipboard-document-list" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'surveys' || request()->routeIs('flux-admin.survey*')">Surveys</flux:navlist.item>
            @endcan

            @if(\App\Support\FluxAdminAccess::canViewCommunicationsLog())
                <flux:navlist.item href="{{ route('flux-admin.communications.sent.index') }}" icon="bell" :current="request()->routeIs('flux-admin.communications.sent.*')">Notifications <span class="js-staff-notifications-unread flux-admin-unread-badge{{ ($staffUnread['notifications'] ?? 0) > 0 ? '' : ' hidden' }}" data-count="{{ (int) ($staffUnread['notifications'] ?? 0) }}">{{ ($staffUnread['notifications'] ?? 0) > 99 ? '99+' : (int) ($staffUnread['notifications'] ?? 0) }}</span></flux:navlist.item>
            @endif
            @if(\App\Support\FluxAdminAccess::canAccessCommunications())
                <flux:navlist.item href="{{ route('flux-admin.communications.index') }}" icon="bell-alert" :current="request()->routeIs('flux-admin.communications.index') || request()->routeIs('flux-admin.communications.show') || request()->routeIs('flux-admin.communications.email-preview')">Communications</flux:navlist.item>
            @endif

            @role('Admin')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'misc') }}" icon="cog-6-tooth" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'misc' || request()->routeIs('flux-admin.calendar.*','flux-admin.employee-schedules.*','flux-admin.agent-settings.*','flux-admin.branches.*','flux-admin.vehicle-issuances.*','flux-admin.queue-monitor.*')">Misc / Experiments</flux:navlist.item>
            @endrole

            {{-- 17. Security --}}
            @can('see-menu-security')
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'security') }}" icon="lock-closed" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'security' || request()->routeIs('flux-admin.ip-restrictions.*','flux-admin.access-logs.*')">Security</flux:navlist.item>
            @endcan

            @if(\App\Support\FluxAdminAccess::isSuperAdmin())
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'permissions') }}" icon="key" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'permissions' || request()->routeIs('flux-admin.users.*','flux-admin.user','flux-admin.user.*','flux-admin.backpack.user.*','flux-admin.roles.*','flux-admin.role','flux-admin.role.*','flux-admin.backpack.role.*','flux-admin.permissions.*','flux-admin.permission','flux-admin.permission.*','flux-admin.backpack.permission.*')">Permissions</flux:navlist.item>
            @endif

            {{-- 18. Judopay --}}
            @canany(['see-judopay-home', 'see-judopay'])
                <flux:navlist.item href="{{ route('flux-admin.modules.show', 'judopay') }}" icon="banknotes" :current="request()->routeIs('flux-admin.modules.show') && request()->route('module') === 'judopay' || request()->routeIs('flux-admin.judopay.*','flux-admin.judopay-*','flux-admin.ngn-mit-queue.*')">Judo Pay</flux:navlist.item>
            @endcanany
        </flux:navlist>
        @endif
        </div>

        @if(! $commsOnlyStaff)
        <div x-show="navMode === 'menu'" x-cloak class="flux-admin-nav-mode flex min-h-0 flex-1 flex-col overflow-y-auto">
            @php
                $menuIcon = static function (string $label): string {
                    $label = strtolower($label);
                    return match (true) {
                        str_contains($label, 'dashboard') => 'home',
                        str_contains($label, 'vehicle') || str_contains($label, 'motorbike') => 'truck',
                        str_contains($label, 'rental') || str_contains($label, 'booking') => 'key',
                        str_contains($label, 'customer') || str_contains($label, 'club') => 'users',
                        str_contains($label, 'invoice') || str_contains($label, 'payment') => 'banknotes',
                        str_contains($label, 'calendar') || str_contains($label, 'schedule') => 'calendar-days',
                        str_contains($label, 'repair') || str_contains($label, 'service') => 'wrench-screwdriver',
                        str_contains($label, 'document') || str_contains($label, 'blog') => 'document-text',
                        str_contains($label, 'security') || str_contains($label, 'permission') || str_contains($label, 'role') => 'shield-check',
                        str_contains($label, 'sale') || str_contains($label, 'purchase') || str_contains($label, 'order') => 'shopping-cart',
                        str_contains($label, 'mot') => 'clipboard-document-check',
                        default => 'squares-2x2',
                    };
                };
                $groupedMenu = collect(\App\Support\FluxAdminMenuRegistry::items())
                    ->reject(fn ($item) => $item['group'] === 'Quick links')
                    ->groupBy('group');
            @endphp
            <flux:navlist id="flux-admin-grouped-navlist" class="flux-admin-menu min-h-0 overflow-y-auto" wire:navigate:scroll>
                @foreach($groupedMenu as $group => $items)
                    @php($groupExpanded = request()->routeIs('flux-admin.dashboard*') || $items->contains(function ($item) { return str_contains((string) $item['url'], request()->path()); }))
                    <flux:navlist.group expandable :expanded="$groupExpanded" heading="{{ $group }}">
                        @foreach($items as $item)
                            <flux:navlist.item href="{{ $item['url'] }}" icon="{{ $menuIcon($item['label']) }}">{{ $item['label'] }}</flux:navlist.item>
                        @endforeach
                    </flux:navlist.group>
                @endforeach
            </flux:navlist>
        </div>
        @endif

        @if(! $commsOnlyStaff)
        <div class="mt-auto shrink-0 px-3 py-2">
            <div class="flex items-center justify-between gap-2 px-1" role="group" aria-label="Sidebar view">
                <div class="flex min-w-0 items-center gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                    <flux:icon name="squares-2x2" class="size-4 shrink-0" />
                    <span>Sidebar view:</span>
                    <strong class="font-semibold text-zinc-900 dark:text-white" x-text="navMode === 'menu' ? 'Menu' : 'Home'">Home</strong>
                </div>
                <flux:switch
                    x-effect="$el.checked = navMode === 'menu'"
                    @click="setNavMode(navMode === 'menu' ? 'home' : 'menu')"
                    aria-label="Switch between module home and full menu sidebar views"
                />
            </div>
        </div>
        @endif

        <flux:separator />

        {{-- Profile --}}
        <flux:dropdown position="top" align="start">
            <flux:profile
                :name="auth()->user()->full_name ?? auth()->user()->first_name"
                :avatar="null"
                icon-trailing="chevron-up-down"
            />
            <flux:menu class="min-w-[200px]">
                <button type="button" class="js-enable-communication-alerts w-full px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Sound alerts
                </button>
                <p class="js-communication-alerts-status px-3 pb-2 text-xs text-zinc-500"></p>
                @if(! $commsOnlyStaff)
                <flux:separator />
                <flux:menu.item icon="arrow-left" href="/ngn-admin/dashboard">
                    Back to Backpack
                </flux:menu.item>
                @endif
                <flux:separator />
                <form method="POST" action="{{ route('flux-admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <flux:icon name="arrow-right-start-on-rectangle" class="size-4" />
                        Sign out
                    </button>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    {{-- Main column: min-w-0 stops wide tables from growing under the sidebar; overflow-y keeps scroll in this pane. --}}
    <div class="flux-admin-main-column flex min-h-dvh w-full min-w-0 flex-1 flex-col lg:min-h-screen">
        <flux:header
            class="flux-admin-top-header flex shrink-0 items-center border-b border-zinc-200 bg-white px-3 py-2 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 sm:px-4 lg:px-6 lg:py-3"
            x-data="{ searchOpen: false }"
            @keydown.escape.window="searchOpen = false"
        >
            <flux:sidebar.toggle class="shrink-0 lg:hidden" icon="bars-2" aria-label="Open menu" x-show="!searchOpen" x-cloak />

            @if(! $commsOnlyStaff)
            <div class="flux-admin-header-search-row flex min-w-0 flex-1 items-center gap-2 lg:hidden" x-show="searchOpen" x-cloak>
                <button
                    type="button"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                    @click="searchOpen = false"
                    aria-label="Close search"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>

                <form action="{{ route('flux-admin.search') }}" method="get" class="flux-admin-header-search-form flex min-w-0 flex-1 items-center gap-2">
                    <div class="flux-admin-header-search-field min-w-0 flex-1">
                        <flux:input
                            id="flux-admin-header-search"
                            name="q"
                            value="{{ request('q') }}"
                            icon="magnifying-glass"
                            placeholder="Search…"
                            variant="outline"
                            size="sm"
                            class="w-full"
                        />
                    </div>
                    <flux:button type="submit" size="sm" variant="primary" class="!rounded-none shrink-0">Search</flux:button>
                </form>
            </div>
            @endif

            <div class="hidden min-w-0 sm:block lg:max-w-[12rem] lg:shrink-0" x-show="!searchOpen" x-cloak>
                <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-500">NGN Motors</div>
                <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $title ?? 'Flux Admin' }}</div>
            </div>

            <flux:spacer class="!hidden lg:!block" />

            @if(! $commsOnlyStaff)
            <form action="{{ route('flux-admin.search') }}" method="get" class="hidden min-w-0 lg:flex lg:max-w-md lg:flex-none">
                <flux:input name="q" value="{{ request('q') }}" icon="magnifying-glass" placeholder="Search menu or records…" variant="outline" size="sm" class="w-full" />
            </form>
            @endif

            <flux:spacer class="!hidden lg:!block" />

            @if(! $commsOnlyStaff)
            <flux:button
                type="button"
                size="sm"
                variant="ghost"
                icon="magnifying-glass"
                class="!rounded-none shrink-0 lg:hidden"
                x-show="!searchOpen"
                x-cloak
                @click="searchOpen = true; $nextTick(() => document.getElementById('flux-admin-header-search')?.focus())"
                aria-label="Open search"
            >
                Search
            </flux:button>
            @endif

            <div class="flex shrink-0 items-center gap-2" x-show="!searchOpen" x-cloak>
                <button
                    type="button"
                    class="js-enable-communication-alerts inline-flex h-8 items-center gap-1.5 border border-zinc-200 bg-white px-2.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    title="Turn on sound when a notification is sent or received"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M17.95 6.05a8 8 0 010 11.9M11 5L6 9H3v6h3l5 4V5z"/></svg>
                    Sound alerts
                </button>
                @if(\App\Support\FluxAdminAccess::canViewCommunicationsLog())
                <a href="{{ route('flux-admin.communications.sent.index') }}" class="flux-admin-header-unread">
                    <flux:button size="sm" variant="ghost" icon="bell" class="!rounded-none">Notifications</flux:button>
                    <span class="js-staff-notifications-unread flux-admin-unread-badge{{ ($staffUnread['notifications'] ?? 0) > 0 ? '' : ' hidden' }}" data-count="{{ (int) ($staffUnread['notifications'] ?? 0) }}">{{ ($staffUnread['notifications'] ?? 0) > 99 ? '99+' : (int) ($staffUnread['notifications'] ?? 0) }}</span>
                </a>
                @endif
                @if(! $commsOnlyStaff)
                <a href="{{ route('flux-admin.dashboard') }}">
                    <flux:button size="sm" variant="ghost" icon="home" class="!rounded-none">Dashboard</flux:button>
                </a>
                @endif
                <button
                    type="button"
                    x-data
                    @click="window.ngnSetColourMode && window.ngnSetColourMode(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                    class="inline-flex h-8 items-center gap-1.5 border border-zinc-200 bg-white px-2.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    title="Toggle light / dark mode"
                    aria-label="Toggle light and dark mode"
                >
                    <svg class="h-4 w-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="h-4 w-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="dark:hidden">Dark</span>
                    <span class="hidden dark:inline">Light</span>
                </button>
                @if(! $commsOnlyStaff)
                <a href="/ngn-admin/dashboard">
                    <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Backpack</flux:button>
                </a>
                @endif
            </div>
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
    <script>
        function registerFluxAdminNavigationPreference() {
            if (!window.Alpine || window.Alpine.__fluxAdminNavigationPreferenceRegistered) {
                return;
            }

            window.Alpine.__fluxAdminNavigationPreferenceRegistered = true;
            Alpine.data('fluxAdminNavigationPreference', function () {
                var storageKey = 'flux-admin:navigation-mode-v2:{{ auth()->id() }}';
                var savedMode = 'home';

                try {
                    savedMode = localStorage.getItem(storageKey) === 'menu' ? 'menu' : 'home';
                } catch (error) {}

                return {
                    navMode: savedMode,
                    setNavMode: function (mode) {
                        this.navMode = mode === 'menu' ? 'menu' : 'home';
                        try {
                            localStorage.setItem(storageKey, this.navMode);
                        } catch (error) {}
                    },
                };
            });
        }

        if (window.Alpine) {
            registerFluxAdminNavigationPreference();
        } else {
            document.addEventListener('alpine:init', registerFluxAdminNavigationPreference, { once: true });
        }
    </script>
    <script>
        (function () {
            function ensureFluxDialogModal(dialog) {
                if (!(dialog instanceof HTMLDialogElement)) {
                    return;
                }
                if (dialog.hasAttribute('data-flux-flyout')) {
                    return;
                }
                if (!dialog.open || typeof dialog.showModal !== 'function') {
                    return;
                }
                try {
                    if (!dialog.matches(':modal')) {
                        dialog.showModal();
                    }
                } catch (err) {}
            }

            function scanFluxDialogs(root) {
                root.querySelectorAll('[data-flux-modal] > dialog:not([data-flux-flyout]), ui-modal > dialog:not([data-flux-flyout])')
                    .forEach(ensureFluxDialogModal);
            }

            document.addEventListener('livewire:init', function () {
                scanFluxDialogs(document.body);
                Livewire.hook('morph.updated', function () {
                    scanFluxDialogs(document.body);
                });
            });

            new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'attributes'
                        && mutation.attributeName === 'open'
                        && mutation.target instanceof HTMLDialogElement) {
                        ensureFluxDialogModal(mutation.target);
                    }
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1) {
                            scanFluxDialogs(node);
                        }
                    });
                });
            }).observe(document.body, {
                subtree: true,
                childList: true,
                attributes: true,
                attributeFilter: ['open'],
            });
        })();
    </script>
    <script>
        (function () {
            var key = 'flux-admin:navlist-scroll';

            function navlist() {
                return document.getElementById('flux-admin-navlist')
                    || document.querySelector('[data-flux-sidebar] [data-flux-navlist]');
            }

            function readScroll() {
                var raw = sessionStorage.getItem(key);
                if (raw === null) {
                    return null;
                }
                var top = parseInt(raw, 10);
                return Number.isNaN(top) ? null : top;
            }

            function saveScroll() {
                var el = navlist();
                if (!el) {
                    return;
                }
                sessionStorage.setItem(key, String(el.scrollTop));
            }

            function restoreScroll() {
                var el = navlist();
                var top = readScroll();
                if (!el || top === null) {
                    return;
                }
                el.scrollTop = top;
            }

            function restoreScrollUntilStable() {
                var top = readScroll();
                if (top === null) {
                    return;
                }
                var attempts = 0;
                var tick = function () {
                    var el = navlist();
                    if (!el) {
                        return;
                    }
                    if (el.scrollTop !== top) {
                        el.scrollTop = top;
                    }
                    if (++attempts < 10) {
                        requestAnimationFrame(tick);
                    }
                };
                requestAnimationFrame(tick);
            }

            function bindNavlist() {
                var el = navlist();
                if (!el || el.dataset.scrollBound === '1') {
                    return;
                }
                el.dataset.scrollBound = '1';
                el.addEventListener('scroll', saveScroll, { passive: true });
            }

            document.addEventListener('click', function (event) {
                if (event.target.closest('[data-flux-sidebar] a[href]')) {
                    saveScroll();
                    if (window.matchMedia('(max-width: 1023px)').matches) {
                        var sidebar = document.querySelector('[data-flux-sidebar]');
                        if (sidebar && !sidebar.hasAttribute('data-flux-sidebar-collapsed-mobile')) {
                            document.dispatchEvent(new CustomEvent('flux-sidebar-toggle', { bubbles: true }));
                        }
                    }
                }
            }, true);

            document.addEventListener('livewire:navigating', saveScroll);
            document.addEventListener('livewire:navigated', function () {
                bindNavlist();
                restoreScrollUntilStable();
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    bindNavlist();
                    restoreScroll();
                });
            } else {
                bindNavlist();
                restoreScroll();
            }
        })();

        // Keep live searchable suggestion lists above scroll/overflow clipping (top-layer popover).
        (function () {
            var scheduled = false;

            function wrapFor(menu) {
                return menu.closest('.flux-admin-autocomplete');
            }

            function positionMenu(menu, wrap) {
                // Anchor to the whole autocomplete field (not an inner/hidden input node).
                var rect = wrap.getBoundingClientRect();
                if (!rect.width && !rect.height) {
                    return;
                }

                var width = Math.max(rect.width, 220);
                var left = Math.max(8, Math.min(rect.left, window.innerWidth - width - 8));
                var spaceBelow = window.innerHeight - rect.bottom;
                var preferUp = spaceBelow < 200 && rect.top > spaceBelow;

                // Must clear UA popover shorthand (inset:0; margin:auto) or left/top are ignored.
                menu.style.setProperty('position', 'fixed', 'important');
                menu.style.setProperty('inset', 'auto', 'important');
                menu.style.setProperty('margin', '0', 'important');
                menu.style.setProperty('right', 'auto', 'important');
                menu.style.setProperty('left', left + 'px', 'important');
                menu.style.setProperty('width', width + 'px', 'important');
                menu.style.setProperty('max-width', 'calc(100vw - 16px)', 'important');
                menu.style.setProperty('z-index', '10070', 'important');

                if (preferUp) {
                    menu.style.setProperty('top', 'auto', 'important');
                    menu.style.setProperty('bottom', (window.innerHeight - rect.top + 2) + 'px', 'important');
                } else {
                    menu.style.setProperty('bottom', 'auto', 'important');
                    menu.style.setProperty('top', (rect.bottom + 2) + 'px', 'important');
                }
            }

            function syncAutocompletes() {
                scheduled = false;
                document.querySelectorAll('.flux-admin-autocomplete .flux-admin-autocomplete-menu').forEach(function (menu) {
                    var wrap = wrapFor(menu);
                    if (!wrap || !wrap.classList.contains('flux-admin-autocomplete-open')) {
                        if (typeof menu.hidePopover === 'function' && menu.matches(':popover-open')) {
                            try { menu.hidePopover(); } catch (e) {}
                        }
                        return;
                    }

                    if (typeof menu.showPopover === 'function') {
                        if (!menu.hasAttribute('popover')) {
                            menu.setAttribute('popover', 'manual');
                        }
                        if (!menu.matches(':popover-open')) {
                            try {
                                menu.showPopover();
                            } catch (e) {
                                // Fall through to fixed positioning below the field.
                            }
                        }
                    }

                    positionMenu(menu, wrap);
                });
            }

            function scheduleSync() {
                if (scheduled) {
                    return;
                }
                scheduled = true;
                requestAnimationFrame(function () {
                    requestAnimationFrame(syncAutocompletes);
                });
            }

            document.addEventListener('scroll', scheduleSync, true);
            window.addEventListener('resize', scheduleSync);
            document.addEventListener('livewire:navigated', scheduleSync);
            document.addEventListener('DOMContentLoaded', scheduleSync);

            document.addEventListener('livewire:init', function () {
                if (typeof Livewire === 'undefined' || !Livewire.hook) {
                    return;
                }
                Livewire.hook('morph.updated', scheduleSync);
                Livewire.hook('commit', function ({ succeed }) {
                    succeed(scheduleSync);
                });
            });

            document.addEventListener('livewire:init', function () {
                if (typeof Livewire === 'undefined' || !Livewire.on) {
                    return;
                }

                Livewire.on('flux-admin:toast', function (payload) {
                    var type = payload && payload.type ? payload.type : 'success';
                    var message = payload && payload.message ? payload.message : '';
                    var heading = payload && payload.heading ? payload.heading : null;
                    var variantMap = { success: 'success', warning: 'warning', error: 'danger', danger: 'danger' };
                    var variant = variantMap[type] || 'success';
                    var duration = (type === 'error' || type === 'danger') ? 15000 : 5000;

                    if (!heading && (type === 'error' || type === 'danger')) {
                        heading = 'Action failed';
                    }

                    document.dispatchEvent(new CustomEvent('toast-show', {
                        detail: {
                            duration: duration,
                            slots: {
                                text: message,
                                heading: heading,
                            },
                            dataset: {
                                variant: variant,
                            },
                        },
                    }));
                });
            });

            if (typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(scheduleSync);
                var start = function () {
                    if (!document.body) {
                        return;
                    }
                    observer.observe(document.body, { childList: true, subtree: true });
                    scheduleSync();
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', start);
                } else {
                    start();
                }
            }
        })();
    </script>
</body>
</html>
