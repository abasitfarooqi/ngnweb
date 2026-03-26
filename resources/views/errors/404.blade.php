<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | NGN Motors</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <span class="text-9xl font-black text-brand-red leading-none">404</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Page Not Found</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            Sorry, the page you are looking for does not exist or has been moved.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-brand-red text-white font-medium hover:bg-red-700 transition">
                Go to Homepage
            </a>
            <a href="{{ url('/contact') }}"
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                Contact Support
            </a>
        </div>
        <p class="mt-8 text-sm text-gray-400">
            <a href="javascript:history.back()" class="hover:text-brand-red transition">← Go Back</a>
        </p>
    </div>
</body>
</html>
