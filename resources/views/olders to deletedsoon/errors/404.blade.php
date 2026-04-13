@extends('errors.minimal')

@section('title', __('Sorry, this page isn\'t here | 404'))
@section('code', '404')
@section('message')
    <p>{{ __('We know it\'s frustrating. Can we help you find what you were looking for?') }}</p>
    <br>
    <div class="cta-links">
        <a href="{{ url('/') }}" class="effect-on-btn btn-shape">{{ __('Go to Homepage') }}</a>
        <a href="{{ url('/contact') }}" class="effect-on-btn btn-shape">{{ __('Contact Support') }}</a>
    </div>
@endsection
