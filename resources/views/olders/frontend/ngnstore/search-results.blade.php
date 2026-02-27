@extends('olders.frontend.ngnstore.layouts.master')

@section('title', $query . ' Search Results')

@section('content')
    <div class="container pt-3 pb-3">
        <h1 class="text-center">Search Results for "{{ $query }}"</h1>

        {{-- Display product results --}}
        @if($products->isEmpty() && empty($filteredPages))
            <p>No results found for your search.</p>
        @else
            @if($products->isNotEmpty())
                <h2>Products:</h2>
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-2">
                            <a href="{{ route('ngn_product_details', $product->sku) }}"><div class="card h-100">
                                <img loading="lazy" src="{{ $product->image_url !== null && $product->image_url !== 'Not specified' ? $product->image_url : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}" class="card-img-top" alt="{{ $product->name }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    {{-- <p class="card-text">
                                        {!! \Illuminate\Support\Str::limit($product->description, 100) !!}
                                    </p> --}}
                                    <a href="{{ route('ngn_product_details', $product->sku) }}" class="btn ngn-btn font-three">View Product</a>
                                </div></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Display static pages results --}}
            @if(!empty($filteredPages))
                <h2>Pages:</h2>
                <ul>
                    @foreach($filteredPages as $page)
                        <li>
                            <a href="{{ $page['url'] }}">
                                {{ $page['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No static pages found for your search.</p>
            @endif
        @endif
    </div>
@endsection
