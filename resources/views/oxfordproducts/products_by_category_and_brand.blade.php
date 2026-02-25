@extends('frontend.main_master')

@section('content')
<div class="container my-5">
<p class="colourized text-uppercase" style="font-size:16px;font-weight:bold;"><a href="{{ route('oxfordproducts.brands') }}">Brands</a> > <span ><a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}">{{ $brand }}</span></a> > {{ $category }}</p>
    <h1 class="colourized fw-bold">{{ $category }}</h1>
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <a href="{{ route('oxfordproducts.showProduct', ['id' => $product->id]) }}">
                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->description }}">
                </a>
                <div class="card-body">
                    <h5 class="card-title">
                    <strong>SKU:</strong> {{ $product->sku }}
                        <a href="{{ route('oxfordproducts.showProduct', ['id' => $product->id]) }}">
                        {{ Str::words($product->description, 20, '...') }}
                        </a>
                        <p style="color: rgb(11, 49, 106);"><strong>Price:</strong> £{{ $product->rrp_inc_vat }}</p>
                        
                    </h5>
                    <!-- <p class="card-text p-4"> -->
                        <!-- <p style="color: rgb(11, 49, 106);"><strong>SKU:</strong> {{ $product->sku }}</p>
                        <p style="color: rgb(11, 49, 106);"><strong>Stock:</strong> {{ $product->stock }}</p>
                        <p style="color: rgb(11, 49, 106);"><strong>{{ $product->extended_description }}</strong></p>
            
                        <a href="#" class="btn btn-primary">Add to Cart</a> -->
                    
                    </p>
            
             
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
