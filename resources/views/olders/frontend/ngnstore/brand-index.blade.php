@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Brands')

@section('content')
    <div class="brand-landing-page">
        <h1>All Brands</h1>
        <p>Discover the brands we have in store.</p>

        <div class="brand-list">
            @foreach($brands as $brand)
                <div class="brand-item">
                    <h2><a href="{{ route('ngn_brand_listing', ['slug' => $brand->slug]) }}">{{ $brand->name }}</a></h2>
                    <p>{{ $brand->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
