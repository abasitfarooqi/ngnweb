@extends('livewire.agreements.migrated.frontend.shop')

@section('title', 'Products - Motorcycle Rental London, Tooting, Sutton, Catford, UK')

@section('content')
<div class="product-content product-threecolumn product-slidebar clearfix">
    @foreach($products->chunk(3) as $chunk)
    <ul class="product style2 sd1">
        @foreach($chunk as $product)
        <li class="product-item">
            <a href="/product/{{ $product->id }}" class="product-thumb">
                <div class="product-thumb clearfix mb-3">
                    <img loading="lazy" src="{{ $product->image_url }}" alt="image">
                    <!-- span class="new sale">Sale</span -->
                </div>
                <div class="product-info clearfix">
                    <span class="product-title">{{ $product->description }}</span>
                    <div class="price">
                        <del>
                            <!-- span class="regular">£150.00</span -->
                        </del>
                        <ins>
                            <span class="amount">£{{ $product->price }}</span>
                        </ins>
                    </div>
                    <ul>
                        <li>{{ $product->colour }}</li>
                        <li>{{ $product->sku }}</li>
                    </ul>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
    @endforeach
</div>

<div class="product-pagination text-center">
    {{ $products->links() }}
</div>

@endsection
