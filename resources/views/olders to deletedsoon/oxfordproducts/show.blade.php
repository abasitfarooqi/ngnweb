@extends('olders.frontend.main_master')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ $product->image_url }}" class="img-fluid" alt="{{ $product->description }}">
        </div>
        <div class="col-md-6">
            <h1>{{ $product->description }}</h1>
            <p><strong>Brand:</strong> {{ $product->brand }}</p>
            <p><strong>Category:</strong> {{ $product->category }}</p>
            <p><strong>SKU:</strong> {{ $product->sku }}</p>
            <p><strong>Price:</strong> £{{ $product->rrp_inc_vat }}</p>
            <p>{{ $product->extended_description }}</p>
            <p><strong>Stock:</strong> {{ $product->stock }}</p>
            <p><strong>Estimated Delivery:</strong> {{ $product->estimated_delivery }}</p>
        </div>
    </div>
</div>
@endsection
