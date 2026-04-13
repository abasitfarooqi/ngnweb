@extends('olders.frontend.main_master')

@section(
    'title',
    'Used Motorbike For Sale - NGN - Motorcycle Rentals, Repairs, Accessories in Catford,
    Tooting, UK'
)
@section('meta_keywords')
    <meta name="keywords"
        content="NGN Club, motorcycle rentals, motorcycle repairs, motorcycle MOT,used motorcycle, motorcycle for sale, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
    <meta name="description"
        content="Discover NGN, your premier destination in the UK for new and used motorcycle sales, rentals, repairs, and accessories. Located in Catford, Sutton and Tooting, we offer a wide range of services to meet all your motorcycling needs.">
@endsection

@section('content')

    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading">
                        <h1 class="title">Used Motorcycles For Sale</h1>
                    </div><!-- /.pagehero-title-heading xt -->
                    <div class="breadcrumbs">
                        <ul class="">
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/motorcycle-sales">Motorcycle Sales</a></li>
                            <li><a href="/used-motorcycles">Used Motorcycles for Sales</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    <section class="flat-row main-shop shop-slidebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="">
                        <div class="search-and-sort">
                            <div class="row">
                                <div class="col-md-6"> <input type="text" id="search" class="search-input form-control"
                                        style="background: #f3f3f3;border: 0;border-radius: 5px;color: #101111;"
                                        placeholder="Search by make, model, or reg no" /></div>
                                <div class="col-md-3"> <select id="sort" class="sort-select form-control">
                                        <option value="default">Sort by</option>
                                        <option value="price_asc">Price: Low to High</option>
                                        <option value="price_desc">Price: High to Low</option>
                                        <option value="year_asc">Year: Old to New</option>
                                        <option value="year_desc">Year: New to Old</option>
                                    </select></div>
                                <div class="col-md-3"><!-- New Availability Dropdown -->
                                    <select id="availability" class="availability-select form-control">
                                        <option value="all">All</option>
                                        <option value="available">Available</option>
                                        <option value="sold">Sold</option>
                                    </select>
                                </div>

                            </div>



                        </div>
                    </div><!-- /.sidebar -->
                </div><!-- /.col-md-12 -->

                <div class="col-md-12">
                    <div class="product-content product-threecolumn product-slidebar clearfix">
                        <ul id="product-list" class="row">
                            @foreach ($motorbikes as $motorcycle)
                                <li class="col-lg-3 col-md-4 col-sm-6 col-xs-6 mt-2 mb-2 product-item extra-small-box"
                                    data-price="{{ $motorcycle->price }}" data-year="{{ $motorcycle->year }}"
                                    data-reg-no="{{ substr($motorcycle->reg_no, -4) }}"
                                    data-availability="{{ $motorcycle->is_sold ? 'sold' : 'available' }}">
                                    <div class="product-sq" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $motorcycle->id }}" aria-expanded="false"
                                        aria-controls="collapse{{ $motorcycle->id }}">
                                        <div style="position: relative;">
                                            @if ($motorcycle->is_sold == 1)
                                                <div class="text-left"
                                                    style="position: absolute; top: 11px; left: 14px; z-index: 10;">
                                                    <span class="badge badge-warning sold-badge"
                                                        style="font-size: 21.5px;background-color: #c31924;">Sold</span>
                                                </div>
                                            @else
                                                <div class="text-left"
                                                    style="position: absolute; top: 11px; left: 14px; z-index: 10;">
                                                    <span class="badge badge-warning"
                                                        style="font-size: 21.5px;background-color:rgb(0, 163, 0);border-radius: 6px;">Used</span>
                                                </div>
                                            @endif
                                            <a href="/used-motorcycle/{{ $motorcycle->id }}" style="z-index:auto;">
                                                <div style="position:relative; z-index: 1;" class="ngn-overlay-container">
                                                    @if ($motorcycle->image_one || $motorcycle->image_two || $motorcycle->image_three || $motorcycle->image_four)
                                                        <!-- Carousel for images -->
                                                        <!--
                                                        <div id="motorcycleCarousel{{ $motorcycle->id }}" class="carousel slide "
                                                            data-bs-ride="carousel" data-bs-interval="2000">
                                                            <div class="carousel-inner">
                                                                <div class="carousel-item active ngn-overlay-container">
                                                                    <img loading="lazy" src="{{ $motorcycle->image_one ? 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_one : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                                        class="ngn-overlay-inner" alt="Motorcycle Image">
                                                                </div>
                                                                @if ($motorcycle->image_two)
                                                                    <div class="carousel-item ngn-overlay-container">
                                                                        <img loading="lazy" src="{{ 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_two }}"
                                                                            class="ngn-overlay-inner" alt="Motorcycle Image">
                                                                    </div>
                                                                @endif
                                                                @if ($motorcycle->image_three)
                                                                    <div class="carousel-item ngn-overlay-container">
                                                                        <img loading="lazy" src="{{ 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_three }}"
                                                                            class="ngn-overlay-inner" alt="Motorcycle Image">
                                                                    </div>
                                                                @endif
                                                                @if ($motorcycle->image_four)
                                                                    <div class="carousel-item ngn-overlay-container">
                                                                        <img loading="lazy" src="{{ 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_four }}"
                                                                            class="ngn-overlay-inner" alt="Motorcycle Image">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        -->
                                                        <img loading="lazy" src="{{ $motorcycle->image_one ? 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_one : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                            class="ngn-overlay-inner" alt="Motorcycle Image">
                                                    @else
                                                        <img loading="lazy" src="https://neguinhomotors.co.uk/assets/img/no-image.png"
                                                            alt="No Image Available" class="ngn-overlay-inner ngn-image-w-100">
                                                    @endif
                                                </div>
                                            </a>

                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="motorcycle-card">
                                            <div class="row">
                                                <div class="col-md-10 col-sm-10 col-xs-10">
                                                    <h3 class="product-sq-title">{{ $motorcycle->make }}
                                                        {{ $motorcycle->model }}
                                                    </h3>
                                                    <h4 class="product-sq-price">
                                                        <span>£{{ number_format($motorcycle->price, 2) }}</span>
                                                        <div class="clearfix"></div>
                                                        <strong>****{{ substr($motorcycle->reg_no, -3) }}</strong>
                                                    </h4>
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-2">
                                                    <div class="icon-area text-center">
                                                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="motorcycle-card">
                                                <div id="collapse{{ $motorcycle->id }}" class="collapse"
                                                    aria-labelledby="heading{{ $motorcycle->id }}" data-bs-parent="#accordion">
                                                    <div class="card-body product-sq-toggle-hideshow">
                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-6"><span>Reg No.
                                                                </span></div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                                <strong>****{{ substr($motorcycle->reg_no, -3) }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-6"><span>Year </span>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                                <strong>{{ $motorcycle->year }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-6"><span>Engine </span>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                                <strong>{{ $motorcycle->engine }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-6"><span>Color </span>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                                <strong>{{ $motorcycle->color }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-6"><span>Mileage
                                                                </span></div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                                <strong>{{ number_format($motorcycle->mileage, 0, '.', '') }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="text-align: center;" class="">

                                                <a href="tel:02083141498" class="btn-shape effect-on-btn w-100"
                                                    style="width: 100%; padding: 4px;display: block;">CALL NOW
                                                    02083141498</a>

                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Discover More Bikes</h2>
                        </div>
                        <div class="col-md-12">
                            <div class="product-content product-threecolumn product-slidebar clearfix">
                                @if ($LatestMotorcycles->isNotEmpty())
                                    <ul class="latest-motorcycles-list row">
                                        @foreach ($LatestMotorcycles as $motorcycle)
                                            <li class="col-lg-3 col-md-4 col-sm-6 col-xs-6 mt-2 mb-2 product-item-n extra-small-box"
                                                data-price="{{ $motorcycle->price }}" data-year="{{ $motorcycle->year }}"
                                                data-reg-no="{{ $motorcycle->reg_no }}" data-availability="available">
                                                <div class="product-sq-n">
                                                    <div style="position:relative;" class="ngn-overlay-container">
                                                        <img loading="lazy" src="{{ $motorcycle->file_path ? 'https://neguinhomotors.co.uk' . $motorcycle->file_path : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                            alt="{{ $motorcycle->make }} {{ $motorcycle->model }}"
                                                            class="ngn-overlay-inner">
                                                    </div>
                                                    <h3 class="product-sq-title-n">{{ $motorcycle->make }} {{ $motorcycle->model }}</h3>
                                                    <h4 class="product-sq-price-n">
                                                        <span>£{{ number_format($motorcycle->sale_new_price, 2) }}</span>
                                                    </h4>

                                                    <div class="clearfix"></div>
                                                    <button class="ngn-btn w-100" style="margin-bottom: 0px;" id="toggleFormButton{{ $motorcycle->id }}" type="button">Enquire
                                                        <i class="fa fa-envelope"></i></button>
                                                    <div id="contactForm{{ $motorcycle->id }}" style="display: none; margin-top: 20px;">
                                                        <h4 class="font-two">Enquiry Form for {{ $motorcycle->make }}
                                                            {{ $motorcycle->model }}</h4>
                                                        <form method="POST" action="/store/message"
                                                            id="contactFormElement{{ $motorcycle->id }}">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="name">Name <i class="fa fa-user"></i></label>
                                                                <input type="text" class="form-control" name="name" id="name{{ $motorcycle->id }}"
                                                                    placeholder="Your Name" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="email">Email <i class="fa fa-envelope"></i></label>
                                                                <input type="email" class="form-control" name="email" id="email{{ $motorcycle->id }}"
                                                                    placeholder="Your Email" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="phone">Phone <i class="fa fa-phone"></i></label>
                                                                <input type="text" class="form-control" name="phone" id="phone{{ $motorcycle->id }}"
                                                                    placeholder="Your Phone Number" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="subject">Subject <i class="fa fa-tag"></i></label>
                                                                <input type="text" class="form-control" name="subject" id="subject{{ $motorcycle->id }}"
                                                                    value="FOR SALE: {{ $motorcycle->make }} {{ $motorcycle->model }}"
                                                                    readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="message">Message <i class="fa fa-comment"></i></label>
                                                                <textarea class="form-control" name="message" id="message{{ $motorcycle->id }}"
                                                                    required>I'm interested in your {{ $motorcycle->make }} {{ $motorcycle->model }}.</textarea>
                                                            </div>
                                                            <button type="submit" class="ngn-btn ngn-btn-primary w-100 ngn-bg">SEND <i
                                                                    class="fa fa-paper-plane"></i></button>
                                                        </form>
                                                    </div>
                                                    <script>
                                                        document.getElementById('toggleFormButton{{ $motorcycle->id }}').addEventListener('click', function () {
                                                            var form = document.getElementById('contactForm{{ $motorcycle->id }}');
                                                            var isFormVisible = form.style.display === 'block';
                                                            form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
                                                            this.textContent = isFormVisible ? 'Enquire' : 'Hide Form'; // Update button text

                                                            if (!isFormVisible) {
                                                                form.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll to the form
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No latest motorcycles available at the moment.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="product-pagination text-center clearfix">
                        <ul class="flat-pagination">
                            <li>
                                <!-- Pagination links -->
                            </li>
                        </ul>
                    </div>

                </div><!-- /.row -->
            </div><!-- /.container -->
    </section><!-- /.flat-row -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function () {
            // Load filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search') || '';
            const sortParam = urlParams.get('sort') || 'default';
            const availabilityParam = urlParams.get('availability') || 'all';

            $('#search').val(searchParam);
            $('#sort').val(sortParam);
            $('#availability').val(availabilityParam);

            // Apply filters on page load
            filterProducts();

            // Search Functionality
            $('#search').on('input', function () {
                updateUrlParams();
                filterProducts();
            });

            // Sort Functionality
            $('#sort').on('change', function () {
                updateUrlParams();
                sortProducts();
            });

            // Availability Filter Functionality
            $('#availability').on('change', function () {
                updateUrlParams();
                filterProducts();
            });

            function updateUrlParams() {
                const searchValue = $('#search').val();
                const sortValue = $('#sort').val();
                const availabilityValue = $('#availability').val();

                const params = new URLSearchParams();
                if (searchValue) params.set('search', searchValue);
                if (sortValue !== 'default') params.set('sort', sortValue);
                if (availabilityValue !== 'all') params.set('availability', availabilityValue);

                window.history.replaceState({}, '', `${window.location.pathname}?${params}`);
            }

            function filterProducts() {
                var searchValue = $('#search').val().toLowerCase();
                var availabilityValue = $('#availability').val();

                $('.product-item').each(function () {
                    var title = $(this).find('.product-sq-title').text().toLowerCase();
                    var regNo = $(this).data('reg-no').toLowerCase();
                    var availability = $(this).data('availability');

                    var matchesSearch = title.includes(searchValue) || regNo.includes(searchValue);
                    var matchesAvailability = (availabilityValue === 'all') || (availabilityValue === availability);

                    $(this).toggle(matchesSearch && matchesAvailability);
                });
            }

            function sortProducts() {
                var sortValue = $('#sort').val();
                var products = $('.product-item').toArray();

                products.sort(function (a, b) {
                    var aValue, bValue;
                    switch (sortValue) {
                        case 'price_asc':
                            aValue = parseFloat($(a).find('.product-sq-price span').text().replace('£', '').replace(',', ''));
                            bValue = parseFloat($(b).find('.product-sq-price span').text().replace('£', '').replace(',', ''));
                            return aValue - bValue;
                        case 'price_desc':
                            aValue = parseFloat($(a).find('.product-sq-price span').text().replace('£', '').replace(',', ''));
                            bValue = parseFloat($(b).find('.product-sq-price span').text().replace('£', '').replace(',', ''));
                            return bValue - aValue;
                        case 'year_asc':
                            aValue = parseInt($(a).data('year'));
                            bValue = parseInt($(b).data('year'));
                            return aValue - bValue;
                        case 'year_desc':
                            aValue = parseInt($(a).data('year'));
                            bValue = parseInt($(b).data('year'));
                            return bValue - aValue;
                        default:
                            return 0;
                    }
                });

                $('#product-list').html(products);
            }
        });
    </script>
@endsection