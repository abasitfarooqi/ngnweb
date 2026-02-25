@extends('frontend.ngnstore.layouts.master')

@section('title', 'Brand Listing Page')

@section('content')
    <h1>Brand Listing</h1>
    <!-- Brand-specific product listing content goes here -->
    @foreach ($products as $product)
        <div class="product">
            <h2>{{ $product->name }}</h2>
            <!-- Add more product details as needed -->
        </div>
    @endforeach
@endsection
