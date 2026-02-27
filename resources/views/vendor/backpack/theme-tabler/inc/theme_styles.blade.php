{{-- Override: load Tabler CSS via CDN + direct vendor path so Backpack has CSS even when Basset cache is empty. --}}
<script>document.documentElement.setAttribute("data-bs-theme", localStorage.colorMode ?? 'light');</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" integrity="sha256-fvdQvRBUamldCxJ2etgEi9jz7F3n2u+xBn+dDao9HJo=" crossorigin="anonymous">
<link rel="stylesheet" href="{{ route('backpack.theme-tabler.asset', ['package' => 'theme-tabler', 'path' => 'resources/assets/css/style.css']) }}">
<link rel="stylesheet" href="{{ route('backpack.theme-tabler.asset', ['package' => 'theme-tabler', 'path' => 'resources/assets/css/color-adjustments.css']) }}">
