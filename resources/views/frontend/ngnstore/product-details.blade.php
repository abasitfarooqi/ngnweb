@extends('frontend.ngnstore.layouts.master')

@section('title', 'Product Details - ' . $product->name)

@section('content')
    <div class="container pt-3 pb-3">
        <img loading="lazy" src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width: 300px;">
        <h1>{{ $product->name }}</h1>
        {{-- <p><strong>Price:</strong> {{ $product->normal_price }}</p> --}}
        <p><strong>Description:</strong> {{ $product->description }}</p>
        <p><strong>Brand:</strong> {{ $product->brand->name }}</p>
        <!-- You can add more fields as needed -->
    </div>
@endsection
