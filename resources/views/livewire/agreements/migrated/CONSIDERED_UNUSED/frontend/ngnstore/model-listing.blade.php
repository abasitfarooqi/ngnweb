@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Model Listing Page')

@section('content')
    <h1>Model Listing</h1>
    <!-- Model-specific product listing content goes here -->
    @foreach ($products as $product)
        <div class="product">
            <h2>{{ $product->name }}</h2>
            <!-- Add more product details as needed -->
        </div>
    @endforeach
@endsection
