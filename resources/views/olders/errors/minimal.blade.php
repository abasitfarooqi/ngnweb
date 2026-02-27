{{-- NGM-WEB/resources/views/errors/minimal.blade.php --}}
@extends('olders.frontend.main_master')

@section('title', __('Error'))

@section('content')
<br>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 px-4 py-12 w-full">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-600 blink-4s">@yield('code', 'Error')</h1>
            <p class="mt-6 text-xl">@yield('message', 'Something went wrong.')</p>
        </div>
    </div>
@endsection
