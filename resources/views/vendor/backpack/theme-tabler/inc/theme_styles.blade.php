{{--
    Render a blocking script in <head> to set the theme attribute before paint,
    preventing white flashes when colour mode is set to dark.
--}}
<script>document.documentElement.setAttribute("data-bs-theme", localStorage.colorMode ?? 'light');</script>

@basset('https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css', true, ['integrity' => 'sha256-fvdQvRBUamldCxJ2etgEi9jz7F3n2u+xBn+dDao9HJo=', 'crossorigin' => 'anonymous'])
@basset(base_path('vendor/backpack/theme-tabler/resources/assets/css/style.css'))
@basset(base_path('vendor/backpack/theme-tabler/resources/assets/css/color-adjustments.css'))
