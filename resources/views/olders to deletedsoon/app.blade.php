{{-- URI: NGM-WEB/resources/views/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite('resources/js/app.js') <!-- Include the Vue entry point -->
</head>
<body>
    <header>
        @include('partials.header') <!-- Blade header -->
    </header>

    <main>
        @yield('content') <!-- Main content area -->
    </main>

    <footer>
        @include('partials.footer') <!-- Blade footer -->
    </footer>
</body>
</html>