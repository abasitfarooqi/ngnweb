@extends('frontend.shop')

@section('content')
<div class="product-content product-threecolumn product-slidebar clearfix">
    @foreach($products->chunk(3) as $chunk)
    <ul class="product style2 sd1">
        @foreach($chunk as $item)
        <li class="product-item">
            <a href="/item/{{ $item->id }}" class="product-thumb">
                <div class="product-thumb clearfix mb-3">
                    <img src="{{ $item->image_url }}" alt="image">
                    <!-- span class="new sale">Sale</span -->
                </div>
                <div class="product-info clearfix">
                    <span class="product-title">{{ $item->description }}</span>
                    <div class="price">
                        <del>
                            <!-- span class="regular">£150.00</span -->
                        </del>
                        <ins>
                            <span class="amount">£{{ $item->price }}</span>
                        </ins>
                    </div>
                    <ul>
                        <li>{{ $item->colour }}</li>
                        <li>{{ $item->sku }}</li>
                    </ul>
                </div>
            </a>
        </li>
        @endforeach
    </ul><!-- /.product -->
    @endforeach
</div><!-- /.product-content -->
<div class="product-pagination pull-right clearfix">
    {{ $products->links() }}
</div>
@endsection
