@extends('olders.frontend.main_master')

<title>
    @yield('title', $motorcycle->make . ' ' . $motorcycle->model . ' - Used For Sale - NGN - Motorcycle Rentals, Repairs, Sale in UK')
</title>

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
                    <div class="pagehero-title-heading xt">
                        <h1 class="title">{{ $motorcycle->make ?? 'Make Not Specified' }}
                            {{ $motorcycle->model ?? 'Model Not Specified' }}</h1>
                    </div>
                    <div class="breadcrumbs">
                        <ul>
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/used-motorcycles">Used Motorcycle Sales</a></li>
                            <li><a href="/used-motorcycle/{{ $motorcycle->id ?? '#' }}">{{ $motorcycle->make ?? 'Make Not Specified' }}
                                    {{ $motorcycle->model ?? 'Model Not Specified' }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main section for bike details -->
    <section class="flat-row main-shop shop-detail style-1">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="flat-image-box clearfix">
                        <div class="inner padding-top-4">
                            <!-- Displaying multiple images -->
                            <ul class="motorcycle-list-fix-image">
                                @if($motorcycle->image_one || $motorcycle->image_two || $motorcycle->image_three || $motorcycle->image_four)
                                    <!-- Carousel for images -->
                                    <div id="motorcycleDetailCarousel" class="carousel slide" data-bs-ride="carousel"
                                        data-bs-interval="2000">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img loading="lazy"
                                                    src="{{ $motorcycle->image_one ? 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_one : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                    alt="Motorcycle Image" style="width:100%;">
                                            </div>
                                            @if($motorcycle->image_two)
                                                <div class="carousel-item">
                                                    <img loading="lazy"
                                                        src="{{ 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_two }}"
                                                        alt="Motorcycle Image" style="width:100%;">
                                                </div>
                                            @endif
                                            @if($motorcycle->image_three)
                                                <div class="carousel-item">
                                                    <img loading="lazy"
                                                        src="{{ 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_three }}"
                                                        alt="Motorcycle Image" style="width:100%;">
                                                </div>
                                            @endif
                                            @if($motorcycle->image_four)
                                                <div class="carousel-item">
                                                    <img loading="lazy"
                                                        src="{{ 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorcycle->image_four }}"
                                                        alt="Motorcycle Image" style="width:100%;">
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Controls for carousel -->
                                        <button style="background: rgba(0,0,0,0.8);" class="carousel-control-prev" type="button" data-bs-target="#motorcycleDetailCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button style="background: rgba(0,0,0,0.8);" class="carousel-control-next" type="button" data-bs-target="#motorcycleDetailCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                @else
                                    <li>
                                        <img loading="lazy"
                                            src="https://neguinhomotors.co.uk/assets/img/no-image.png" alt="No Image Available"
                                            style="width:100%;">
                                    </li>
                                @endif
                            </ul>

                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="product-detail">
                        <div class="inner">
                            <form action="{{ route('store.cart', $motorcycle->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="content-detail form-group">
                                    <h2 class="product-title">
                                        <strong>{{ $motorcycle->make ?? 'Make Not Specified' }}</strong>
                                        <strong>{{ $motorcycle->model ?? 'Model Not Specified' }}</strong></h2>

                                    <ul class="mt-3">
                                        <li><span>Registration Number:
                                            </span><strong>****{{ substr($motorcycle->reg_no, -3) }}</strong></li>
                                        <li><span>Year: </span><strong>{{ $motorcycle->year }}</strong></li>
                                        <li><span>Engine: </span><strong>{{ $motorcycle->engine }}</strong></li>
                                        <li><span>Color: </span><strong>{{ $motorcycle->color }}</strong></li>
                                        <li><span>Mileage:
                                            </span><strong>{{ number_format($motorcycle->mileage, 0, '.', '') }}
                                                miles</strong></li>
                                        <li><span>Condition:
                                            </span><strong>{{ $motorcycle->condition ?? 'Not Specified' }}</strong></li>
                                    </ul>

                                    <div class="price margin-top-24">
                                        <ins><span
                                                class="amount"><strong>£{{ $motorcycle->price ?? 'Not Specified' }}</strong></span></ins>
                                    </div>

                                    <br>
                                    <a href="tel:02083141498" class="btn-shape effect-on-btn ngn-btn">CALL NOW
                                        02083141498</a>
                                    <style>
                                        ul,
                                        ol {
                                            list-style-type: inherit;
                                            padding-left: 10px;
                                            margin-left: 10px;
                                        }

                                        ul li,
                                        ol li {
                                            list-style-type: inherit;
                                            padding-left: 10px;
                                            margin-left: 10px;
                                        }
                                    </style>
                                    @if($motorcycle->accessories)
                                        <div class="used-motorbike-accessories mt-5">
                                            <h4 class="color-active">Accessories Included:</h4>
                                            {!! $motorcycle->accessories !!}

                                            
                                            <!-- @isset($branchName)
                                                <ol style="border-top: 1px solid #000; padding: 0px 0px 0px 0px !important; margin: 0px !important;">
                                                    <li class="mt-3"><span>Branch: </span><strong>{{ $branchName }}</strong></li>
                                                </ol>
                                            @endisset -->
                                        </div>
                                    @endif
                                    @isset($branchName)
                                        <div class="used-motorbike-branch mt-5">
                                            <li style="list-style-type:none !important;"><span>Branch: </span><strong>{{ $branchName }}</strong></li>
                                        </div>
                                    @endisset
                                    <ul class="flat-socials margin-top-36">
                                        <li><a href="https://www.facebook.com/p/Neguinho-Motors-LTD-100063111406747/"><i
                                                    class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://www.instagram.com/neguinho_motors/"><i
                                                    class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </form>

                            <!-- Enquiry Form Section -->
                            <div id="contactForm" style="display: none; margin-top: 20px;">
                                <h4 class="font-two">Enquiry Form</h4>
                                <form method="POST" action="/store/message" id="contactFormElement">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Name <i class="fa fa-user"></i></label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            placeholder="Your Name" required>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email <i class="fa fa-envelope"></i></label>
                                        <input type="email" class="form-control" name="email" id="email"
                                            placeholder="Your Email" required>
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone <i class="fa fa-phone"></i></label>
                                        <input type="text" class="form-control" name="phone" id="phone"
                                            placeholder="Your Phone Number" required>
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject">Subject <i class="fa fa-tag"></i></label>
                                        <input type="text" class="form-control" name="subject" id="subject"
                                            value="FOR SALE: {{ $motorcycle->make }} {{ $motorcycle->model }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="message">Message <i class="fa fa-comment"></i></label>
                                        <textarea class="form-control" name="message" id="message"
                                            required>I'm interested in your {{ $motorcycle->make }} {{ $motorcycle->model }}.</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">SEND <i
                                            class="fa fa-paper-plane"></i></button>
                                </form>
                            </div>
                            <button id="toggleFormButton" class="ngn-btn" type="button">Enquiry <i
                                    class="fa fa-envelope"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('toggleFormButton').addEventListener('click', function () {
            var form = document.getElementById('contactForm');
            var isFormVisible = form.style.display === 'block';
            form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
            this.textContent = isFormVisible ? 'Enquiry' : 'Hide Form'; // Update button text

            if (!isFormVisible) {
                form.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll to the form
            }
        });
    </script>

    <!-- Tabs for more details (description, reviews) -->
    <section class="flat-row shop-detail-content style-1">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    {{-- <div class="flat-tabs style-1 has-border"> --}}
                        {{-- <div class="inner"> --}}
                            {{-- <ul class="nav nav-tabs justify-content-center" id="ngnClubTabContent" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="description-tab" data-bs-toggle="tab" href="#description"
                                        role="tab" aria-controls="description" aria-selected="true">Description</a>
                                </li>
                            </ul> --}}

                            {{-- <div class="tab-content"> --}}
                                {{-- <div class="tab-pane fade show active" id="description" role="tabpanel"
                                    aria-labelledby="description-tab">
                                    <div class="content-inner p-2" style="color:black;">
                                        <!-- Description content -->
                                        <p>{!! $motorcycle->description ?? 'Description Not Available' !!}</p>
                                    </div>
                                </div> --}}

                                {{-- <div class="tab-pane fade" id="additional-info" role="tabpanel"
                                    aria-labelledby="additional-info-tab">
                                    <!-- Additional Information -->
                                    <table class="table">
                                        <tr>
                                            <td>Engine Type</td>
                                            <td>{{ $motorcycle->engine_type ?? 'Not Specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Engine</td>
                                            <td>{{ $motorcycle->sale_engine ?? 'Not Specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Suspension</td>
                                            <td>{{ $motorcycle->suspension ?? 'Not Specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Brakes</td>
                                            <td>{{ $motorcycle->brakes ?? 'Not Specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Electrical</td>
                                            <td>{{ $motorcycle->electrical ?? 'Not Specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tires</td>
                                            <td>{{ $motorcycle->tyres ?? 'Not Specified' }}</td>
                                        </tr>
                                    </table>
                                </div> --}}

                                {{-- <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                    <!-- Reviews section -->
                                    <h5>Reviews</h5>
                                    <div class="comment-respond review-respond" id="respond">
                                        <h5>Write a review</h5>
                                    </div>
                                </div> --}}
                                {{-- </div> --}}
                            {{-- </div> --}}
                        {{-- </div> --}}
                </div>
            </div>
        </div>
    </section>

@endsection