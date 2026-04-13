@foreach ($products as $product)
    <div class="card mb-3">
        <div class="card-body">
            <img loading="lazy" src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width: 200px;">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text">{{ $product->description }}</p>
            <p class="card-text"><strong>Price: ${{ $product->normal_price }}</strong></p>
        </div>
    </div>
@endforeach

@if($products->isEmpty())
    <p>No products found matching the selected filters.</p>
@endif
