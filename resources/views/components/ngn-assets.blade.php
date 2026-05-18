@props([
    'jsOnly' => false,
    'cssOnly' => false,
])

@if(config('broadcasting.connections.pusher.key'))
    <meta name="ngn-env:pusher_app_key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="ngn-env:pusher_app_cluster" content="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}">
    <meta name="ngn-env:pusher_host" content="{{ config('broadcasting.connections.pusher.options.host', '') }}">
    <meta name="ngn-env:pusher_port" content="{{ config('broadcasting.connections.pusher.options.port', 443) }}">
    <meta name="ngn-env:pusher_scheme" content="{{ config('broadcasting.connections.pusher.options.scheme', 'https') }}">
@endif

@unless($jsOnly)
    <link rel="stylesheet" href="{{ ngn_asset('app.css') }}">
@endunless

@unless($cssOnly)
    <script src="{{ ngn_asset('app.js') }}" defer></script>
@endunless
