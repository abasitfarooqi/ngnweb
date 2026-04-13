@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Products Index')

@section('content')
    <div class="supplier-landing-page">
        <h1>All Products</h1>
        <p>Meet our Products.</p>


        @foreach ($products as $product)
            <div class="product-item">
                <h2><a href="{{ route('ngn_product_details', ['sku' => $product->sku]) }}">{{ $product->name }}</a></h2>
                <p>{{ $product->description }}</p>
            </div>
        @endforeach

        
        <!-- <div class="product-list">
                @foreach ($suppliers as $supplier)
    <div class="supplier-item">
                        <h2><a href="{{ route('ngn_supplier_listing', ['slug' => $supplier->slug]) }}">{{ $supplier->name }}</a></h2>
                        <p>{{ $supplier->description }}</p>
                    </div>
    @endforeach
            </div> -->
    </div>
@endsection
