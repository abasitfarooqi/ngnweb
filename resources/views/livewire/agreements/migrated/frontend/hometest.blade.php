@extends('livewire.agreements.migrated.frontend.main_master')

@section('content')

<x-mot-checker-form />


{{--

@include('livewire.agreements.migrated.frontend.ProductCarouselTabBased')


 --}}







{{-- @include('livewire.agreements.migrated.frontend.sparepartHomeSection') --}}

    {{-- <div class="container">
        <div class="text-center ">

            <h2 class="fw-600" style="font-size: 2.2em;margin: 0;padding: 15px 0 10px 0;">Check MOT Status</h2>
        </div>
        @livewire('mot-checker-form')
    </div> --}}



    {{-- Testing install livewire --}}
    {{--
<!-- Hero Section -->
<section class="hero bg-dark text-white text-center py-5">
        <div class="container">
        <!-- {{ url('img/ngn-motor-logo-fit-optimized.png') }} -->
            <img loading="lazy" src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN - Motorcycle Rentals, Repairs, and Motorcycle MOT in London, Catford, and Tooting" class="mb-3" style="width:300px;">
            <h1 class="display-4">Premier Motorcycle Services in London</h1>
            <a href="/new-motorcycles" ><button class="btn ngn-btn btn-lg mt-3"> Browse Motorcycles</button></a>

            <a href="{{ route('rental-hire') }}" ><button class="btn  btn-lg mt-3" style="background:#3d3d3d;"> Book a Test Ride</button></a>
        </div>
    </section>

    <!-- Featured Motorcycles -->
    <section id="motorcycle-sales" class="text-center py-5">
        <div class="container">
            <h2 class="display-4">Featured Motorcycles</h2>
            <div id="motorcycleCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img loading="lazy" src="{{ asset('images/motorcycle1.jpg') }}" class="d-block w-100" alt="Motorcycle 1">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>New Motorcycle Model</h5>
                            <p>Details about the new model.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img loading="lazy" src="{{ asset('images/motorcycle2.jpg') }}" class="d-block w-100" alt="Motorcycle 2">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Used Motorcycle</h5>
                            <p>Details about the used motorcycle.</p>
                        </div>
                    </div>
                    <!-- Add more carousel items as needed -->
                </div>
                <a class="carousel-control-prev" href="#motorcycleCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#motorcycleCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </section>


    @include('livewire.agreements.migrated.frontend.motorbikeRentalHomeSection')


    <!-- Spare Parts Sales -->
    <section id="spare-parts" class="text-center py-5">
        <div class="container">
            <h2 class="display-4">Spare Parts Sales</h2>
            <p class="lead">Genuine spare parts for various motorcycle brands.</p>
            <a href="#spare-parts" class="btn ngn-btn">Shop Now</a>
        </div>
    </section>

    <!-- Shop Products -->
    <section id="shop-products" class="bg-light text-center py-5">
        <div class="container">
            <h2 class="display-4">Shop Products</h2>
            <p class="lead">Accessories, riding gear, and other motorcycle-related products.</p>
            <a href="#shop-products" class="btn ngn-btn">Browse Products</a>
        </div>
    </section>

    <!-- Services Overview -->
    <section id="services" class="text-center py-5">
        <div class="container">
            <h2 class="display-4">Our Services</h2>
            <p class="lead">Repairs, maintenance, MOT bookings, and more.</p>
            <a href="{{ route('services') }}" class="btn ngn-btn">Explore Services</a>
        </div>
    </section> --}}

    {{-- @include('livewire.agreements.migrated.frontend.MOTHomeSection')

    @include('livewire.agreements.migrated.frontend.aboutHomeSection')

    @include('livewire.agreements.migrated.frontend.contactHomeSection')

    @include('livewire.agreements.migrated.frontend.testimonialsHomeSection')


    @include('livewire.agreements.migrated.frontend.body.newsletter') --}}
    {{-- <livewire:motorcycle-list /> --}}

@endsection
