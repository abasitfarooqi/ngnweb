<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- No Vite: error pages must render even when manifest is missing (e.g. production before npm run build) --}}
    <style>body{font-family:system-ui,sans-serif;margin:0;background:#f3f4f6}.min-h-screen{min-height:100vh}.flex{display:flex}.items-center{align-items:center}.justify-center{justify-content:center}.px-4{padding-left:1rem;padding-right:1rem}.py-12{padding-top:3rem;padding-bottom:3rem}.text-center{text-align:center}.text-6xl{font-size:3.75rem}.font-bold{font-weight:700}.text-red-600{color:#dc2626}.mt-6{margin-top:1.5rem}.text-xl{font-size:1.25rem}</style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <main class="min-h-screen flex items-center justify-center px-4 py-12">
        @yield('content')
    </main>
</body>
</html>
