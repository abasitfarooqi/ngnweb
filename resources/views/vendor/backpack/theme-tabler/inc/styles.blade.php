{{-- Override: load Backpack/CRUD CSS via CDN + asset route so admin has styles without Basset. --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.compat.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noty@3.2.0-beta-deprecated/lib/noty.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
<link rel="stylesheet" href="{{ route('backpack.theme-tabler.asset', ['package' => 'crud', 'path' => 'assets/css/common.css']) }}">
@if (backpack_theme_config('styles') && count(backpack_theme_config('styles')))
    @foreach (backpack_theme_config('styles') as $path)
        @if(is_string($path) && str_contains($path, 'theme-tabler'))
            <link rel="stylesheet" href="{{ route('backpack.theme-tabler.asset', ['package' => 'theme-tabler', 'path' => 'resources/assets/css/skins/' . basename($path)]) }}">
        @endif
    @endforeach
@endif
