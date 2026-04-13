@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Login')

@section('content')

    <div class="container login-page">
        <div class="row">
            <div class="col-md-6  pt-3">

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <h3 class="page-title mb-2">Login</h3>
                    <!-- Email Address -->
                    <div>
                        {{-- <x-input-label for="email" :value="__('Email')" /> --}}
                        <x-text-input id="email" class="block mt-1 w-100" type="email" placeholder="Type your email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <!-- Password -->
                    <div class="">
                        {{-- <x-input-label for="password" :value="__('Password')" /> --}}

                        <x-text-input id="password" class="block mt-1 w-100" type="password" placeholder="Type your password"  name="password" required
                            autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block " style="margin-top:-12px;">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                name="remember">
                            <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end ">

                        <x-primary-button class="ml-3 btn-shape effect-on-btn">
                            {{ __('Log in') }}
                        </x-primary-button>

                    </div>
                </form>
            </div>

            <div class="col-md-5  pt-3" style="padding-left:10px;">
                <h3 class="page-title mb-2">Create an account</h3>
                <p>Don't have an account yet? Sign up today to enjoy faster shopping, easy online returns, and the ability to track your orders effortlessly.</p>
                <div class="clearfix"></div>

                <a href="/register" class="btn-shape effect-on-btn-inverse mt-2" style="top: 25px; padding: 11px 50px;">Register</a>
            </div>
        </div>
        <div class="mt-5"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const timezoneInput = document.createElement("input");
            timezoneInput.type = "hidden"; // Hidden input to store timezone
            timezoneInput.name = "timezone"; // Name to match the server-side
            timezoneInput.value = Intl.DateTimeFormat().resolvedOptions().timeZone; // Get user's timezone
            document.querySelector("form").appendChild(timezoneInput); // Append to the form
        });
    </script>
@endsection
