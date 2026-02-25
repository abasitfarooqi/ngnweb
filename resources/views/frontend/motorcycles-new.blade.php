@extends('frontend.main_master')

@section('title', 'Motorcycles For Sale - New and Used Motorcycles for Sale in London, Catford, Tooting and Sutton')

@section('content')

<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Motorcycles For Sale</h1>
                </div>
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="{{ route('sale-motorcycles') }}">Motorcycle Sales</a></li>
                        <li><a href="{{ route('motorcycles.new') }}">Motorcycles for Sale</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.flat-row.main-shop.shop-slidebar{
    padding: 0px 0px 29px 0px;
}
.form-control{
    -webkit-appearance: inherit !important;
  -moz-appearance: inherit !important;
  appearance: inherit !important;
}
</style>
<div class="container">
<div class="search-and-sort">
    <div class="row mt-3">
        <div class="col-md-6">
            <input type="text" id="search" class="search-input form-control"
                style="background: #f3f3f3;border: 0;border-radius: 5px;color: #101111;"
                placeholder="Search by make, model" />
        </div>
        <div class="col-md-3">
            <select id="sort" class="sort-select form-control">
                <option value="default">Sort by</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
            </select>
        </div>
    </div>
</div>
</div>


<section class="flat-row main-shop shop-slidebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-content product-threecolumn product-slidebar clearfix">
                    @foreach ($motorcycles->chunk(3) as $chunk)
                    <ul class="product style2 sd1">
                        @foreach($chunk as $motorcycle)
                        <li class="product-item new">
                            <div class="product-thumb clearfix mb-3">
                                <a href="/new-motorcycle/{{ $motorcycle->id }}">
                                    @php
                                        $imagePath = $motorcycle->file_path;
                                        $fullPath = '';

                                        if ($imagePath) {
                                            if (is_string($imagePath)) {
                                                if (strpos($imagePath, '/storage/uploads/') === 0 || strpos($imagePath, '/storage/motorbikes/') === 0) {
                                                    $fullPath = $imagePath;
                                                } else {
                                                    $fullPath = '/storage/motorbikes/' . $imagePath;
                                                }
                                            }
                                        } 
                                        
                                        // Default image if no valid path is found
                                        if (empty($fullPath)) {
                                            $fullPath = 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                                        }
                                    @endphp
                                    <img loading="lazy" src="{{ $fullPath }}" alt="image" style="max-width: 280px;">
                                </a>
                                <span class="new">New</span>
                            </div>
                            <div class="product-info clearfix">
                                <div><span class="product-title">{{ $motorcycle->make }} {{ $motorcycle->model }}</span></div>
                                <ul class="product-infor style-1">
                                    <li><span>{{ $motorcycle->type }}</span></li>
                                    <li><span>{{ $motorcycle->engine }}CC</span></li>
                                </ul>
                                <div class="price">
                                    <!-- <i class="fa fa-gbp" aria-hidden="true"></i> -->
                                    <!-- <ins><span class="amount" value="{{$motorcycle->price}}" name="price" id="price">{{$motorcycle->sale_new_price}}</span></ins> -->
                                </div>
                            </div>
                            <div class="add-to-cart text-center">
                                <a href="/new-motorcycle/{{ $motorcycle->id }}">MORE INFORMATION</a>
                            </div>
                            <a href="#" class="like"><i class="fa fa-heart-o"></i></a>
                        </li>
                        @endforeach
                    </ul>
                    @endforeach
                </div>
                <div class="product-pagination text-center clearfix">
                    <ul class="flat-pagination">
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Load filters from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search') || '';
        const sortParam = urlParams.get('sort') || 'default';

        $('#search').val(searchParam);
        $('#sort').val(sortParam);

        // Apply filters on page load
        filterProducts();

        // Search Functionality
        $('#search').on('input', function() {
            filterProducts();
            updateUrlParams();
        });

        // Sort Functionality
        $('#sort').on('change', function() {
            sortProducts();
            updateUrlParams();
        });

        function updateUrlParams() {
            const searchValue = $('#search').val();
            const sortValue = $('#sort').val();

            const params = new URLSearchParams();
            if (searchValue) params.set('search', searchValue);
            if (sortValue !== 'default') params.set('sort', sortValue);

            window.history.replaceState({}, '', `${window.location.pathname}?${params}`);
        }

        function filterProducts() {
            var searchValue = $('#search').val().toLowerCase();
            var products = $('.product-item').toArray();

            products.forEach(function(product) {
                var title = $(product).find('.product-title').text().toLowerCase();
                $(product).toggle(title.includes(searchValue));
            });
        }

        function sortProducts() {
            var sortValue = $('#sort').val();
            var products = $('.product-item').toArray();

            products.sort(function(a, b) {
                var aPrice = parseFloat($(a).find('.amount').text());
                var bPrice = parseFloat($(b).find('.amount').text());

                switch (sortValue) {
                    case 'price_asc':
                        return aPrice - bPrice;
                    case 'price_desc':
                        return bPrice - aPrice;
                    default:
                        return 0;
                }
            });

            // Append sorted products back to the list
            $('.product.style2.sd1').empty().append(products);
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endsection
