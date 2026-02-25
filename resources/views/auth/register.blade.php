@extends('frontend.ngnstore.layouts.master')

@section('title', 'Register')

@section('content')

    <div class="container register-page pt-3">
        <div class="col-md-6">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h3 class="page-title mb-2">Create an account</h3>

                <!-- First Name -->
                {{-- <div> --}}
                    {{-- <x-input-label for="first_name" :value="__('First Name')" /><div class="clearfix"></div> --}}
                    {{-- <x-text-input placeholder="First Name" id="first_name" class="block w-100" type="text" name="first_name" --}}
                        {{-- :value="old('first_name')" required autofocus autocomplete="first_name" /> --}}
                    {{-- <x-input-error :messages="$errors->get('first_name')" class="" /> --}}
                {{-- </div> --}}

                <!-- Last Name -->
                {{-- <div class=""> --}}
                    {{-- <x-input-label for="last_name" :value="__('Last Name')" /><div class="clearfix"></div> --}}
                    {{-- <x-text-input placeholder="Last Name" id="last_name" class="block w-100" type="text" name="last_name" --}}
                        {{-- :value="old('last_name')" required autofocus autocomplete="last_name" /> --}}
                    {{-- <x-input-error :messages="$errors->get('last_name')" class="" /> --}}
                {{-- </div> --}}

                <!-- Hidden -->
                <x-text-input id="is_client" type="hidden" name="is_client" value="1" />
                <x-text-input id="role_id" type="hidden" name="role_id" value="4" />
                <x-text-input id="role" type="hidden" name="role" value="4" />
                <x-text-input id="rating" type="hidden" name="rating" value="good" />

                <!-- Email Address -->
                <div class="">
                    {{-- <x-input-label for="email" :value="__('Email')" /><div class="clearfix"></div> --}}
                    <x-text-input placeholder="Email" id="email" class="block w-100" type="email" name="email"
                        :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="" />
                </div>

                <!-- Password -->
                <div class="">
                    {{-- <x-input-label for="password" :value="__('Password')" /><div class="clearfix"></div> --}}
                    <x-text-input placeholder="Password" id="password" class="block w-100" type="password" name="password"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="" />
                </div>

                <!-- Confirm Password -->
                <div class="">
                    {{-- <x-input-label for="password_confirmation" :value="__('Confirm Password')" /><div class="clearfix"></div> --}}
                    <x-text-input placeholder="Confirm Password" id="password_confirmation" class="block w-100"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="" />
                </div>

                <div class="flex items-center justify-end ">

                    <x-primary-button class="btn-shape effect-on-btn">
                        {{ __('Register') }}
                    </x-primary-button>

                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-one"
                        style="margin-left: 20px;" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>
                </div>
            </form>
        </div>
        <div class="col-md-6">

        </div>
    </div>
@endsection
