@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Category Listing Page')

@section('content')
    <h1>Category Listing</h1>
    <!-- Category-specific product listing content goes here -->
    @foreach ($products as $product)
        <div class="product">
            <h2>{{ $product->name }}</h2>
            <!-- Add more product details as needed -->
        </div>
    @endforeach
@endsection
