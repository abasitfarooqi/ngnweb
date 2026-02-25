<link rel="stylesheet" href="{{ asset('/assets/css/product-tab-carousel.css') }}">
<div>
    <div class="text-center">
        <h2 class="fw-600" style="font-size: 2.2em; margin: 0; padding: 15px 0 10px 0;">Featured Helmets</h2>
    </div>

    <div class="scroll-buttons mt-2" style="padding-left:3rem; padding-right:3rem;">
        <button class="btn ngn-btn mb-2 scroll-left"><i class="fa fa-chevron-left mx-2"></i></button>
        <button class="btn ngn-btn mb-2 scroll-right"><i class="fa fa-chevron-right mx-2"></i></button>
    </div>

    <div class="scrollable-container">
        @foreach ($products as $key => $product)
            <div class="item">
                <a class="tbtn tbtn-primary " data-toggle="tab" href="#tab{{ $key }}">
                    <div class="image-container">
                        <img src="{{ asset($product['image_1']) }}" class="main-image">
                        <img src="{{ asset($product['image_2']) }}" class="hover-image">
                    </div>
                    <h5 class="prod-title">{{ $product['title'] }}</h5>
                    <p style="color:black; font-size:18px;">Price: {{ $product['price'] }}</p>
                </a>
            </div>
        @endforeach
    </div>

    <div class="p-5" style="padding-top:inherit !important; padding-bottom:inherit !important;">
        <div class="custom-scrollbar mt-2">
            <div class="scroller"></div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="tab-content">
            @foreach ($products as $key => $product)
                <div id="tab{{ $key }}" class="tab-pane fade {{ $key == 0 ? 'show active' : '' }}">
                    <div class="row">
                        <div class="col-md-4 d-none d-lg-block d-md-block">
                            <img src="{{ $product['image_1'] }}" class="img-fluid">
                        </div>
                        <div class="col-md-8">
                            <h3 style="font-weight: bold; color: #e81932; font-size:19px;">{{ $product['title'] }}</h3>
                            <p style="margin: 5px 0; font-weight:600; color:black;">Price.: {{ $product['price'] }}</p>
                            <p style="margin: 5px 0; font-weight:600; color:black;">Description: {!! $product['description'] !!}
                            </p>
                            <p style="margin-top: 10px; font-size:15px; color:black;">
                                <strong>CALL NOW FOR ENQUIRY 02083141498</strong>
                            </p>
                            <button class="btn btn-primary">Add to Cart</button>
                            <button class="btn btn-danger">Sold</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="{{ asset('/assets/js/product-tab-carousel.js') }}"></script>
